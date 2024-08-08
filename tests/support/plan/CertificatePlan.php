<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\plan;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\price\Sums;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Unit;
use Money\Currency;

class CertificatePlan extends Plan
{
    protected static $instance;

    /** @var Customer */
    public $customer;
    /** @var Type */
    public $purchase;
    /** @var Type */
    public $renewal;
    /** @var Target */
    public $rapidssl;
    /** @var Target */
    public $verisign;
    /** @var array */
    public $types;
    /** @var array */
    public $rawPrices;
    /** @var Target[] */
    public $targets;

    public static function get()
    {
        if (static::$instance === null) {
            new static();
        }

        return static::$instance;
    }

    public function __construct()
    {
        if (static::$instance === null) {
            static::$instance = $this;
        }
        $this->seller   = new Customer(1, 'seller');
        $this->customer = new Customer(2, 'client', $this->seller);
        $this->purchase = new Type(1, 'certificate_purchase');
        $this->renewal  = new Type(2, 'certificate_renewal');
        $this->rapidssl = new Target('rapidssl_standard', 'certificate_type');
        $this->verisign = new Target('verisign_standard', 'certificate_type');
        $this->types = [
            'purchase'  => $this->purchase,
            'renewal'   => $this->renewal,
        ];
        $this->targets = [
            'rapidssl'  => $this->rapidssl,
            'verisign'  => $this->verisign,
        ];
        $this->rawPrices = [
            $this->getRawPriceKey($this->purchase, $this->rapidssl) => [
                1 => 1129,
                2 => 1219,
                3 => 1309,
            ],
            $this->getRawPriceKey($this->renewal, $this->rapidssl) => [
                1 => 1125,
                2 => 1215,
                3 => 1305,
            ],
            $this->getRawPriceKey($this->purchase, $this->verisign) => [
                1 => 2129,
                2 => 2219,
                3 => 2309,
            ],
            $this->getRawPriceKey($this->renewal, $this->verisign) => [
                1 => 2125,
                2 => 2215,
                3 => 2305,
            ],
        ];
        $prices = [];
        foreach ($this->types as $type) {
            foreach ($this->targets as $target) {
                $prices[] = new EnumPrice(null, $type, $target, null, Unit::year(), new Currency('USD'), $this->getRawPrices($type, $target));
            }
        }
        parent::__construct(null, 'Test Certificate Plan', $this->seller, $prices);
    }

    /**
     * @param Action $action
     * @return mixed
     */
    public function getRawPrice($action)
    {
        $years = $action->getQuantity()->convert(Unit::year())->getQuantity();

        return $this->getRawPrices($action->getType(), $action->getTarget())[$years];
    }

    public function getRawPrices($type, $target): Sums
    {
        return new Sums($this->rawPrices[$this->getRawPriceKey($type, $target)]);
    }

    /**
     * @param Type $type
     * @param Target $target
     * @return string
     */
    public function getRawPriceKey($type, $target)
    {
        return $type->getName() . '-' . $target->getUniqueId();
    }
}
