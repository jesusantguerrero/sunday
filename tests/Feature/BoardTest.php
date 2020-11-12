<?php

namespace Tests\Feature;

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
        $response = $this->json('POST', '/api/boards', ['name' => 'Sally']);;

        $response->assertStatus(201);
    }
}
