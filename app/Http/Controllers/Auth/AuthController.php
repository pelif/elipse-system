<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\StorePasswordRequest;
use App\UseCase\User\CreatePasswordUseCase;
use App\UseCase\User\DTO\PasswordInputDto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\UseCase\User\GetUserByEmailUseCase;
use App\UseCase\User\LoginUserUseCase;
use App\UseCase\User\UpdatePasswordUseCase;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private CreatePasswordUseCase $createPasswordUseCase,
        private UpdatePasswordUseCase $updatePasswordUseCase,
        private LoginUserUseCase $loginUserUseCase
    ) {}

    public function index(): Response
    {
        if (Auth::check()) {
            return Inertia::render('Welcome');
        }
        return Inertia::render('Auth/Login');
    }

    public function auth(LoginRequest $request)
    {
        $output = $this->loginUserUseCase->execute($request->validated());

        if ($output->success) {
            Auth::loginUsingId($output->user->getId());
            $request->session()->regenerate();

            return to_route('welcome')
                ->with('message', 'Login realizado com sucesso!');
        }

        return back()
            ->with('error', $output->error);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')
            ->with('message', 'Logout realizado com sucesso!');
    }

    public function createPassword(CreatePasswordRequest $request): Response|RedirectResponse
    {
        $data = $request->validated();
        $output = $this->createPasswordUseCase->execute($data);

        // Token invalid and password never set → the link is expired/invalid
        if (!$output->success && !$output->hasToken) {
            return to_route('login')
                ->with('error', $output->error ?? 'O link para criação de senha expirou ou é inválido.');
        }

        return Inertia::render('Auth/CreatePassword', [
            'user' => $output->user,
            'token' => $request['token'],
            'verified' => $output->verified,
            'hasToken' => $output->hasToken
        ]);
    }

    public function storePassword(StorePasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $input = new PasswordInputDto(
            id: $data['id'],
            password: $data['password']
        );

        $output = $this->updatePasswordUseCase->execute($input);

        if ($output->success) {
            return to_route('login')
                ->with('message', 'Senha criada com sucesso! Agora você pode fazer login.');
        }

        return to_route('login')
            ->with('error', $output->error);
    }
}
