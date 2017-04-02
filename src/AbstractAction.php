<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\units\QuantityInterface;

/**
 * Chargable Action.
 *
 * @see ActionInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var TargetInterface
     */
    protected $target;

    /**
     * @var QuantityInterface
     */
    protected $quantity;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @param ClientInterface $client
     * @param TargetInterface $target
     * @param QuantityInterface $quantity
     * @param DateTime $time
     */
    public function __construct(
        ClientInterface $client,
        TargetInterface $target,
        QuantityInterface $quantity,
        DateTime $time
    ) {
        $this->client = $client;
        $this->target = $target;
        $this->quantity = $quantity;
        $this->time = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCharge(PriceInterface $price)
    {
        if (!$this->isApplicable($price)) {
            return null;
        }

        $usage = $price->calculateUsage($this->getQuantity());
        if ($usage === null) {
            return null;
        }

        $sum = $price->calculateSum($this->getQuantity());
        if ($sum === null) {
            return null;
        }

        return new Charge($this, $price->getTarget(), $price->getType(), $usage, $sum);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function isApplicable(PriceInterface $price);
}
