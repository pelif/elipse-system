<?php

namespace Tests\Unit\UseCase\EmailConfig;

use App\Domain\EmailConfig\Entities\EmailConfig as EmailConfigEntity;
use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\EmailConfig\CreateEmailConfigUseCase;
use App\UseCase\EmailConfig\DTO\CreateEmailConfigInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateEmailConfigUseCaseTest extends TestCase
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(EmailConfigRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $useCase = new CreateEmailConfigUseCase($this->repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(['address' => 'x@example.com']);
    }

    public function test_creates_email_config(): void
    {
        $input = new CreateEmailConfigInputDto(
            address: 'x@example.com',
            departmentId: 'uuid-dept-1'
        );

        $createdEntity = new EmailConfigEntity(
            id: 'uuid-email-setting-1',
            email: $input->address,
            departmentId: $input->departmentId
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input) {
                if (!$entity instanceof EmailConfigEntity) {
                    return false;
                }

                return $entity->getId() === null
                    && $entity->getEmail() === $input->address
                    && $entity->getDepartmentId() === $input->departmentId;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateEmailConfigUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertSame('uuid-email-setting-1', $output->id);
        $this->assertSame('x@example.com', $output->address);
        $this->assertSame('uuid-dept-1', $output->departmentId);
    }
}

