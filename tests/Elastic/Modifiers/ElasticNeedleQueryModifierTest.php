<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\NeedleQueryModifier;

class ElasticNeedleQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\NeedleQueryModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new NeedleQueryModifier($engine);

        $this->assertFalse(isset($engine->query['bool']['should'][0]['multi_match']));
        $this->assertFalse(isset($engine->query['bool']['should'][1]['wildcard']));
        $modifier->modify(new PaginateRequest(['q' => 'test']));
        $this->assertTrue(isset($engine->query['bool']['should'][0]['multi_match']));
        $this->assertTrue(isset($engine->query['bool']['should'][1]['wildcard']));
    }

}