<?php

namespace App\Http\Controllers\Config\JobFlow;

use App\Http\Controllers\Controller;
use App\Http\Requests\Status\DeleteJobStatusRequest;
use App\Http\Requests\Status\ListJobStatusRequest;
use App\Http\Requests\Status\StoreJobStatusRequest;
use App\Http\Requests\Status\UpdateJobStatusRequest;
use App\UseCase\Status\CreateJobStatusUseCase;
use App\UseCase\Status\DTO\CreateJobStatusInputDto;
use App\UseCase\Status\DTO\UpdateJobStatusInputDto;
use App\UseCase\Status\DeleteJobStatusUseCase;
use App\UseCase\Status\GetJobStatusUseCase;
use App\UseCase\Status\ListJobStatusUseCase;
use App\UseCase\Status\UpdateJobStatusUseCase;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class JobStatusController extends Controller
{
    public function __construct(
        private ListJobStatusUseCase $listJobStatusUseCase,
        private CreateJobStatusUseCase $createJobStatusUseCase,
        private GetJobStatusUseCase $getJobStatusUseCase,
        private UpdateJobStatusUseCase $updateJobStatusUseCase,
        private DeleteJobStatusUseCase $deleteJobStatusUseCase
    ) {}

    public function index(ListJobStatusRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listJobStatusUseCase->execute($data);

        return Inertia::render('JobFlow/Index', [
            'statuses' => $output,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('JobFlow/Create');
    }

    public function store(StoreJobStatusRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateJobStatusInputDto(
            status: $data['status'],
            observacoes: $data['observacoes'] ?? null
        );

        $output = $this->createJobStatusUseCase->execute($input);

        if (!empty($output->id)) {
            Inertia::flash('message', 'Status de Job inserido com sucesso!');
            return to_route('job-flow.index');
        }

        return Inertia::flash('error', 'Não foi possível inserir o Status de Job')->back();
    }

    public function show(Request $request, string $id): HttpResponse|Response
    {
        $output = $this->getJobStatusUseCase->execute($id);

        if (!$output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('JobFlow/Show', [
            'status' => $output,
        ]);
    }

    public function edit(string $id): Response
    {
        $output = $this->getJobStatusUseCase->execute($id);

        if (!$output) {
            abort(404);
        }

        return Inertia::render('JobFlow/Edit', [
            'status' => $output,
        ]);
    }

    public function update(UpdateJobStatusRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateJobStatusInputDto(
            id: $id,
            status: $data['status'],
            observacoes: $data['observacoes'] ?? null
        );

        $output = $this->updateJobStatusUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Status de Job atualizado com sucesso!');
            return to_route('job-flow.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o Status de Job.');
        return to_route('job-flow.index');
    }

    public function delete(string $id): Response
    {
        $output = $this->getJobStatusUseCase->execute($id);

        if (!$output) {
            abort(404);
        }

        return Inertia::render('JobFlow/Delete', [
            'status' => $output,
        ]);
    }

    public function destroy(DeleteJobStatusRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteJobStatusUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Status de Job excluído com sucesso!');
            return to_route('job-flow.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o Status de Job.');
        return to_route('job-flow.index');
    }
}

