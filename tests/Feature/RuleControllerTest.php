<?php

namespace Tests\Feature;
use App\User;
use Tests\TestCase;

class RuleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testIndex()
    {
        //$this->resetDatabaseAndSeed();
        $user = new User();

        $user->id = 1;

        $this->be($user);
        // Main page with actors list
        $response = $this->call('GET','/rule');

        $response->assertStatus(200)
          ->assertViewHas('ruleslist')
          ->assertSeeText("Draft By");        
        
        // Filter on Task
        $response = $this->call('GET','/rule?Task=na');
        $response->assertStatus(200)
          ->assertSeeText("National Phase")
          ->assertSeeText("Patent")
          ->assertSeeText("Filed")
          ->assertSeeText("World Intellectual Property Organization");  
        
        // A detailed page
        $response = $this->call('GET','/rule/5');
        $response->assertStatus(200)
          ->assertViewHas('ruleInfo')
          ->assertViewHas('ruleComments')
          ->assertSeeText("Rule details");
       
        // Autocompletion
        $response = $this->call('GET','/task-name/autocomplete/1?term=national');
        $response->assertStatus(200)
          ->assertJson( [0 => array(
            'value' => 'NPH',
            'label' => 'National Phase')]);
    }
}
