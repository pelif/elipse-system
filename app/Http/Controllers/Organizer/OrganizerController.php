<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\DeleteOrganizerRequest;
use App\Http\Requests\Organizer\ListOrganizerRequest;
use App\Http\Requests\Organizer\ShowOrganizerRequest;
use App\Http\Requests\Organizer\StoreOrganizerRequest;
use App\Http\Requests\Organizer\UpdateOrganizerRequest;
use App\UseCase\Customer\ListCustomersForSelectUseCase;
use App\UseCase\Organizer\CreateOrganizerUseCase;
use App\UseCase\Organizer\DeleteOrganizerUseCase;
use App\UseCase\Organizer\DTO\CreateOrganizerInputDto;
use App\UseCase\Organizer\DTO\UpdateOrganizerInputDto;
use App\UseCase\Organizer\GetOrganizerUseCase;
use App\UseCase\Organizer\ListOrganizersUseCase;
use App\UseCase\Organizer\UpdateOrganizerUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrganizerController extends Controller
{
    public function __construct(
        private ListOrganizersUseCase $listOrganizersUseCase,
        private ListCustomersForSelectUseCase $listCustomersForSelectUseCase,
        private CreateOrganizerUseCase $createOrganizerUseCase,
        private GetOrganizerUseCase $getOrganizerUseCase,
        private UpdateOrganizerUseCase $updateOrganizerUseCase,
        private DeleteOrganizerUseCase $deleteOrganizerUseCase
    ) {}

    public function index(ListOrganizerRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listOrganizersUseCase->execute($data);
        $customers = $this->listCustomersForSelectUseCase->execute();

        return Inertia::render('Organizer/Index', [
            'organizers' => $output,
            'customers' => $customers,
            'canManageOrganizers' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        $customers = $this->listCustomersForSelectUseCase->execute();

        return Inertia::render('Organizer/Create', [
            'customers' => $customers,
            'canManageOrganizers' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreOrganizerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateOrganizerInputDto(
            name: (string) $data['name'],
            phone: (string) $data['phone'],
            email: (string) $data['email'],
            customerId: (string) $data['customer_id'],
            communicationCare: self::nullableTrimmedString($data['communication_care'] ?? null),
            createdBy: $request->user()?->id,
            updatedBy: $request->user()?->id,
        );

        $output = $this->createOrganizerUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Organizador cadastrado com sucesso!');

            return to_route('organizers.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o organizador.');

        return back();
    }

    public function show(ShowOrganizerRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getOrganizerUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('Organizer/Show', [
            'organizer' => $output,
            'canManageOrganizers' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function update(UpdateOrganizerRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateOrganizerInputDto(
            id: $id,
            name: (string) $data['name'],
            phone: (string) $data['phone'],
            email: (string) $data['email'],
            customerId: (string) $data['customer_id'],
            communicationCare: self::nullableTrimmedString($data['communication_care'] ?? null),
            createdBy: null,
            updatedBy: $request->user()?->id,
        );

        $output = $this->updateOrganizerUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Organizador atualizado com sucesso!');

            return to_route('organizers.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o organizador.');

        return to_route('organizers.index');
    }

    public function destroy(DeleteOrganizerRequest $request, string $id): RedirectResponse
    {
        $request->validated();
        $deleted = $this->deleteOrganizerUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Organizador excluído com sucesso!');

            return to_route('organizers.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o organizador.');

        return to_route('organizers.index');
    }

    private static function nullableTrimmedString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        return $trimmed;
    }
}
