<?php

namespace Tests\Feature\Http\Controllers;

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
}
