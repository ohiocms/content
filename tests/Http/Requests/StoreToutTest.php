<?php

use Belt\Content\Http\Requests\StoreTout;

class StoreToutTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers \Belt\Content\Http\Requests\StoreTout::rules
     */
    public function test()
    {

        $request = new StoreTout();

        $this->assertNotEmpty($request->rules());
    }

}