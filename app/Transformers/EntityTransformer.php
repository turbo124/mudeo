<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class EntityTransformer extends TransformerAbstract
{
    protected $serializer;

    const API_SERIALIZER_ARRAY = 'array';
    const API_SERIALIZER_JSON = 'json';

    public function __construct($serializer = null)
    {
        $this->serializer = $serializer;
    }

    protected function includeCollection($data, $transformer, $entityType)
    {
        if ($this->serializer && $this->serializer != self::API_SERIALIZER_JSON) {
            $entityType = null;
        }

        return $this->collection($data, $transformer, $entityType);
    }

    protected function includeItem($data, $transformer, $entityType)
    {
        if ($this->serializer && $this->serializer != self::API_SERIALIZER_JSON) {
            $entityType = null;
        }

        return $this->item($data, $transformer, $entityType);
    }

    public function getDefaultIncludes()
    {
        return $this->defaultIncludes;
    }

    protected function getDefaults($entity)
    {

    }
}
