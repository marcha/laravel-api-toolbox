<?php

namespace Erpmonster\Transformers;

use League\Fractal\Serializer\ArraySerializer;

class ResourceKeyDataSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }

    public function null()
    {
        return [];
    }
}
