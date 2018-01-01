<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Hydrator Trait.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait HydratorTrait
{
    public static function create(array $data)
    {
        return (new static())->hydrate($data);
    }

    protected $properties = [];

    protected function hydrate(array $data)
    {
        foreach ($def as $key => $creator) {
            if (isset($data[$key])) {
                if ($creator && is_array($data[$key])) {
                    $this->{$key} = call_user_func($creator, $data[$key]);
                } else {
                    $this->{$key} = $data[$key];
                }
            }
        }
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['target'])) {
            $this->target = is_array($data['target']) ? AbstractTarget::create($data['target']) : $data['target'];
        }

        return $this;
    }
}
