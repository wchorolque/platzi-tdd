<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class RepositoryControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_guest()
    {
        // index
        $this->get('repositories')->assertRedirect('login');
        // show
        $this->get('repositories/1')->assertRedirect('login');
        // edit
        $this->get('repositories/1/edit')->assertRedirect('login');
        // update
        $this->put('repositories/1')->assertRedirect('login');
        // destroy
        $this->delete('repositories/1')->assertRedirect('login');
        // create
        $this->get('repositories/create')->assertRedirect('login');
        // guardar
        $this->post('repositories', [])->assertRedirect('login');
    }

    public function test_store()
    {
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('repositories', $data)
            ->assertRedirect('repositories');

        $this->assertDatabaseHas('repositories', $data);
    }

    public function test_update()
    {
        //$this->withoutExceptionHandling();
        $repository = Repository::factory()->create();
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $user = User::factory()->create();

        $this->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertRedirect("repositories/$repository->id/edit");

        $this->assertDatabaseHas('repositories', $data);
    }

    public function test_validate_store()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('repositories', [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }
}
