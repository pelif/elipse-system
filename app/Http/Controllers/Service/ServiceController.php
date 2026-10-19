<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\DeleteServiceRequest;
use App\Http\Requests\Service\ListServiceRequest;
use App\Http\Requests\Service\ShowServiceRequest;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DeleteServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\DTO\UpdateServiceInputDto;
use App\UseCase\Service\GetServiceUseCase;
use App\UseCase\Service\ListServicesUseCase;
use App\UseCase\Service\UpdateServiceUseCase;
use App\UseCase\ServiceUnity\ListServiceUnityForSelectUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ServiceController extends Controller
{
    public function __construct(
        private ListServicesUseCase $listServicesUseCase,
        private CreateServiceUseCase $createServiceUseCase,
        private GetServiceUseCase $getServiceUseCase,
        private UpdateServiceUseCase $updateServiceUseCase,
        private DeleteServiceUseCase $deleteServiceUseCase,
        private ListServiceUnityForSelectUseCase $listServiceUnityForSelectUseCase
    ) {}

    public function index(ListServiceRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listServicesUseCase->execute($data);

        return Inertia::render('Service/Index', [
            'services'                  => $output,
            'canManageServices'         => $request->user()?->hasAnyRole(['admin', 'commercial', 'production']) ?? false,
            'serviceUnitySelectOptions' => $this->listServiceUnityForSelectUseCase->execute(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Service/Create', [
            'canManageServices'         => $request->user()?->hasAnyRole(['admin', 'commercial', 'production']) ?? false,
            'serviceUnitySelectOptions' => $this->listServiceUnityForSelectUseCase->execute(),
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateServiceInputDto(
            service:     $data['service'],
            unityId:     (string) $data['unity_id'],
            description: self::normalizeOptionalString($data['description'] ?? null),
            createdBy:   $request->user()?->id ? (int) $request->user()->id : null
        );

        $output = $this->createServiceUseCase->execute($input);

        if (!empty($output->id)) {
            Inertia::flash('message', 'Serviço cadastrado com sucesso!');
            return to_route('services.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o serviço.');
        return back();
    }

    public function show(ShowServiceRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getServiceUseCase->execute($id);

        if (!$output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('Service/Show', [
            'service'           => $output,
            'canManageServices' => $request->user()?->hasAnyRole(['admin', 'commercial', 'production']) ?? false,
        ]);
    }

    public function update(UpdateServiceRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateServiceInputDto(
            id:          $id,
            service:     $data['service'],
            unityId:     (string) $data['unity_id'],
            description: self::normalizeOptionalString($data['description'] ?? null),
            updatedBy:   $request->user()?->id ? (int) $request->user()->id : null
        );

        $output = $this->updateServiceUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Serviço atualizado com sucesso!');
            return to_route('services.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o serviço.');
        return to_route('services.index');
    }

    public function destroy(DeleteServiceRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteServiceUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Serviço excluído com sucesso!');
            return to_route('services.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o serviço.');
        return to_route('services.index');
    }

    private static function normalizeOptionalString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
