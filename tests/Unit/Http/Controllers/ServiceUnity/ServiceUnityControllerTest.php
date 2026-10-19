<?php

namespace Tests\Unit\Http\Controllers\ServiceUnity;

use App\Http\Controllers\ServiceUnity\ServiceUnityController;
use App\Http\Requests\ServiceUnity\StoreRequest;
use App\UseCase\ServiceUnity\CreateServiceUnity;
use App\UseCase\ServiceUnity\DeleteServiceUnity;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use App\UseCase\ServiceUnity\DTO\ServiceUnityOutputDto;
use App\UseCase\ServiceUnity\GetServiceUnity;
use App\UseCase\ServiceUnity\ListServiceUnity;
use App\UseCase\ServiceUnity\UpdateServiceUnity;
use Illuminate\Http\RedirectResponse;
use Mockery;
use Tests\TestCase;

class ServiceUnityControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_builds_input_dto_and_redirects_when_created(): void
    {
        $output = new ServiceUnityOutputDto(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            unity: 'Unidade Centro',
            createdBy: 1,
            updatedBy: null,
            createdAt: '2026-09-12 12:00:00',
            updatedAt: null
        );

        $createUseCase = Mockery::mock(CreateServiceUnity::class);
        $createUseCase
            ->shouldReceive('execute')
            ->once()
            ->with(Mockery::on(function ($input): bool {
                return $input instanceof ServiceUnityInputDto
                    && $input->id === null
                    && $input->unity === 'Unidade Centro'
                    && $input->createdBy === 1
                    && $input->updatedBy === null
                    && is_string($input->createdAt)
                    && $input->updatedAt === null;
            }))
            ->andReturn($output);

        $controller = new ServiceUnityController(
            Mockery::mock(ListServiceUnity::class),
            $createUseCase,
            Mockery::mock(GetServiceUnity::class),
            Mockery::mock(UpdateServiceUnity::class),
            Mockery::mock(DeleteServiceUnity::class)
        );

        $user = new class {
            public int $id = 1;
        };

        $request = Mockery::mock(StoreRequest::class);
        $request->shouldReceive('validated')->once()->andReturn(['unity' => 'Unidade Centro']);
        $request->shouldReceive('user')->andReturn($user);

        $response = $controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
