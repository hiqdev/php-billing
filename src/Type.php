<?php

namespace hiqdev\php\billing;

/**
 * Resource Type.
 */
class Type
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * Finds objects matching the resource type and given object.
     * Default behavior: just the object itself.
     * E.g.:
     * - for server these are all it's hardware parts.
     * - for domain these is it's zone
     * @return Object[]
     */
    public function findMatchingObjects()
    {
        return [$this->object];
    }

}
