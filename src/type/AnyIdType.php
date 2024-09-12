<?php declare(strict_types=1);

namespace hiqdev\php\billing\type;

/**
 * AnyIdType class.
 *
 * This class simplifies the creation of Type instances where the ID is always
 *  set to `Type::ANY`, which is used to represent a wildcard or "any" match for the ID.
 *  It reduces the repetitive creation of Type objects with `Type::ANY` as the ID.
 *
 *  Example:
 *  Instead of:
 *      new Type(Type::ANY, 'monthly,leasing');
 *  You can use:
 *      new AnyIdType('monthly,leasing');
 */
class AnyIdType extends Type
{
    public function __construct($name)
    {
        parent::__construct(self::ANY, $name);
    }
}
