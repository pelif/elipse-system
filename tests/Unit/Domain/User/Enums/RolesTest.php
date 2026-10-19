<?php

namespace Tests\Unit\Domain\User\Enums;

use App\Domain\User\Enums\Roles;
use Tests\TestCase;

class RolesTest extends TestCase
{
    public function test_it_returns_options_array(): void
    {
        $options = Roles::options();

        $this->assertIsArray($options);
        $this->assertCount(4, $options);
        $this->assertArrayHasKey('value', $options[0]);
        $this->assertArrayHasKey('label', $options[0]);
    }

    public function test_it_returns_label_for_each_role(): void
    {
        $this->assertEquals('administrador', Roles::administrador->name);
        $this->assertEquals('comercial', Roles::comercial->name);
        $this->assertEquals('produção', Roles::produção->name);
        $this->assertEquals('financeiro', Roles::financeiro->name);
    }
}
