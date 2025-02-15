<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'status' => 'pending',
        ];

        $response = $this->json('POST', '/api/tasks', $data);
        
        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'description', 'status']);
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

        $data = [
            'title' => 'Updated Task Title',
            'status' => 'in-progress' // ✅ Ensure this is a valid status
        ];

        $response = $this->json('PUT', "/api/tasks/{$task->id}", $data);

        $response->assertStatus(200)
                ->assertJson(['title' => 'Updated Task Title']);
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
