<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';

/*
 * Ensures compatibility with PHPUnit 5.x
 */
if (!class_exists(\PHPUnit\Framework\TestCase::class) && class_exists(\PHPUnit_Framework_TestCase::class)) {
    namespace \PHPUnit\Framework;
    abstract class TestCase extends \PHPUnit_Framework_TestCase {};
}
