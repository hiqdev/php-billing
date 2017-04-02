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
 * Simple Action.
 * Charges only same target and same type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SimpleAction extends AbstractAction
{
    /**
     * @param ClientInterface $client
     * @param TargetInterface $target
     * @param QuantityInterface $quantity
     * @param DateTime $time
     * @param TypeInterface $type
     */
    public function __construct(
        ClientInterface $client,
        TargetInterface $target,
        QuantityInterface $quantity,
        DateTime $time,
        TypeInterface $type
    ) {
        parent::__construct($client, $target, $quantity, $time);
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(PriceInterface $price)
    {
        return $this->target->equals($price->getTarget()) &&
            $this->getType()->equals($price->getType());
    }
}
