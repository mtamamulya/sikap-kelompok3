<?php

namespace Tests\Feature;

<<<<<<< HEAD
use Illuminate\Foundation\Testing\RefreshDatabase; // Tambahkan ini
=======
use Illuminate\Foundation\Testing\RefreshDatabase;
>>>>>>> f263c9bbf94e464639a070c51fbe79f7ec205667
use Tests\TestCase;

class ExampleTest extends TestCase
{
<<<<<<< HEAD
    use RefreshDatabase; // Tambahkan baris ini di dalam class
=======
    use RefreshDatabase;
>>>>>>> f263c9bbf94e464639a070c51fbe79f7ec205667

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertOk();
    }
}