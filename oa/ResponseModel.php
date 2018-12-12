<?php

namespace OA;

use DigitSoft\Swagger\DumperYaml;
use DigitSoft\Swagger\ModelParser;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ResponseModel extends Response
{
    public $with;

    /**
     * @inheritdoc
     */
    protected function getContent()
    {
        if ($this->content === null || !class_exists($this->content)) {
            throw new \Exception("Model '{$this->content}' not found");
        }
        return [
            'type' => 'object',
            'properties' => $this->getModelProperties(),
        ];
    }

    /**
     * Get model properties
     * @return array
     */
    protected function getModelProperties()
    {
        $parser = new ModelParser($this->content);
        $properties = $parser->properties(true);
        $propertiesRead = [];
        if (($with = $this->getWith()) !== null) {
            $propertiesRead = $parser->propertiesRead($with);
        }
        $properties = array_merge($properties, $propertiesRead);
        $result = [];
        foreach ($properties as $varName => $varData) {
            $result[$varName] = $this->describeProperty($varName, $varData);
        }
        return $result;
    }

    /**
     * Get property-read names
     * @return array
     */
    protected function getWith()
    {
        if (is_string($this->with)) {
            return $this->with = [$this->with];
        }
        return $this->with;
    }

    /**
     * Describe model property
     * @param string $propName
     * @param array $propData
     * @return array
     */
    private function describeProperty($propName, $propData = [])
    {
        if (isset($propData['properties'])) {
            foreach ($propData['properties'] as $propNameNested => $propDataNested) {
                $propData['properties'][$propNameNested] = $this->describeProperty($propNameNested, $propDataNested);
            }
        } else {
            $propValue = DumperYaml::getExampleValue($propData['type'], $propName);
            $propData = array_merge($propData, DumperYaml::describe($propValue));
        }
        return $propData;
    }
}
