<?php

namespace App\Http\Controllers\ServiceUnity;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceUnity\DeleteRequest;
use App\Http\Requests\ServiceUnity\ListRequest;
use App\Http\Requests\ServiceUnity\ShowRequest;
use App\Http\Requests\ServiceUnity\StoreRequest;
use App\Http\Requests\ServiceUnity\UpdateRequest;
use App\UseCase\ServiceUnity\CreateServiceUnity;
use App\UseCase\ServiceUnity\DeleteServiceUnity;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use App\UseCase\ServiceUnity\GetServiceUnity;
use App\UseCase\ServiceUnity\ListServiceUnity;
use App\UseCase\ServiceUnity\UpdateServiceUnity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ServiceUnityController extends Controller
{
    public function __construct(
        private ListServiceUnity $listUseCase,
        private CreateServiceUnity $createUseCase,
        private GetServiceUnity $getUseCase,
        private UpdateServiceUnity $updateUseCase,
        private DeleteServiceUnity $delteUseCase
    ) {}

    public function index(ListRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listUseCase->execute($data);

        return Inertia::render('ServiceUnity/Index', [
            'serviceUnits' => $output,
            'canManageServiceUnits' => $request->user()?->hasAnyRole(['admin','commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('ServiceUnity/Create', [
            'canManageServiceUnity' => $request->user()?->hasAnyRole(['admin','commercial']) ?? false
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new ServiceUnityInputDto(
            id: null,
            unity: (string) $data['unity'],
            createdBy: (int) $request->user()?->id,
            updatedBy: null,
            createdAt: now()->format('Y-m-d H:i:s'),
            updatedAt: null
        );

        $output = $this->createUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Unidade de serviço cadastrada com sucesso!');
            return to_route('unity-service.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar a unidade de serviço.');
        return back();
    }

    public function show(ShowRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getUseCase->execute($id);

        if (!$output) abort(404);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($output);
        }

        return Inertia::render('ServiceUnity/Show', [
            'serviceUnity' => $output,
            'canManageServiceUnity' => $request->user()?->hasAnyRole(['admin', 'commercial', 'production', 'finance']) ?? false,
        ]);
    }

    public function update(UpdateRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new ServiceUnityInputDto(
            id: $id,
            unity: $data['unity'],
            updatedBy: $request->user()?->id,
            updatedAt: now()?->format('Y-m-d H:i:s')
        );

        $output = $this->updateUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Unidade de Serviço atualizada com sucesso!');
            return to_route('unity-service.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar a unidade de serviço');
        return to_route('unity-service.index');
    }

    public function destroy(DeleteRequest $request, string $id): RedirectResponse
    {
        $request->validated();
        $deleted = $this->delteUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Unidade de Serviço excluído com sucesso!');
            return to_route('unity-service.index');
        }

        Inertia::flash('error', 'Náo foi possível excluir a unidade de serviço.');
        return to_route('unity-service.index');
    }
}
