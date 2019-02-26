<?php namespace Tests\Belt\Content\Unit\Http\Requests;
use Mockery as m;
use Tests\Belt\Core;

use Belt\Content\Page;
use Belt\Content\Term;
use Belt\Content\Http\Requests\PaginateTerms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaginateTermsTest extends \Tests\Belt\Core\BeltTestCase
{

    use \Tests\Belt\Core\Base\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Http\Requests\PaginateTerms::modifyQuery
     */
    public function test()
    {
        $qbMock = m::mock(Builder::class);
        $qbMock->shouldReceive('whereIn')->once()->with('terms.parent_id', [1]);

        # modifyQuery
        $paginateRequest = new PaginateTerms(['parent_id' => 1]);
        $paginateRequest->modifyQuery($qbMock);
    }

}