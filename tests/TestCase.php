<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $dropViews = true;
    
    public function resetDatabase() 
    {
    
        $this->artisan('migrate:rollback');
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }
    
    public function resetDatabaseAndSeed() 
    {
        $this->resetDatabase();
        $this->artisan('db:seed --class=SampleSeeder');
    }
}
