<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\DeleteCustomerRequest;
use App\Http\Requests\Customer\ListCustomerRequest;
use App\Http\Requests\Customer\ShowCustomerRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerFinancialRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\CustomerEntryService;
use App\UseCase\ComissionedAgent\ListComissionedforSelect;
use App\UseCase\Customer\CreateCustomerUseCase;
use App\UseCase\Customer\DeleteCustomerUseCase;
use App\UseCase\Customer\DTO\CreateCustomerInputDto;
use App\UseCase\Customer\GetCustomerUseCase;
use App\UseCase\Customer\ListCustomersForSelectUseCase;
use App\UseCase\Customer\ListCustomersUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CustomerController extends Controller
{
    public function __construct(
        private ListCustomersUseCase $listCustomersUseCase,
        private ListCustomersForSelectUseCase $listCustomersForSelectUseCase,
        private CreateCustomerUseCase $createCustomerUseCase,
        private GetCustomerUseCase $getCustomerUseCase,
        private DeleteCustomerUseCase $deleteCustomerUseCase,
        private ListComissionedforSelect $listComissionedforSelectUseCase,
        private CustomerEntryService $customerEntryService
    ) {}

    public function forSelect(Request $request): JsonResponse
    {
        $term = mb_strtolower(trim((string) $request->query('q', '')));
        $rows = $this->listCustomersForSelectUseCase->execute();
        $options = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = $row['id'] ?? null;
            if (! is_string($id) || $id === '') {
                continue;
            }

            $name = '';
            if (isset($row['company_name']) && is_string($row['company_name'])) {
                $name = $row['company_name'];
            }

            if ($term !== '' && ! str_contains(mb_strtolower($name), $term)) {
                continue;
            }

            $label = $id;
            if ($name !== '') {
                $label = $name;
            }

            $options[] = [
                'id' => $id,
                'label' => $label,
            ];

            if (count($options) >= 15) {
                break;
            }
        }

        return response()->json($options);
    }

    public function index(ListCustomerRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listCustomersUseCase->execute($data);

        return Inertia::render('Customer/Index', [
            'customers' => $output,
            'canManageCustomers' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Customer/Create', [
            'comissionedAgentOptions' => $this->comissionedAgentOptions(),
            'canManageCustomers' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateCustomerInputDto(
            companyName: (string) $data['company_name'],
            legalName: (string) $data['legal_name'],
            document: (string) $data['document'],
            codeCustomer: (string) $data['code_customer'],
            active: (bool) ($data['active'] ?? true),
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: $request->user()?->id,
            updatedBy: $request->user()?->id,
            comissionedAgentId: $this->customerEntryService->nullableString($data['comissioned_agent_id'] ?? null),
            financialInfo: null,
            contacts: [],
        );

        $output = $this->createCustomerUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Cliente cadastrado com sucesso!');
            return to_route('customers.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o cliente.');
        return back();
    }

    public function show(ShowCustomerRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getCustomerUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('Customer/Show', [
            'customer' => $output,
            'comissionedAgentOptions' => $this->comissionedAgentOptions(),
            'canManageCustomers' => $request->user()?->hasAnyRole(['admin', 'commercial', 'production', 'finance']) ?? false,
        ]);
    }

    public function update(UpdateCustomerRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();
        $output = $this->customerEntryService->toUpdateCustomer($data, $request, $id);

        if (null === $output) abort(404);

        if ($output) {
            Inertia::flash('message', 'Cliente atualizado com sucesso!');
            return to_route('customers.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o cliente.');
        return to_route('customers.index');
    }

    public function updateFinancial(UpdateCustomerFinancialRequest $request, string $id): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $output = $this->customerEntryService->toUpdateFinancialInfo($data, $request, $id);

        if (! $output) {
            Inertia::flash('error', 'Não foi possível atualizar as informações financeiras.');

            return match (true) {
                $request->wantsJson() => response()->json(['message' => 'Não foi possível atualizar as informações financeiras.'], 422),
                default => back(),
            };
        }

        Inertia::flash('message', 'Informações financeiras salvas com sucesso!');

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return back();
    }

    public function destroy(DeleteCustomerRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteCustomerUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Cliente excluído com sucesso!');
            return to_route('customers.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o cliente.');
        return to_route('customers.index');
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    private function comissionedAgentOptions(): array
    {
        return $this->listComissionedforSelectUseCase->execute([]);
    }


}
