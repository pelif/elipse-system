<?php

namespace App\Http\Controllers\ThirdParty;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThirdParty\DeleteThirdPartyRequest;
use App\Http\Requests\ThirdParty\ListThirdPartyRequest;
use App\Http\Requests\ThirdParty\ShowThirdPartyRequest;
use App\Http\Requests\ThirdParty\StoreThirdPartyRequest;
use App\Http\Requests\ThirdParty\UpdateThirdPartyRequest;
use App\UseCase\Service\ListServicesForSelectUseCase;
use App\UseCase\ThirdParty\CreateThirdPartyUseCase;
use App\UseCase\ThirdParty\DeleteThirdPartyUseCase;
use App\UseCase\ThirdParty\DTO\CreateThirdPartyInputDto;
use App\UseCase\ThirdParty\DTO\UpdateThirdPartyInputDto;
use App\UseCase\ThirdParty\GetThirdPartyUseCase;
use App\UseCase\ThirdParty\ListThirdPartiesUseCase;
use App\UseCase\ThirdParty\UpdateThirdPartyUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ThirdPartyController extends Controller
{
    public function __construct(
        private ListThirdPartiesUseCase $listThirdPartiesUseCase,
        private CreateThirdPartyUseCase $createThirdPartyUseCase,
        private GetThirdPartyUseCase $getThirdPartyUseCase,
        private UpdateThirdPartyUseCase $updateThirdPartyUseCase,
        private DeleteThirdPartyUseCase $deleteThirdPartyUseCase,
        private ListServicesForSelectUseCase $listServicesForSelectUseCase
    ) {}

    public function index(ListThirdPartyRequest $request): Response
    {
        $data = $request->validated();
        $output = $this->listThirdPartiesUseCase->execute($data);

        return Inertia::render('ThirdParty/Index', [
            'thirdParties' => $output,
            'canManageThirdParties' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
            'serviceSelectOptions' => $this->listServicesForSelectUseCase->execute(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('ThirdParty/Create', [
            'canManageThirdParties' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
            'serviceSelectOptions' => $this->listServicesForSelectUseCase->execute(),
        ]);
    }

    public function store(StoreThirdPartyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $serviceIds = [];
        if (isset($data['service_ids']) && is_array($data['service_ids'])) {
            $serviceIds = array_values($data['service_ids']);
        }

        $input = new CreateThirdPartyInputDto(
            name: (string) $data['name'],
            alias: self::nullableTrimmedString($data['alias'] ?? null),
            phone: (string) $data['phone'],
            phoneAlternative: self::nullableTrimmedString($data['phone_alternative'] ?? null),
            email: (string) $data['email'],
            emailAlternative: self::nullableTrimmedString($data['email_alternative'] ?? null),
            indication: self::nullableTrimmedString($data['indication'] ?? null),
            observation: self::nullableTrimmedString($data['observation'] ?? null),
            companyName: self::nullableTrimmedString($data['company_name'] ?? null),
            document: (string) $data['document'],
            active: (bool) $data['active'],
            createdBy: $request->user()?->id,
            updatedBy: $request->user()?->id,
            serviceIds: $serviceIds,
        );

        $output = $this->createThirdPartyUseCase->execute($input);

        if (! empty($output->id)) {
            Inertia::flash('message', 'Terceiro cadastrado com sucesso!');

            return to_route('third-parties.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o terceiro.');

        return back();
    }

    public function show(ShowThirdPartyRequest $request): HttpResponse|Response
    {
        $request->validated();
        $id = (string) $request->route('id');

        $output = $this->getThirdPartyUseCase->execute($id);

        if (! $output) {
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($output);
        }

        return Inertia::render('ThirdParty/Show', [
            'thirdParty' => $output,
            'canManageThirdParties' => $request->user()?->hasAnyRole(['admin', 'commercial']) ?? false,
        ]);
    }

    public function update(UpdateThirdPartyRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $serviceIds = [];
        if (isset($data['service_ids']) && is_array($data['service_ids'])) {
            $serviceIds = array_values($data['service_ids']);
        }

        $input = new UpdateThirdPartyInputDto(
            id: $id,
            name: (string) $data['name'],
            alias: self::nullableTrimmedString($data['alias'] ?? null),
            phone: (string) $data['phone'],
            phoneAlternative: self::nullableTrimmedString($data['phone_alternative'] ?? null),
            email: (string) $data['email'],
            emailAlternative: self::nullableTrimmedString($data['email_alternative'] ?? null),
            indication: self::nullableTrimmedString($data['indication'] ?? null),
            observation: self::nullableTrimmedString($data['observation'] ?? null),
            companyName: self::nullableTrimmedString($data['company_name'] ?? null),
            document: (string) $data['document'],
            active: (bool) $data['active'],
            updatedBy: $request->user()?->id,
            serviceIds: $serviceIds,
        );

        $output = $this->updateThirdPartyUseCase->execute($input);

        if ($output) {
            Inertia::flash('message', 'Terceiro atualizado com sucesso!');

            return to_route('third-parties.index');
        }

        Inertia::flash('error', 'Não foi possível atualizar o terceiro.');

        return to_route('third-parties.index');
    }

    public function destroy(DeleteThirdPartyRequest $request, string $id): RedirectResponse
    {
        $request->validated();

        $deleted = $this->deleteThirdPartyUseCase->execute($id);

        if ($deleted) {
            Inertia::flash('message', 'Terceiro excluído com sucesso!');

            return to_route('third-parties.index');
        }

        Inertia::flash('error', 'Não foi possível excluir o terceiro.');

        return to_route('third-parties.index');
    }

    private static function nullableTrimmedString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
