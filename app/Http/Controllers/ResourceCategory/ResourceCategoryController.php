<?php

namespace App\Http\Controllers\ResourceCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceCategory\DeleteResourceCategoryRequest;
use App\Http\Requests\ResourceCategory\ListResourceCategoryRequest;
use App\Http\Requests\ResourceCategory\ShowResourceCategoryRequest;
use App\Http\Requests\ResourceCategory\StoreResourceCategoryRequest;
use App\Http\Requests\ResourceCategory\UpdateResourceCategoryRequest;
use App\UseCase\ResourceCategory\CreateResourceCategoryUseCase;
use App\UseCase\ResourceCategory\DeleteResourceCategoryUseCase;
use App\UseCase\ResourceCategory\DTO\CreateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\DTO\UpdateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\GetResourceCategoryUseCase;
use App\UseCase\ResourceCategory\ListResourceCategoriesUseCase;
use App\UseCase\ResourceCategory\UpdateResourceCategoryUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResourceCategoryController extends Controller
{
    public function __construct(
        private ListResourceCategoriesUseCase $listResourceCategoriesUseCase,
        private CreateResourceCategoryUseCase $createResourceCategoryUseCase,
        private GetResourceCategoryUseCase $getResourceCategoryUseCase,
        private UpdateResourceCategoryUseCase $updateResourceCategoryUseCase,
        private DeleteResourceCategoryUseCase $deleteResourceCategoryUseCase
    ) {}

    public function index(ListResourceCategoryRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listResourceCategoriesUseCase->execute($data);

        return Inertia::render('ResourceCategory/Index', [
            'resourceCategories' => $output,
            'canManageResourceCategories' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('ResourceCategory/Create', [
            'canManageResourceCategories' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreResourceCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateResourceCategoryInputDto(
            name: (string) $data['name'],
            description: self::nullableTrimmedString($data['description'] ?? null),
        );

        $output = $this->createResourceCategoryUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Categoria cadastrada com sucesso!');
            return to_route('resource-categories.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar a categoria.');
        return back();
    }

    public function show(ShowResourceCategoryRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getResourceCategoryUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('ResourceCategory/Show', [
            'resourceCategory' => $output,
            'canManageResourceCategories' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function update(UpdateResourceCategoryRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateResourceCategoryInputDto(
            id: $id,
            name: (string) $data['name'],
            description: self::nullableTrimmedString($data['description'] ?? null),
        );

        $output = $this->updateResourceCategoryUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Categoria atualizada com sucesso!');
            return to_route('resource-categories.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar a categoria.');
        return to_route('resource-categories.index');
    }

    public function destroy(DeleteResourceCategoryRequest $request, string $id): RedirectResponse
    {
        $request->validated();
        $deleted = $this->deleteResourceCategoryUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Categoria excluída com sucesso!');
            return to_route('resource-categories.index');
        }

        Inertia::flash('error', 'Não foi possível excluir a categoria.');
        return to_route('resource-categories.index');
    }

    private static function nullableTrimmedString(mixed $value): ?string
    {
        if (! is_string($value)) return null;

        $trimmed = trim($value);

        if ($trimmed === '') return null;

        return $trimmed;
    }
}
