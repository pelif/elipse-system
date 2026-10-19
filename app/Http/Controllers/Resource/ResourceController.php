<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\DeleteResourceRequest;
use App\Http\Requests\Resource\ListResourceRequest;
use App\Http\Requests\Resource\ShowResourceRequest;
use App\Http\Requests\Resource\StoreResourceRequest;
use App\Http\Requests\Resource\UpdateResourceRequest;
use App\Models\Department\Department;
use App\Services\Resources\ResourcesEntryService;
use App\UseCase\Resource\CreateResourceUseCase;
use App\UseCase\Resource\DeleteResourceUseCase;
use App\UseCase\Resource\DTO\CreateResourceInputDto;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;
use App\UseCase\Resource\GetResourceUseCase;
use App\UseCase\Resource\ListResourcesUseCase;
use App\UseCase\Resource\UpdateResourceUseCase;
use App\UseCase\ResourceCategory\ListResourceCategoriesUseCase;
use App\UseCase\ResourceCategory\ListResourceCategoryForSelect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResourceController extends Controller
{
    public function __construct(
        private ListResourcesUseCase $listResourcesUseCase,
        private CreateResourceUseCase $createResourceUseCase,
        private GetResourceUseCase $getResourceUseCase,
        private UpdateResourceUseCase $updateResourceUseCase,
        private DeleteResourceUseCase $deleteResourceUseCase,
        private ListResourceCategoriesUseCase $listResourceCategoriesUseCase,
        private ListResourceCategoryForSelect $listResourceCategoryForSelect,
        private ResourcesEntryService $resourcesEntryService
    ) {}

    public function index(ListResourceRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listResourcesUseCase->execute($data);

        return Inertia::render('Resource/Index', [
            'resources' => $output,
            'canManageResources' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
            'categorySelectOptions' => $this->listResourceCategoryForSelect->execute(),
            'departmentSelectOptions' => $this->resourcesEntryService->departmentSelectOptions(),
        ]);
    }

    public function create(Request $request): Response
    {
        $categorySelectOptions = $this->listResourceCategoryForSelect->execute();
        $departmentSelectOptions = $this->resourcesEntryService->departmentSelectOptions();

        return Inertia::render('Resource/Create', [
            'canManageResources' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
            'categorySelectOptions' => $categorySelectOptions,
            'departmentSelectOptions' => $departmentSelectOptions,
        ]);
    }

    public function store(StoreResourceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateResourceInputDto(
            categoryId: (string) $data['category_id'],
            departmentId: (string) $data['department_id'],
            resourceName: (string) $data['resource_name'],
            patrimonialCode: (string) $data['patrimonial_code'],
            manufacturer: $this->resourcesEntryService->nullableTrimmedString($data['manufacturer'] ?? null),
            model: $this->resourcesEntryService->nullableTrimmedString($data['model'] ?? null),
            serialNumber: $this->resourcesEntryService->nullableTrimmedString($data['serial_number'] ?? null),
            responsibleId: $this->resourcesEntryService->nullablePositiveInt($data['responsible_id'] ?? null),
            notes: $this->resourcesEntryService->nullableTrimmedString($data['notes'] ?? null),
            documentationLink: $this->resourcesEntryService->nullableTrimmedString($data['documentation_link'] ?? null),
            createdBy: $request->user()?->id,
            updatedBy: $request->user()?->id,
        );

        $output = $this->createResourceUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Recurso cadastrado com sucesso!');
            return to_route('resources.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o recurso.');
        return back();
    }

    public function show(ShowResourceRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getResourceUseCase->execute($id);

        if (! $output) abort(404);

        if ($request->wantsJson()) return response()->json($output);

        return Inertia::render('Resource/Show', [
            'resource' => $output,
            'canManageResources' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
            'categorySelectOptions' => $this->listResourceCategoryForSelect->execute(),
            'departmentSelectOptions' => $this->resourcesEntryService->departmentSelectOptions(),
        ]);
    }

    public function update(UpdateResourceRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateResourceInputDto(
            id: $id,
            categoryId: (string) $data['category_id'],
            departmentId: (string) $data['department_id'],
            resourceName: (string) $data['resource_name'],
            patrimonialCode: (string) $data['patrimonial_code'],
            manufacturer: $this->resourcesEntryService->nullableTrimmedString($data['manufacturer'] ?? null),
            model: $this->resourcesEntryService->nullableTrimmedString($data['model'] ?? null),
            serialNumber: $this->resourcesEntryService->nullableTrimmedString($data['serial_number'] ?? null),
            responsibleId: $this->resourcesEntryService->nullablePositiveInt($data['responsible_id'] ?? null),
            notes: $this->resourcesEntryService->nullableTrimmedString($data['notes'] ?? null),
            documentationLink: $this->resourcesEntryService->nullableTrimmedString($data['documentation_link'] ?? null),
            updatedBy: $request->user()?->id,
        );

        $output = $this->updateResourceUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Recurso atualizado com sucesso!');
            return to_route('resources.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o recurso.');
        return to_route('resources.index');
    }

    public function destroy(DeleteResourceRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteResourceUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Recurso excluído com sucesso!');
            return to_route('resources.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o recurso.');
        return to_route('resources.index');
    }

}
