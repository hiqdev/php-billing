<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
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

    /**
     * @var string default price class, when given will be used for not found types
     */
    protected $defaultClass = null;

    public function __construct(array $types = [], $defaultClass = null)
    {
        $this->types = $types;
        $this->defaultClass = $defaultClass;
    }

    /**
     * Creates price object.
     * @return Price
     */
    public function create(PriceCreationDto $dto)
    {
        $type = $dto->type->getName();
        $class = $this->findClassForType($type);
        $method = $this->findMethodForClass($class);

        return $this->{$method}($dto);
    }

    public function findClassForType($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }
        if ($this->defaultClass) {
            return $this->defaultClass;
        }
        throw new FailedCreatePriceException("unknown type: $type");
    }

    public function findMethodForClass($class)
    {
        if (isset($this->creators[$class])) {
            return $this->creators[$class];
        }
        throw new FailedCreatePriceException("unknown class: $class");
    }

    public function createEnumPrice(PriceCreationDto $dto)
    {
        return new EnumPrice($dto->id, $dto->type, $dto->target, $dto->plan, $dto->unit, $dto->currency, $dto->sums);
    }

    public function createSinglePrice(PriceCreationDto $dto)
    {
        return new SinglePrice($dto->id, $dto->type, $dto->target, $dto->plan, $dto->prepaid, $dto->price);
    }
}
