<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;

class ActorControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testIndex()
    {
        $this->resetDatabaseAndSeed();
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
        
        // A page used-in
        $response = $this->call('GET','/actor/124/usedin');
        $response->assertStatus(200)
          ->assertViewHas('matter_dependencies')
          ->assertViewHas('other_dependencies')
          ->assertSeeText("Matter Dependencies");
          
        // Autocompletion
        $response = $this->call('GET','/actor/autocomplete?term=Tes');
        $response->assertStatus(200)
          ->assertJson( [0 => array(
            'value' => 'Tesla Motors Inc.')]);
    }
}
