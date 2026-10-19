<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\DeleteContractorRequest;
use App\Http\Requests\Contractor\ListContractorRequest;
use App\Http\Requests\Contractor\ShowContractorRequest;
use App\Http\Requests\Contractor\StoreContractorRequest;
use App\Http\Requests\Contractor\UpdateContractorRequest;
use App\UseCase\Contractor\CreateContractorUseCase;
use App\UseCase\Contractor\DeleteContractorUseCase;
use App\UseCase\Contractor\DTO\CreateContractorInputDto;
use App\UseCase\Contractor\DTO\UpdateContractorInputDto;
use App\UseCase\Contractor\GetContractorUseCase;
use App\UseCase\Contractor\ListContractorsUseCase;
use App\UseCase\Contractor\UpdateContractorUseCase;
use App\UseCase\Customer\ListCustomersForSelectUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ContractorController extends Controller
{
    public function __construct(
        private ListContractorsUseCase $listContractorsUseCase,
        private ListCustomersForSelectUseCase $listCustomersForSelectUseCase,
        private CreateContractorUseCase $createContractorUseCase,
        private GetContractorUseCase $getContractorUseCase,
        private UpdateContractorUseCase $updateContractorUseCase,
        private DeleteContractorUseCase $deleteContractorUseCase
    ) {}

    public function forSelect(Request $request): JsonResponse
    {
        $customerId = trim((string) $request->query('customer_id', ''));
        if ($customerId === '') {
            return response()->json([]);
        }

        $term = trim((string) $request->query('q', ''));
        $filters = [
            'customer_id' => $customerId,
            'sortField' => 'name',
            'sortDirection' => 'asc',
        ];

        if ($term !== '') {
            $filters['name'] = $term;
        }

        $result = $this->listContractorsUseCase->execute($filters);
        $options = [];

        foreach ($result['data'] as $contractor) {
            $options[] = [
                'id' => $contractor->id,
                'label' => $contractor->name,
            ];
        }

        return response()->json($options);
    }

    public function index(ListContractorRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listContractorsUseCase->execute($data);
        $customers = $this->listCustomersForSelectUseCase->execute();

        return Inertia::render('Contractor/Index', [
            'contractors' => $output,
            'customers' => $customers,
            'canManageContractors' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        $customers = $this->listCustomersForSelectUseCase->execute();

        return Inertia::render('Contractor/Create', [
            'customers' => $customers,
            'canManageContractors' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreContractorRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        $input = new CreateContractorInputDto(
            name: (string) $data['name'],
            phone: (string) $data['phone'],
            email: (string) $data['email'],
            customerId: (string) $data['customer_id'],
            communicationCare: self::nullableTrimmedString($data['communication_care'] ?? null),
            createdBy: $request->user()?->id,
            updatedBy: $request->user()?->id,
        );

        $output = $this->createContractorUseCase->execute($input);

        if (! empty($output->id)) {
            if ($request->wantsJson()) {
                return response()->json($output);
            }

            Inertia::flash('message', 'Contratante cadastrado com sucesso!');
            return to_route('contractors.index');
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Não foi possível cadastrar o contratante.'], 422);
        }

        Inertia::flash('error', 'Não foi possível cadastrar o contratante.');
        return back();
    }

    public function show(ShowContractorRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getContractorUseCase->execute($id);

        if (! $output) abort(404);

        if ($request->wantsJson())
            return response()->json($output);

        return Inertia::render('Contractor/Show', [
            'contractor' => $output,
            'canManageContractors' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function update(UpdateContractorRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateContractorInputDto(
            id: $id,
            name: (string) $data['name'],
            phone: (string) $data['phone'],
            email: (string) $data['email'],
            customerId: (string) $data['customer_id'],
            communicationCare: self::nullableTrimmedString($data['communication_care'] ?? null),
            createdBy: null,
            updatedBy: $request->user()?->id,
        );

        $output = $this->updateContractorUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Contratante atualizado com sucesso!');
            return to_route('contractors.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o contratante.');
        return to_route('contractors.index');
    }

    public function destroy(DeleteContractorRequest $request, string $id): RedirectResponse
    {
        $request->validated();
        $deleted = $this->deleteContractorUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Contratante excluído com sucesso!');
            return to_route('contractors.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o contratante.');
        return to_route('contractors.index');
    }

    private static function nullableTrimmedString(mixed $value): ?string
    {
        if (! is_string($value)) return null;

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
