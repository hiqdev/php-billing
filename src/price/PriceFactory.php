<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
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
        RatePrice::class    => 'createRatePrice',
        SinglePrice::class  => 'createSinglePrice',
        ProgressivePrice::class => 'createProgressivePrice',
    ];

    protected $types = [
        'enum'      => EnumPrice::class,
        'single'    => SinglePrice::class,
        'progressive' => ProgressivePrice::class,
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


    public function create(PriceCreationDto $dto): PriceInterface
    {
        $class = $this->findClassForTypes([
            get_class($dto),
            $dto->type->getName(),
        ]);
        $method = $this->findMethodForClass($class);

        return $this->{$method}($dto);
    }

    public function findClassForTypes(array $types)
    {
        foreach ($types as $type) {
            if (isset($this->types[$type])) {
                return $this->types[$type];
            }
        }
        if ($this->defaultClass) {
            return $this->defaultClass;
        }
        throw new FailedCreatePriceException(sprintf('unknown types: "%s"', implode(',', $types)));
    }

    public function findMethodForClass($class)
    {
        if (isset($this->creators[$class])) {
            return $this->creators[$class];
        }
        throw new FailedCreatePriceException("unknown class: $class");
    }

    public function createEnumPrice(PriceCreationDto $dto): EnumPrice
    {
        return new EnumPrice($dto->id, $dto->type, $dto->target, $dto->plan, $dto->unit, $dto->currency, new Sums($dto->sums));
    }

    public function createRatePrice(PriceCreationDto $dto)
    {
        return new RatePrice($dto->id, $dto->type, $dto->target, $dto->plan, $dto->rate);
    }

    public function createSinglePrice(PriceCreationDto $dto)
    {
        return new SinglePrice($dto->id, $dto->type, $dto->target, $dto->plan, $dto->prepaid, $dto->price);
    }

    public function createProgressivePrice(PriceCreationDto $dto): ProgressivePrice
    {
        $thresholds = ProgressivePriceThresholdList::fromScalarsArray($dto->thresholds);

        return new ProgressivePrice($dto->id, $dto->type, $dto->target, $dto->prepaid, $dto->price, $thresholds, $dto->plan);
    }
}
