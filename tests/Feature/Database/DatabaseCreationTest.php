<?php

namespace Tests\Feature\Database;

use Database\Seeders\SensorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseCreationTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_base()
    {
        $this->seed(SensorSeeder::class);

        $this->assertTrue(true);
    }
}
