<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

/**
 * Default price factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PriceFactory implements PriceFactoryInterface
{
    protected $creators = [
        EnumPrice::class    => 'createEnumPrice',
        SinglePrice::class  => 'createSinglePrice',
    ];

    protected $types = [
        'enum'      => EnumPrice::class,
        'single'    => SinglePrice::class,
    ];

    public function __construct(array $types = []) {
        $this->types = $types;
    }

    /**
     * Creates price object.
     * @return Price
     */
    public function create(PriceCreationDto $dto)
    {
        $type = $dto->type->getName();
        if (!isset($this->types[$type])) {
            throw new FailedCreatePriceException("unknown type: $type");
        }
        $class = $this->types[$type];
        if (!isset($this->creators[$class])) {
            throw new FailedCreatePriceException("unknown class: $class");
        }
        $method = $this->creators[$class];
        return $this->{$method}($dto);
    }

    public function createEnumPrice(PriceCreationDto $dto)
    {
        return new EnumPrice($dto->id, $dto->type, $dto->target, $dto->unit, $dto->prices);
    }

    public function createSinglePrice(PriceCreationDto $dto)
    {
        return new SinglePrice($dto->id, $dto->type, $dto->target, $dto->prepaid, $dto->price);
    }
}
