<?php

namespace Belt\Content\Behaviors;

use Belt\Core\Behaviors\ParamableInterface;

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
     * @param $value
     *
     * @return string
     */
    public function getTemplateAttribute($value)
    {
        return $value ?: 'default';
    }

    /**
     * @return string
     */
    public function getTemplateConfigPrefix()
    {
        return sprintf('belt.templates.%s', $this->getTemplateGroup());
    }

    /**
     * @return string
     */
    public function getDefaultTemplateKey()
    {
        $prefix = $this->getTemplateConfigPrefix();

        $configs = config($prefix);

        if (isset($configs['default']) || !count($configs)) {
            return 'default';
        }

        return array_keys($configs)[0];
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
    public function getTemplateConfig()
    {
        $prefix = $this->getTemplateConfigPrefix();

        $config = config(sprintf('%s.%s', $prefix, $this->template));

        if (!$config) {
            $config = config(sprintf('%s.%s', $prefix, $this->getDefaultTemplateKey()));
        }

        if (!$config) {
            throw new \Exception("missing template config: $prefix.$this->template");
        }

        return $config;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTemplateViewAttribute()
    {
        $config = $this->getTemplateConfig();

        return is_array($config) ? array_get($config, 'path', array_get($config, 0)) : $config;
    }

    /**
     * @todo need other method to purge orphaned params
     */
    public function reconcileTemplateParams()
    {
        if (!$this instanceof ParamableInterface) {
            return;
        }

        $config = $this->getTemplateConfig();

        foreach (array_get($config, 'params', []) as $key => $values) {

            $values = array_keys($values);

            $param = $this->params->where('key', $key)->first() ?: $this->params()->create(['key' => $key]);

            if (!$param->value || !in_array($param->value, $values)) {
                $param->update(['value' => $values[0]]);
            }
        }
    }

}