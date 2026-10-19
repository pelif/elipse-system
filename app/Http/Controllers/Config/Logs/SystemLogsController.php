<?php

namespace App\Http\Controllers\Config\Logs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\ShowSystemLogRequest;
use App\UseCase\Logs\GetSystemLogContentUseCase;
use App\UseCase\Logs\ListSystemLogsUseCase;
use Inertia\Inertia;
use Inertia\Response;

class SystemLogsController extends Controller
{
    public function __construct(
        private readonly ListSystemLogsUseCase $listSystemLogsUseCase,
        private readonly GetSystemLogContentUseCase $getSystemLogContentUseCase,
    ) {}

    public function index(): Response
    {
        $result = $this->listSystemLogsUseCase->execute();

        return Inertia::render('SystemLogs/Index', [
            'logs' => $result['data'],
        ]);
    }

    public function show(ShowSystemLogRequest $request): Response
    {
        $dto = $this->getSystemLogContentUseCase->execute($request->validated());

        return Inertia::render('SystemLogs/Show', [
            'file' => $dto->file,
            'content' => $dto->content,
            'file_size_bytes' => $dto->fileSizeBytes,
            'error_message' => $dto->errorMessage,
        ]);
    }
}
