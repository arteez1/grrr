<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_role(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);

        $this->actingAs($admin)
            ->post(route('filament.admin.resources.roles.create'), [
                'name' => 'manager',
            ])
            ->assertRedirect();
    }
}
