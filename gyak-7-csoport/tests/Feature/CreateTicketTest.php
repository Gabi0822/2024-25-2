<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCreateNewTicketWithCorrectInputs(): void {
        $user = User::factory()->create(['admin' => false]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $header = ['Authorization' => "Bearer $token"];
        $payload = [
            'title' => fake()->word(),
            'priority' => fake()->numberBetween(0,3),
            'text' => fake()->paragraph(),
        ];

        $this->json('POST', 'api/tickets, $payload, $header')->assertStatus(201);
        $this->assertDatabaseHas('tickets', [
            'title' => $payload['title'],
            'done' => false,
            'priority' => $payload['priority'],

        ]);

        $this->assertDatabaseHas('comments', [
            'text' => $payload['text'],
            'filename' => null,
            'filename_hash' => null,
            'user_id' => $user->id,
        ]);
    }

    public function testCreateNewTicketWithIncorrectInputs(): void {
        $user = User::factory()->create(['admin' => false]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $header = ['Authorization' => "Bearer $token"];
        $payload = [
            'title' => fake()->sentence(100),
            'priority' => fake()->numberBetween(0,3),
            'text' => fake()->paragraph(),
        ];

        $this->json('POST', 'api/tickets, $payload, $header')->assertStatus(422)->assertJson(['message' => 'valami', 'errors' => [ 'title' => ['valami']]]);
        $this->assertDatabaseMissing('tickets', [
            'title' => $payload['title'],
            'done' => false,
            'priority' => $payload['priority'],

        ]);

        $this->assertDatabaseMissing('comments', [
            'text' => $payload['text'],
            'filename' => null,
            'filename_hash' => null,
            'user_id' => $user->id,
        ]);
    }
}
