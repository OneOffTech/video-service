<?php

namespace Tests\Feature\Http\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddFrameOptionHeaderTest extends TestCase
{

    public function test_header_present_to_web_routes()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
    
    public function test_header_not_added_to_api_routes()
    {
        $response = $this->get('/oembed');

        $response->assertHeaderMissing('X-Frame-Options');
    }
}
