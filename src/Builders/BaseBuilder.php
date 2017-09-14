<?php

namespace Belt\Content\Builders;

use Belt;
use Belt\Content\Behaviors\IncludesTemplateInterface;
use Belt\Content\Section;

/**
 * Class BaseBuilder
 * @package Belt\Content\Builders
 */
abstract class BaseBuilder
{
    /**
     * @var IncludesTemplateInterface
     */
    public $item;

    /**
     * @var Section
     */
    public $sections;

    public function __construct(IncludesTemplateInterface $item)
    {
        $this->item = $item;
    }

    abstract function build();

    /**
     * @return Section
     */
    public function sections()
    {
        return $this->sections ?: $this->sections = new Section();
    }

    /**
     * @param array $options
     * @return Section
     */
    public function section($options = [])
    {

        $parent = array_get($options, 'parent');

        Section::unguard();

        $section = $this->sections()->create([
            'template' => array_get($options, 'template', 'default'),
            'parent_id' => $parent ? $parent->id : null,
            'owner_id' => $this->item->id,
            'owner_type' => $this->item->getMorphClass(),
            'sectionable_type' => array_get($options, 'sectionable_type', 'sections'),
            'sectionable_id' => array_get($options, 'sectionable_id'),
            'heading' => array_get($options, 'heading'),
            'before' => array_get($options, 'before'),
            'after' => array_get($options, 'after'),
        ]);

        return $section;
    }

}