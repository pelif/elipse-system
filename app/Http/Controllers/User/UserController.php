<?php

namespace App\Http\Controllers\User;

use App\Domain\User\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePasswordRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\User\IdUserRequest;
use App\Http\Requests\User\ListUsersRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\StoreNewPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\UseCase\User\CreateUserUseCase;
use App\UseCase\User\UpdateUserUseCase;
use App\UseCase\User\DeleteUserUseCase;
use App\UseCase\User\GetUserUseCase;
use App\UseCase\User\ListUserUseCase;
use App\UseCase\User\DTO\UserInputDto;
use App\UseCase\User\EditPasswordUseCase;
use App\UseCase\User\NewPasswordUseCase;
use App\UseCase\User\StoreNewPasswordUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use PHPUnit\Framework\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    public function __construct(
        private CreateUserUseCase $createUserUseCase,
        private UpdateUserUseCase $updateUserUseCase,
        private DeleteUserUseCase $deleteUserUseCase,
        private GetUserUseCase $getUserUseCase,
        private ListUserUseCase $listUserUseCase,
        private NewPasswordUseCase $newPasswordUseCase,
        private EditPasswordUseCase $editPasswordUseCase,
        private StoreNewPasswordUseCase $storeNewPasswordUseCase
    ) {}

    public function index(ListUsersRequest $request): Response
    {
        $output = $this->listUserUseCase->execute($request->validated());

        return Inertia::render('User/Index', [
            'users' => $output,
        ]);
    }

    public function show(ShowUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $output = $this->getUserUseCase->execute($data['id']);
        return response()->json($output);
    }

    public function edit(IdUserRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->getUserUseCase->execute($data['id']);

        return Inertia::render('User/Edit', [
            'user' => $output,
            'roles' => Roles::options()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('User/Create', ['roles' => Roles::options()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new UserInputDto(
            id: null,
            name: $data['name'],
            email: $data['email'],
            password: '',
            role: $data['role']
        );

        $output = $this->createUserUseCase->execute($input);

        if ($output->id) {
            Inertia::flash('message', 'Usuário inserido com sucesso. Um e-mail para definir a senha foi enviado!');

            return to_route('users.index');
        }

        return Inertia::flash('error', 'Não foi possível inserir o usuário')->back();
    }

    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new UserInputDto(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            role: $data['role']
        );

        $output = $this->updateUserUseCase->execute($input);

        if ($output->id) {
            return to_route('users.index')
                ->with('message', 'Usuário atualizado com sucesso!');
        }

        return to_route('users.index')
            ->with('error', 'Não foi possível atualizar o usuário');
    }

    public function destroy(IdUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $output = $this->deleteUserUseCase->execute($data['id']);

        if ($output)
            return Inertia::flash('message', 'Usuário excluído com sucesso!')->back();

        return Inertia::flash('error', 'Não foi possível excluir o usuário')->back();
    }

    public function get(Request $request): JsonResponse
    {
        $input = new UserInputDto(
            id: $request->id,
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role
        );

        $output = $this->getUserUseCase->execute($input);

        return response()->json($output);
    }

    public function list(Request $request): JsonResponse
    {
        $input = new UserInputDto(
            id: $request->id,
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role
        );

        $output = $this->listUserUseCase->execute($input);

        return response()->json($output);
    }

    public function newPassword(IdUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $output = $this->newPasswordUseCase->execute($data['id']);

        if ($output->success) {
            return Inertia::flash('message', 'E-mail enviado com sucesso!')->back();
        }

        return Inertia::flash('error', $output->error)->back();
    }

    public function editPassword(CreatePasswordRequest $request): Response|RedirectResponse
    {
        $data = $request->validated();

        $output = $this->editPasswordUseCase->execute($data);

        if (!$output->success) {
            return to_route('login')
                ->with('error', $output->error ?? 'O link para alteração de senha expirou ou é inválido.');
        }

        return Inertia::render('Auth/EditPassword', [
            'user' => $output->user,
            'verified' => $output->verified,
            'hasToken' => $output->hasToken
        ]);
    }

    public function storeNewPassword(StoreNewPasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $output = $this->storeNewPasswordUseCase->execute($data);

        if ($output->success) {
            return to_route('login')
                ->with('message', 'Senha atualizada com sucesso!');
        }

        return to_route('login')
            ->with('error', $output->error);
    }
}
