<?php

namespace App\Http\Controllers\ComissionedAgent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComissionedAgent\DeleteComissionedAgentRequest;
use App\Http\Requests\ComissionedAgent\ListComissionedAgentRequest;
use App\Http\Requests\ComissionedAgent\ShowComissionedAgentRequest;
use App\Http\Requests\ComissionedAgent\StoreComissionedAgentRequest;
use App\Http\Requests\ComissionedAgent\UpdateComissionedAgentRequest;
use App\UseCase\ComissionedAgent\CreateComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\DeleteComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\DTO\CreateComissionedAgentInputDto;
use App\UseCase\ComissionedAgent\DTO\UpdateComissionedAgentInputDto;
use App\UseCase\ComissionedAgent\GetComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\ListComissionedAgentsUseCase;
use App\UseCase\ComissionedAgent\ListComissionedforSelect;
use App\UseCase\ComissionedAgent\UpdateComissionedAgentUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ComissionedAgentController extends Controller
{
    public function __construct(
        private ListComissionedAgentsUseCase $listComissionedAgentsUseCase,
        private CreateComissionedAgentUseCase $createComissionedAgentUseCase,
        private GetComissionedAgentUseCase $getComissionedAgentUseCase,
        private UpdateComissionedAgentUseCase $updateComissionedAgentUseCase,
        private DeleteComissionedAgentUseCase $deleteComissionedAgentUseCase,
    ) {}

    /**
     * JSON list for selects (name as label). Same data source as customer create options.
     */
    public function forSelect(Request $request): JsonResponse
    {
        $rows = app(ListComissionedforSelect::class)->execute([]);
        $options = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = $row['id'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }
            $name = '';
            if (isset($row['name'])) {
                $name = (string) $row['name'];
            }
            $label = (string) $id;
            if ($name !== '') {
                $label = $name;
            }
            $options[] = [
                'id' => (string) $id,
                'label' => $label,
            ];
        }

        if (! $request->query->has('q')) {
            return response()->json($options);
        }

        $term = mb_strtolower(trim((string) $request->query('q', '')));
        $limited = [];
        foreach ($options as $option) {
            if ($term !== '' && ! str_contains(mb_strtolower($option['label']), $term)) {
                continue;
            }
            $limited[] = $option;
            if (count($limited) >= 15) {
                break;
            }
        }

        return response()->json($limited);
    }

    public function index(ListComissionedAgentRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listComissionedAgentsUseCase->execute($data);

        return Inertia::render('ComissionedAgent/Index', [
            'comissionedAgents'          => $output,
            'canManageComissionedAgents' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('ComissionedAgent/Create', [
            'canManageComissionedAgents' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function store(StoreComissionedAgentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateComissionedAgentInputDto(
            name: (string) $data['name'],
            defaultComission: (float) $data['default_comission'],
            paymentDays: (int) $data['payment_days'],
            observations: $data['observations'] ?? null,
            createdBy: $request->user()?->id,
        );

        $output = $this->createComissionedAgentUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Comissionado cadastrado com sucesso!');

            return to_route('comissioned-agents.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o comissionado.');

        return back();
    }

    public function show(ShowComissionedAgentRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getComissionedAgentUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('ComissionedAgent/Show', [
            'comissionedAgent' => $output,
            'canManageComissionedAgents' => $request->user()?->hasAnyRole(['admin','commercial','production','finance']) ?? false,
        ]);
    }

    public function edit(ShowComissionedAgentRequest $request): Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getComissionedAgentUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        return Inertia::render('ComissionedAgent/Edit', [
            'comissionedAgent' => $output,
            'canManageComissionedAgents' => $request->user()?->hasAnyRole(['admin','commercial']) ?? false,
        ]);
    }

    public function update(UpdateComissionedAgentRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateComissionedAgentInputDto(
            id: $id,
            name: (string) $data['name'],
            defaultComission: (float) $data['default_comission'],
            paymentDays: (int) $data['payment_days'],
            observations: $data['observations'] ?? null,
            updatedBy: $request->user()?->id,
        );

        $output = $this->updateComissionedAgentUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Comissionado atualizado com sucesso!');
            return to_route('comissioned-agents.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o comissionado.');
        return to_route('comissioned-agents.index');
    }

    public function destroy(DeleteComissionedAgentRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteComissionedAgentUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Comissionado excluído com sucesso!');
            return to_route('comissioned-agents.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o comissionado.');
        return to_route('comissioned-agents.index');
    }
}
