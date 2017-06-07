<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\plan;

use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Unit;
use Money\Money;

class CertificatePlan extends Plan
{
    protected static $instance;

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
        $this->purchase = new Type('certificate_purchase');
        $this->renewal  = new Type('certificate_renewal');
        $this->rapidssl = new Target('certificate_type', 'rapidssl_standard');
        $this->verisign = new Target('certificate_type', 'verisign_standard');
        $this->types = [
            'purchase'  => $this->purchase,
            'renewal'   => $this->renewal,
        ];
        $this->targets = [
            'rapidssl'  => $this->rapidssl,
            'verisign'  => $this->verisign,
        ];
        $this->rawPrices = [
            'purchase_rapidssl' => [
                1 => Money::USD(1129),
                2 => Money::USD(1219),
                3 => Money::USD(1309),
            ],
            'renewal_rapidssl' => [
                1 => Money::USD(1125),
                2 => Money::USD(1215),
                3 => Money::USD(1305),
            ],
            'purchase_verisign' => [
                1 => Money::USD(2129),
                2 => Money::USD(2219),
                3 => Money::USD(2309),
            ],
            'renewal_verisign' => [
                1 => Money::USD(2125),
                2 => Money::USD(2215),
                3 => Money::USD(2305),
            ],
        ];
        $prices = [];
        foreach ($this->types as $typeName => $type) {
            foreach ($this->targets as $targetName => $target) {
                $prices[] = new EnumPrice(null, $type, $target, Unit::year(), $this->getRawPrices($typeName, $targetName));
            }
        }
        parent::__construct(null, 'Test Certificate Plan', $this->seller, $prices);
    }

    public function getRawPrices($typeName, $targetName)
    {
        return $this->rawPrices[$typeName . '_' . $targetName];
    }
}
