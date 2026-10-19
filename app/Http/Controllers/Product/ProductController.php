<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\DeleteProductRequest;
use App\Http\Requests\Product\ListProductRequest;
use App\Http\Requests\Product\ShowProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\UseCase\Product\CreateProductUseCase;
use App\UseCase\Product\DeleteProductUseCase;
use App\UseCase\Product\DTO\CreateProductInputDto;
use App\UseCase\Product\DTO\UpdateProductInputDto;
use App\UseCase\Product\GetProductUseCase;
use App\UseCase\Product\ListProductsUseCase;
use App\UseCase\Product\UpdateProductUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProductController extends Controller
{
    public function __construct(
        private ListProductsUseCase $listProductsUseCase,
        private CreateProductUseCase $createProductUseCase,
        private GetProductUseCase $getProductUseCase,
        private UpdateProductUseCase $updateProductUseCase,
        private DeleteProductUseCase $deleteProductUseCase
    ) {}

    public function forSelect(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));
        $filters = [
            'sortField' => 'name',
            'sortDirection' => 'asc',
        ];

        if ($term !== '') {
            $filters['name'] = $term;
        }

        $result = $this->listProductsUseCase->execute($filters);
        $options = [];

        foreach ($result['data'] as $product) {
            $options[] = [
                'id' => $product->id,
                'label' => $product->name,
            ];
        }

        return response()->json($options);
    }

    public function index(ListProductRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listProductsUseCase->execute($data);

        return Inertia::render('Product/Index', [
            'products' => $output,
            'canManageProducts' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Product/Create', [
            'canManageProducts' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateProductInputDto(
            name: $data['name'],
            description: self::normalizeOptionalDescription($data['description'] ?? null)
        );

        $output = $this->createProductUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Produto cadastrado com sucesso!');
            return to_route('products.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o produto.');
        return back();
    }

    public function show(ShowProductRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getProductUseCase->execute($id);

        if (! $output) abort(404);

        if ($request->wantsJson())
            return response()->json($output);

        return Inertia::render('Product/Show', [
            'product' => $output,
            'canManageProducts' => $request->user()?->hasAnyRole(['admin', 'commercial', 'production', 'finance']) ?? false,
        ]);
    }

    public function update(UpdateProductRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateProductInputDto(
            id: $id,
            name: $data['name'],
            description: self::normalizeOptionalDescription($data['description'] ?? null)
        );

        $output = $this->updateProductUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Produto atualizado com sucesso!');
            return to_route('products.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o produto.');
        return to_route('products.index');
    }

    public function destroy(DeleteProductRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteProductUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Produto excluído com sucesso!');
            return to_route('products.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o produto.');
        return to_route('products.index');
    }

    private static function normalizeOptionalDescription(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
