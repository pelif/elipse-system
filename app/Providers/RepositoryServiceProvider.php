<?php

namespace App\Providers;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\Domain\Job\Repositories\JobOrganizerRepositoryInterface;
use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\Domain\Logs\Repositories\SystemLogRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Eloquent\ComissionedAgent\EloquentComissionedAgentRepository;
use App\Infrastructure\Eloquent\Contractor\EloquentContractorRepository;
use App\Infrastructure\Eloquent\Customer\EloquentCustomerRepository;
use App\Infrastructure\Eloquent\EmailConfig\EloquentEmailConfigRepository;
use App\Infrastructure\Eloquent\Job\EloquentJobOrganizerRepository;
use App\Infrastructure\Eloquent\Job\EloquentJobRepository;
use App\Infrastructure\Eloquent\Order\EloquentOrderRepository;
use App\Infrastructure\Eloquent\Organizer\EloquentOrganizerRepository;
use App\Infrastructure\Eloquent\Product\EloquentProductRepository;
use App\Infrastructure\Eloquent\Resource\EloquentResourceRepository;
use App\Infrastructure\Eloquent\ResourceCategory\EloquentResourceCategoryRepository;
use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Infrastructure\Eloquent\ServiceUnity\EloquentServiceUnityRepository;
use App\Infrastructure\Eloquent\Status\EloquentStatusRepository;
use App\Infrastructure\Eloquent\ThirdParty\EloquentThirdPartyRepository;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Files\Logs\LocalSystemLogRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            StatusRepositoryInterface::class,
            EloquentStatusRepository::class
        );

        $this->app->bind(
            SystemLogRepositoryInterface::class,
            LocalSystemLogRepository::class
        );

        $this->app->bind(
            EmailConfigRepositoryInterface::class,
            EloquentEmailConfigRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        $this->app->bind(
            ComissionedAgentRepositoryInterface::class,
            EloquentComissionedAgentRepository::class
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            EloquentCustomerRepository::class
        );

        $this->app->bind(
            ThirdPartyRepositoryInterface::class,
            EloquentThirdPartyRepository::class
        );

        $this->app->bind(
            ContractorRepositoryInterface::class,
            EloquentContractorRepository::class
        );

        $this->app->bind(
            OrganizerRepositoryInterface::class,
            EloquentOrganizerRepository::class
        );

        $this->app->bind(
            ResourceRepositoryInterface::class,
            EloquentResourceRepository::class
        );

        $this->app->bind(
            ResourceCategoryRepositoryInterface::class,
            EloquentResourceCategoryRepository::class
        );

        $this->app->bind(
            ServiceUnityRepositoryInterface::class,
            EloquentServiceUnityRepository::class
        );

        $this->app->bind(
            ServiceRepositoryInterface::class,
            EloquentServiceRepository::class
        );

        $this->app->bind(
            JobRepositoryInterface::class,
            EloquentJobRepository::class
        );

        $this->app->bind(
            JobOrganizerRepositoryInterface::class,
            EloquentJobOrganizerRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
