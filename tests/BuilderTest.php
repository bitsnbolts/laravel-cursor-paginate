<?php

namespace Bitsnbolts\CursorPaginate\Tests;

class BuilderTest extends TestCase
{
    /** @test */
    public function it_can_paginate_records()
    {
        (new TestModelFactory)->count(22)->create();
        $paginator = TestModel::cursorPaginateWithTotal(10, ['created_at' => 'desc', 'id' => 'desc']);

        $this->assertCount(10, $paginator->items());
    }

    /** @test */
    public function it_has_a_next_cursor_url_if_there_are_items_left()
    {
        (new TestModelFactory)->count(11)->create();
        $paginator = TestModel::cursorPaginateWithTotal(10, ['created_at' => 'desc', 'id' => 'desc']);

        $this->assertEquals('http://localhost?cursor=WyIyMDIxLTAzLTAzIDAwOjAwOjAwIiwyXQ%3D%3D', $paginator->nextCursorUrl());
    }
}
