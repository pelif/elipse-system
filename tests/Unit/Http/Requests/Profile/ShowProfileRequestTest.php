<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Profile;

use App\Http\Requests\Profile\ShowProfileRequest;
use Mockery;
use Tests\TestCase;

class ShowProfileRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_authorize_returns_true_when_user_accesses_own_profile(): void
    {
        $user = new \stdClass();
        $user->id = 5;

        $routeMock = Mockery::mock(\Illuminate\Routing\Route::class);
        $routeMock->shouldReceive('parameter')->andReturn('5');

        $request = new ShowProfileRequest();
        $request->setUserResolver(fn() => $user);
        $request->setRouteResolver(fn() => $routeMock);

        $this->assertTrue($request->authorize());
    }

    public function test_authorize_returns_false_when_user_accesses_another_profile(): void
    {
        $user = new \stdClass();
        $user->id = 5;

        $routeMock = Mockery::mock(\Illuminate\Routing\Route::class);
        $routeMock->shouldReceive('parameter')->andReturn('9');

        $request = new ShowProfileRequest();
        $request->setUserResolver(fn() => $user);
        $request->setRouteResolver(fn() => $routeMock);

        $this->assertFalse($request->authorize());
    }

    public function test_rules_require_integer_id(): void
    {
        $request = new ShowProfileRequest();

        $rules = $request->rules();

        $this->assertArrayHasKey('id', $rules);
        $this->assertStringContainsString('required', $rules['id']);
        $this->assertStringContainsString('integer', $rules['id']);
    }

    public function test_messages_returns_portuguese_messages(): void
    {
        $request = new ShowProfileRequest();

        $messages = $request->messages();

        $this->assertArrayHasKey('id.required', $messages);
        $this->assertArrayHasKey('id.integer', $messages);
    }
}
