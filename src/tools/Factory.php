<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use Money\Money;
use Money\Currency;
use hiqdev\php\units\Quantity;
use Money\Parser\DecimalMoneyParser;
use Money\Currencies\ISOCurrencies;

/**
 * Generalized entity factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Factory
{
    private $entities = [];

    private $factories = [];

    protected $moneyParser;

    public function __construct(array $factories)
    {
        $this->factories = $factories;
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
    }

    public function getMoney($data)
    {
        if (is_array($data)) {
            $sum = $data['sum'];
            $currency = $data['currency'];
        } else {
            [$sum, $currency] = explode(' ', $data);
        }

        return $this->moneyParser->parse($sum, $currency);
    }

    public function getCurrency($data)
    {
        return new Currency($data);
    }

    public function getQuantity($data)
    {
        [$quantity, $unit] = explode(' ', $data);

        return Quantity::create($unit, $quantity);
    }

    public function getType($data)
    {
        return $this->get('type', $data);
    }

    public function getTarget($data)
    {
        return $this->get('target', $data);
    }

    public function getPlan($data)
    {
        return $this->get('plan', $data);
    }

    public function getCustomer($data)
    {
        return $this->get('customer', $data);
    }

    public function get(string $entity, $data)
    {
        if (is_scalar($data)) {
            $data = [$this->getEntityKey($entity) => $data];
        }
        $keys = $this->extractKeys($entity, $data);

        $res = $this->find($entity, $keys) ?: $this->create($entity, $data);

        foreach ($keys as $key) {
            $this->entities[$entity][$key] = $res;
        }

        return $res;
    }

    public function find(string $entity, array $keys)
    {
        foreach ($keys as $key) {
            if (!empty($this->entities[$entity][$key])) {
                return $this->entities[$entity][$key];
            }
        }

        return null;
    }

    public function create(string $entity, $data)
    {
        if (empty($this->factories[$entity])) {
            throw new FactoryNotFoundException($entity);
        }

        $factory = $this->factories[$entity];

        return $factory->create($this->createDto($entity, $data));
    }

    public function createDto(string $entity, array $data)
    {
        $class = $this->getDtoClass($entity);
        $dto = new $class();

        foreach ($data as $key => $value) {
            $dto->{$key} = $this->prepareValue($entity, $key, $value);
        }

        return $dto;
    }

    public function getDtoClass(string $entity)
    {
        return $this->getEntityClass($entity) . 'CreationDto';
    }

    public function prepareValue($entity, $key, $value)
    {
        $method = $this->getPrepareMethod($entity, $key);

        return $method ? $this->{$method}($value) : $value;
    }

    private function getPrepareMethod(string $entity, string $key)
    {
        if ($key === 'seller') {
            return 'getCustomer';
        }
        switch ($key) {
            case 'seller':
                return 'getCustomer';
            case 'plan':
                return 'getPlan';
            case 'type':
                return 'getType';
            case 'target':
                return 'getTarget';
            case 'price':
                return 'getMoney';
            case 'currency':
                return 'getCurrency';
            case 'prepaid':
                return 'getQuantity';
        }

        return null;
    }

    public function getEntityClass(string $entity)
    {
        $parts = explode('\\', __NAMESPACE__);
        array_pop($parts);
        $parts[] = $entity;
        $parts[] = ucfirst($entity);

        return implode('\\', $parts);
    }

    public function extractKeys(string $entity, $data)
    {
        $id = $data['id'] ?? null;
        $key = $this->extractKey($entity, $data);

        return array_filter(['id' => $id, 'key' => $key]);
    }

    public function extractKey(string $entity, $data)
    {
        $key = $this->getEntityKey($entity);

        return $data[$key] ?? null;
    }

    public function getEntityKey(string $entity): string
    {
        switch ($entity) {
            case 'customer':
                return 'login';
            case 'type':
                return 'name';
            case 'plan':
                return 'name';
            case 'price':
                return 'id';
            case 'target':
                return 'id';
        }

        throw new UnknownEntityException($entity);
    }
}
