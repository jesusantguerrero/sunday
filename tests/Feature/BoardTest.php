<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BoardTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateBoard()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)->json('POST', '/api/boards', ['name' => 'Sally']);
        $response->dump();
        $response->assertStatus(201);
    }
}
