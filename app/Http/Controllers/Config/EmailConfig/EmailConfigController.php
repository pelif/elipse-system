<?php

namespace App\Http\Controllers\Config\EmailConfig;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailConfig\StoreEmailConfigRequest;
use App\Http\Requests\EmailConfig\UpdateEmailConfigRequest;
use App\Models\Department\Department;
use App\UseCase\EmailConfig\CreateEmailConfigUseCase;
use App\UseCase\EmailConfig\DTO\CreateEmailConfigInputDto;
use App\UseCase\EmailConfig\DTO\UpdateEmailConfigInputDto;
use App\UseCase\EmailConfig\DeleteEmailConfigUseCase;
use App\UseCase\EmailConfig\GetEmailConfigUseCase;
use App\UseCase\EmailConfig\ListEmailConfigsUseCase;
use App\UseCase\EmailConfig\UpdateEmailConfigUseCase;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EmailConfigController extends Controller
{
    public function __construct(
        private ListEmailConfigsUseCase $listEmailConfigsUseCase,
        private CreateEmailConfigUseCase $createEmailConfigUseCase,
        private GetEmailConfigUseCase $getEmailConfigUseCase,
        private UpdateEmailConfigUseCase $updateEmailConfigUseCase,
        private DeleteEmailConfigUseCase $deleteEmailConfigUseCase,
    ) {}

    public function index(): Response
    {
        $output = $this->listEmailConfigsUseCase->execute(request()->all());

        return Inertia::render('EmailConfig/Index', [
            'emailConfigs' => $output,
            'departments' => Department::query()->orderBy('department')->get(['id', 'department']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('EmailConfig/Create', [
            'departments' => Department::query()
                ->select('id', 'department')
                ->orderBy('department')
                ->get(),
        ]);
    }

    public function store(StoreEmailConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new CreateEmailConfigInputDto(
            address: $data['address'],
            departmentId: $data['department_id'],
        );

        $output = $this->createEmailConfigUseCase->execute($input);

        if (!empty($output->id)) {
            Inertia::flash('message', 'Configuração de E-mail criada com sucesso!');
            return to_route('email-config.index');
        }

        return Inertia::flash('error', 'Não foi possível criar a Configuração de E-mail.')->back();
    }

    public function show(string $id): JsonResponse
    {
        $output = $this->getEmailConfigUseCase->execute($id);

        return response()->json($output);
    }

    public function edit(string $id): Response
    {
        $emailConfig = $this->getEmailConfigUseCase->execute($id);

        return Inertia::render('EmailConfig/Edit', [
            'emailConfig' => $emailConfig,
            'departments' => Department::query()
                            ->select('id', 'department')
                            ->orderBy('department')
                            ->get(),
        ]);
    }

    public function update(UpdateEmailConfigRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $input = new UpdateEmailConfigInputDto(
            id: $id,
            address: $data['address'],
            departmentId: $data['department_id'],
        );

        $output = $this->updateEmailConfigUseCase->execute($input);

        if (!empty($output->id)) {
            Inertia::flash('message', 'Configuração de E-mail atualizada com sucesso!');
            return to_route('email-config.index');
        }

        return Inertia::flash('error', 'Não foi possível atualizar a Configuração de E-mail.')->back();
    }

    public function destroy(string $id): RedirectResponse
    {
        $deleted = $this->deleteEmailConfigUseCase->execute($id);

        if ($deleted) {
            return Inertia::flash('message', 'Configuração de E-mail excluída com sucesso!')->back();
        }

        return Inertia::flash('error', 'Não foi possível excluir a Configuração de E-mail.')->back();
    }
}

