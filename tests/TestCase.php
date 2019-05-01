<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    public function resetDatabase() 
    {
    
        $this->artisan('migrate:rollback');
        $this->artisan('migrate');
        $this->artisan('db:seed');
        $this->artisan('db:seed --class=SampleSeeder');
    }
}
