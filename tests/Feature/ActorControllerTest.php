<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;

class ActorControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testIndex()
    {
        $this->resetDatabase();
        $user = new User();

        $user->id = 1;

        $this->be($user);
        // Main page with actors list
        $response = $this->call('GET','/actor');

        $response->assertStatus(200)
          ->assertViewHas('actorslist')
          ->assertSeeText("Tesla Motors Inc.");        
        
        // A detailed page
        $response = $this->call('GET','/actor/124');
        $response->assertStatus(200)
          ->assertViewHas('actorInfo')
          ->assertSeeText("Actor details");
    }
}
