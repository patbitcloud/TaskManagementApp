<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
    
        // Create and authenticate a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /** @test */
    public function it_creates_a_task()
    {
        $user = User::factory()->create();

        // Authenticate using Sanctum
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Sample Task',
            'description' => 'This is a test task',
            'status' => 'pending',
        ]);
        $response->dump();
        $response->assertStatus(201);
    }

    /** @test */
    public function it_retrieves_a_task()
    {
        $task = Task::factory()->create();
        $response = $this->json('GET', "/api/tasks/{$task->id}", []);
        
        $response->assertStatus(200)
                 ->assertJson(['id' => $task->id, 'title' => $task->title]);
    }

    /** @test */
    public function it_updates_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'completed' // ✅ Ensure this is a valid status
        ]);
        
        $response->dump(); // Debug response content
        
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_a_task()
    {
        $task = Task::factory()->create();
        $response = $this->json('DELETE', "/api/tasks/{$task->id}", []);
        
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
