<?php
declare(strict_types=1);

namespace App\Resource;

abstract class Resource
{
    public abstract function toArray($item): array;

    /**
     * Transform the resource into a JSON array.
     *
     * @param mixed $resource
     * @return array
     */
    public function transform($resource): array
    {
        if (is_array($resource)) {
            return array_map(function ($item) {
                return $this->toArray($item);
            }, $resource);
        }

        return $this->toArray($resource);
    }

}