<?php
use Mockery as m;
use Ohio\Core\Base\Testing;

use Ohio\Content\Page\Page;
use Ohio\Content\Tag\Tag;
use Ohio\Content\Tag\Http\Requests\PaginateTaggables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaginateTaggablesTest extends Testing\OhioTestCase
{

    use Testing\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Ohio\Content\Tag\Http\Requests\PaginateTaggables::modifyQuery
     * @covers \Ohio\Content\Tag\Http\Requests\PaginateTaggables::tags
     * @covers \Ohio\Content\Tag\Http\Requests\PaginateTaggables::items
     */
    public function test()
    {
        $page = new Page();
        $page->id = 1;
        $page->name = 'page';

        $tag1 = new Tag();
        $tag1->id = 1;
        $tag1->name = 'tag 1';

        $qbMock = m::mock(Builder::class);
        $qbMock->shouldReceive('tagged')->once()->with('pages', 1);
        $qbMock->shouldReceive('notTagged')->once()->with('pages', 1);
        $qbMock->shouldReceive('get')->once()->andReturn(new Collection([$tag1]));

        # modifyQuery
        $paginateRequest = new PaginateTaggables(['taggable_id' => 1, 'taggable_type' => 'pages']);
        $paginateRequest->modifyQuery($qbMock);
        $paginateRequest->merge(['not' => true]);
        $paginateRequest->modifyQuery($qbMock);

        # tags
        $this->assertNull($paginateRequest->tags);
        $paginateRequest->tags();
        $this->assertInstanceOf(Tag::class, $paginateRequest->tags);

        # items
        $paginateRequest->items($qbMock);
    }

}