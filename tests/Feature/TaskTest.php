<?php

namespace Tests\Feature;

use Tests\DatabaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;
use App\Task;

class TaskTest extends DatabaseTestCase
{
    protected $user;
    protected $task;


    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->task = factory(Task::class)->create(['user_id' => $this->user->id]);

    }

    public function testLoginRequired()
    {
        $payload = ['name' => 'My Card', 'task' => 1, 'user_id' => $this->task->id];

        $this->json('POST', 'api/tasks', $payload)
            ->assertStatus(401);
    }

    public function testCreateValidation()
    {
        $this->actingAs($this->user)
            ->json('POST', 'api/tasks')
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "task" => ["The task field is required."],
                    "status" => ["The status field is required."],
                    "user_id" => ["The user id field is required."],
                ]
            ]);
    }

}
