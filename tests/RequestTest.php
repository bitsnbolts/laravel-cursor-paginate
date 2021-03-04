<?php

namespace Bitsnbolts\CursorPaginate\Tests;

class RequestTest extends TestCase
{
    /** @test */
    public function it_will_return_the_next_cursor_url_in_the_response()
    {
        (new TestModelFactory)->count(11)->create();
        $response = $this->get('/');

        $response->assertJsonFragment(['next' => 'http://localhost?cursor=WyIyMDIxLTAzLTAzIDAwOjAwOjAwIiwxMF0%3D']);
    }

    /** @test */
    public function it_will_discover_the_current_cursor_url()
    {
        (new TestModelFactory)->count(11)->create();
        $response = $this->get('/?cursor=WyIyMDIxLTAzLTAzIDAwOjAwOjAwIiwyXQ%3D%3D');

        $response->assertJsonFragment(['self' => 'http://localhost?cursor=WyIyMDIxLTAzLTAzIDAwOjAwOjAwIiwyXQ%3D%3D']);
    }

    /** @test */
    public function the_next_url_will_return_the_next_set_of_results()
    {
        (new TestModelFactory)->count(11)->create();
        $response = $this->get('/');
        $next = json_decode($response->getContent())->next;

        $response = $this->get($next);
        $response->assertJsonFragment(['id' => 9]);
    }
}
