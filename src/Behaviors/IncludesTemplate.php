<?php
namespace Belt\Content\Behaviors;

/**
 * Class IncludesTemplate
 * @package Belt\Content\Behaviors
 */
trait IncludesTemplate
{

    /**
     * @param $value
     */
    public function setTemplateAttribute($value)
    {
        $this->attributes['template'] = trim(strtolower($value));
    }

    /**
     * @return mixed
     */
    public function getTemplateGroup()
    {
        return $this->getMorphClass();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTemplateViewAttribute()
    {
        $key = sprintf('belt.templates.%s', $this->getTemplateGroup());

        $config = config("$key.$this->template") ?: config("$key.default");

        if (!$config) {
            throw new \Exception("missing template view: $key.$this->template");
        }

        return is_array($config) ? $config[0] : $config;
    }

}