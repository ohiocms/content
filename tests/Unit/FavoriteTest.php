<?php namespace Tests\Belt\Content\Unit;

use Tests\Belt\Core\BeltTestCase;
use Belt\Content\Favorite;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteTest extends BeltTestCase
{
    /**
     * @covers \Belt\Content\Favorite::user
     */
    public function test()
    {
        $favorite = new Favorite();

        # user
        $this->assertInstanceOf(BelongsTo::class, $favorite->user());
    }

}