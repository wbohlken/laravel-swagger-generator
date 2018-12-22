<?php

namespace OA;

use DigitSoft\Swagger\DumperYaml;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Illuminate\Support\Arr;

/**
 * Used to declare controller action parameter
 *
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 * @Attributes({
 *   @Attribute("name", type="string"),
 *   @Attribute("type", type="string"),
 *   @Attribute("in", type="string"),
 *   @Attribute("description", type="string"),
 * })
 */
class Parameter extends BaseValueDescribed
{
    public $in = 'path';

    public $type = 'integer';

    public $required = true;

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data['in'] = $this->in;
        Arr::set($data, 'schema.type', $data['type']);
        Arr::forget($data, 'type');
        return $data;
    }
}
