<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ShowProfileRequest;
use App\UseCase\Profile\GetProfileUseCase;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(
        private GetProfileUseCase $getProfileUseCase
    ) {}

    public function show(ShowProfileRequest $request): Response
    {
        $request->validated();
        $output = $this->getProfileUseCase->execute((int) $request->route('id'));

        if (!$output) {
            abort(404);
        }

        return Inertia::render('Profile/Show', [
            'user' => $output,
        ]);
    }
}
