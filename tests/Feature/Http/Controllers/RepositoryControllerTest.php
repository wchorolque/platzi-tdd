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
    
    public function test_index_empty()
    {
        Repository::factory()->create(); // user_id = 1
        $user = User::factory()->create(); // user->id = 2

        $this
            ->actingAs($user)
            ->get('repositories')
            ->assertStatus(200)
            ->assertSee('No hay repositorios creados');
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
        $user = User::factory()->create();
        $repository = Repository::factory()->create(['user_id' => $user->id]);
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $this->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertRedirect("repositories/$repository->id/edit");

        $this->assertDatabaseHas('repositories', $data);
    }

    public function test_update_policy()
    {
        $user = User::factory()->create(); // id = 1
        $repository = Repository::factory()->create(); // id = 2

        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $this->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertStatus(403);
    }

    public function test_validate_store()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('repositories', [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }

    public function test_validate_update()
    {
        $repository = Repository::factory()->create();
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }

    public function test_destroy()
    {
        $user = User::factory()->create();
        $repository = Repository::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertRedirect("repositories");

        $this->assertDatabaseMissing('repositories', [
            'id' => $repository->id,
            'url' => $repository->url, 
            'description' => $repository->description
        ]);
    }

    public function test_destroy_policy()
    {
        $user = User::factory()->create(); // id = 1
        $repository = Repository::factory()->create(); // id = 2

        $this->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertStatus(403);
    }
}
