<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ComissionedAgent\ComissionedAgentController;
use App\Http\Controllers\Config\EmailConfig\EmailConfigController;
use App\Http\Controllers\Config\JobFlow\JobStatusController;
use App\Http\Controllers\Config\Logs\SystemLogsController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Contractor\ContractorController;
use App\Http\Controllers\Job\JobController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Organizer\OrganizerController;
use App\Http\Controllers\Resource\ResourceController;
use App\Http\Controllers\ResourceCategory\ResourceCategoryController;
use App\Http\Controllers\ThirdParty\ThirdPartyController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\ServiceUnity\ServiceUnityController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome')->middleware('auth');

Route::middleware('auth')->prefix('cadastros')->group(function () {
    // Products Routes
    Route::get('/produtos/novo', [ProductController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('products.create');

    Route::get('/produtos/for-select', [ProductController::class, 'forSelect'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('products.for-select');

    Route::get('/produtos', [ProductController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('products.index');

    Route::post('/produtos', [ProductController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('products.store');

    Route::put('/produtos/{id}', [ProductController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('products.update');

    Route::delete('/produtos/{id}', [ProductController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('products.destroy');

    Route::get('/produtos/{id}', [ProductController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('products.show');

    // Third Parties Routes
    Route::get('/terceiros/novo', [ThirdPartyController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('third-parties.create');

    Route::get('/terceiros', [ThirdPartyController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('third-parties.index');

    Route::post('/terceiros', [ThirdPartyController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('third-parties.store');

    Route::put('/terceiros/{id}', [ThirdPartyController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('third-parties.update');

    Route::delete('/terceiros/{id}', [ThirdPartyController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('third-parties.destroy');

    Route::get('/terceiros/{id}', [ThirdPartyController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('third-parties.show');

    // Services Routes
    Route::get('/servicos/novo', [ServiceController::class, 'create'])
        ->middleware(['role:admin,commercial,production'])
        ->name('services.create');

    Route::get('/servicos', [ServiceController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('services.index');

    Route::post('/servicos', [ServiceController::class, 'store'])
        ->middleware(['role:admin,commercial,production'])
        ->name('services.store');

    Route::put('/servicos/{id}', [ServiceController::class, 'update'])
        ->middleware(['role:admin,commercial,production'])
        ->name('services.update');

    Route::delete('/servicos/{id}', [ServiceController::class, 'destroy'])
        ->middleware(['role:admin,commercial,production'])
        ->name('services.destroy');

    Route::get('/servicos/{id}', [ServiceController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('services.show');

    //Service Unity Routes
    Route::get('/unidade-servico/novo', [ServiceUnityController::class, 'create'])
        ->middleware('role:admin,commercial')
        ->name('unity-service.create');

    Route::get('/unidade-servico', [ServiceUnityController::class, 'index'])
        ->middleware('role:admin,commercial,production,finance')
        ->name('unity-service.index');

    Route::post('/unidade-servico', [ServiceUnityController::class, 'store'])
        ->middleware('role:admin,commercial')
        ->name('unity-service.store');

    Route::put('/unidade-servico/{id}', [ServiceUnityController::class, 'update'])
        ->middleware('role:admin,commercial')
        ->name('unity-service.update');

    Route::delete('/unidade-servico/{id}', [ServiceUnityController::class, 'destroy'])
        ->middleware('role:admin,commercial')
        ->name('unity-service.destroy');

    Route::get('/unidade-servico/{id}', [ServiceUnityController::class, 'show'])
        ->middleware('role:admin,commercial,production,finance')
        ->name('unity-service.show');


    // Contractors Routes
    Route::get('/contratantes/novo', [ContractorController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('contractors.create');

    Route::get('/contratantes/for-select', [ContractorController::class, 'forSelect'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('contractors.for-select');

    Route::get('/contratantes', [ContractorController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('contractors.index');

    Route::post('/contratantes', [ContractorController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('contractors.store');

    Route::put('/contratantes/{id}', [ContractorController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('contractors.update');

    Route::delete('/contratantes/{id}', [ContractorController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('contractors.destroy');

    Route::get('/contratantes/{id}', [ContractorController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('contractors.show');

    // Organizers Routes
    Route::get('/organizadores/novo', [OrganizerController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('organizers.create');

    Route::get('/organizadores', [OrganizerController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('organizers.index');

    Route::post('/organizadores', [OrganizerController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('organizers.store');

    Route::put('/organizadores/{id}', [OrganizerController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('organizers.update');

    Route::delete('/organizadores/{id}', [OrganizerController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('organizers.destroy');

    Route::get('/organizadores/{id}', [OrganizerController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('organizers.show');

    // Resource Categories Routes
    Route::get('/categorias/novo', [ResourceCategoryController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('resource-categories.create');

    Route::get('/categorias', [ResourceCategoryController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('resource-categories.index');

    Route::post('/categorias', [ResourceCategoryController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('resource-categories.store');

    Route::put('/categorias/{id}', [ResourceCategoryController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('resource-categories.update');

    Route::delete('/categorias/{id}', [ResourceCategoryController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('resource-categories.destroy');

    Route::get('/categorias/{id}', [ResourceCategoryController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('resource-categories.show');

    // Resources Routes
    Route::get('/recursos/novo', [ResourceController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('resources.create');

    Route::get('/recursos', [ResourceController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('resources.index');

    Route::post('/recursos', [ResourceController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('resources.store');

    Route::put('/recursos/{id}', [ResourceController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('resources.update');

    Route::delete('/recursos/{id}', [ResourceController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('resources.destroy');

    Route::get('/recursos/{id}', [ResourceController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('resources.show');

    // Customers Routes
    Route::get('/clientes/novo', [CustomerController::class, 'create'])
        ->middleware(['role:admin,commercial'])
        ->name('customers.create');

    Route::get('/clientes/for-select', [CustomerController::class, 'forSelect'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('customers.for-select');

    Route::get('/clientes', [CustomerController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('customers.index');

    Route::post('/clientes', [CustomerController::class, 'store'])
        ->middleware(['role:admin,commercial'])
        ->name('customers.store');

    Route::put('/clientes/{id}/financial', [CustomerController::class, 'updateFinancial'])
        ->middleware(['role:admin,commercial'])
        ->name('customers.update-financial');

    Route::put('/clientes/{id}', [CustomerController::class, 'update'])
        ->middleware(['role:admin,commercial'])
        ->name('customers.update');

    Route::delete('/clientes/{id}', [CustomerController::class, 'destroy'])
        ->middleware(['role:admin,commercial'])
        ->name('customers.destroy');

    Route::get('/clientes/{id}', [CustomerController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('customers.show');
});

// Comissioned Agent Routes
Route::middleware(['auth'])->prefix('cadastros')->group(function () {
    Route::get('/comissionados/for-select', [ComissionedAgentController::class, 'forSelect'])
        ->middleware(['role:admin,commercial'])
        ->name('comissioned-agents.for-select');

    Route::get('/comissionados', [ComissionedAgentController::class, 'index'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('comissioned-agents.index');

    Route::get('/comissionados/novo', [ComissionedAgentController::class, 'create'])
        ->middleware(['role:admin'])
        ->name('comissioned-agents.create');

    Route::post('/comissionados', [ComissionedAgentController::class, 'store'])
        ->middleware(['role:admin'])
        ->name('comissioned-agents.store');

    Route::get('/comissionados/{id}', [ComissionedAgentController::class, 'show'])
        ->middleware(['role:admin,commercial,production,finance'])
        ->name('comissioned-agents.show');

    Route::get('/comissionados/{id}/edit', [ComissionedAgentController::class, 'edit'])
        ->middleware(['role:admin'])
        ->name('comissioned-agents.edit');

    Route::put('/comissionados/{id}', [ComissionedAgentController::class, 'update'])
        ->middleware(['role:admin'])
        ->name('comissioned-agents.update');

    Route::delete('/comissionados/{id}', [ComissionedAgentController::class, 'destroy'])
        ->middleware(['role:admin'])
        ->name('comissioned-agents.destroy');
});

Route::middleware('auth')->prefix('fluxo')->group(function () {
    Route::get('/jobs/novo', [JobController::class, 'create'])
        ->middleware('role:commercial')
        ->name('jobs.create');

    Route::get('/jobs/proximo-codigo', [JobController::class, 'nextCode'])
        ->middleware('role:commercial')
        ->name('jobs.next-code');

    Route::post('/jobs', [JobController::class, 'store'])
        ->middleware('role:commercial')
        ->name('jobs.store');

    Route::get('/jobs', [JobController::class, 'index'])
        ->middleware('role:admin,commercial,production,finance')
        ->name('jobs.index');

    Route::get('/os', [OrderController::class, 'index'])
        ->middleware('role:admin,commercial,production,finance')
        ->name('orders.index');

    Route::get('/editar-os', [OrderController::class, 'edit'])
        ->middleware('role:admin,commercial,production,finance')
        ->name('orders.edit');
});

// Login Routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('login.auth');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cadastrar/senha', [AuthController::class, 'createPassword'])->name('password.reset');
Route::get('/alterar/senha', [AuthController::class, 'editPassword'])->name('edit-password');
Route::put('/salvar/senha/{id}', [AuthController::class, 'storePassword'])->name('password.update');
Route::get('/alterar/senha', [UserController::class, 'editPassword'])->name('edit-password');
Route::put('/gerar/nova/senha/{id}', [UserController::class, 'storeNewPassword'])->name('store.new.password');


// Config Routes
Route::prefix('configuracoes')
    ->middleware(['auth', 'is.admin'])
    ->group(function () {

        // Users Routes
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/novo', [UserController::class, 'create'])->name('users.create');
        Route::get('/usuarios/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/usuarios/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::post('/usuarios/{id}/nova/senha', [UserController::class, 'newPassword'])->name('users.new-password');

        // Job Flow Routes
        Route::get('/fluxo-de-job', [JobStatusController::class, 'index'])->name('job-flow.index');
        Route::post('/fluxo-de-job', [JobStatusController::class, 'store'])->name('job-flow.store');
        Route::get('/fluxo-de-job/novo', [JobStatusController::class, 'create'])->name('job-flow.create');
        Route::get('/fluxo-de-job/{id}', [JobStatusController::class, 'show'])->name('job-flow.show');
        Route::get('/fluxo-de-job/{id}/edit', [JobStatusController::class, 'edit'])->name('job-flow.edit');
        Route::get('/fluxo-de-job/{id}/delete', [JobStatusController::class, 'delete'])->name('job-flow.delete');
        Route::put('/fluxo-de-job/{id}', [JobStatusController::class, 'update'])->name('job-flow.update');
        Route::delete('/fluxo-de-job/{id}', [JobStatusController::class, 'destroy'])->name('job-flow.destroy');

        // System Logs Routes
        Route::get('/logs', [SystemLogsController::class, 'index'])->name('config.logs.index');
        Route::get('/logs/{file}', [SystemLogsController::class, 'show'])->name('config.logs.show');

        // Email Config Routes
        Route::get('/config-email', [EmailConfigController::class, 'index'])->name('email-config.index');
        Route::get('/config-email/novo', [EmailConfigController::class, 'create'])->name('email-config.create');
        Route::post('/config-email', [EmailConfigController::class, 'store'])->name('email-config.store');
        Route::get('/config-email/{id}', [EmailConfigController::class, 'show'])->name('email-config.show');
        Route::get('/config-email/{id}/edit', [EmailConfigController::class, 'edit'])->name('email-config.edit');
        Route::put('/config-email/{id}', [EmailConfigController::class, 'update'])->name('email-config.update');
        Route::delete('/config-email/{id}', [EmailConfigController::class, 'destroy'])->name('email-config.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/perfil/usuario/{id}', [ProfileController::class, 'show'])
        ->name('profile.show');
});

Route::prefix('tests')->group(function () {
    Route::get('/hello', function () {
        throw new Exception('Test of Exception');
    });
});
