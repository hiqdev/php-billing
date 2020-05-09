<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\formula;

use Exception;
use Throwable;

/**
 * Class FormulaEngineException.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaEngineException extends Exception
{
    /**
     * @var string
     */
    private $formula;

    public static function fromException(Throwable $previous, string $formula, string $message = null): FormulaEngineException
    {
        if ($message !== null) {
            $message .= ': ';
        }

        $message .= $previous->getMessage();

        $exception = new static($message . ' : ' . $formula, 0, $previous);
        $exception->formula = $formula;

        return $exception;
    }

    public static function create(string $formula, string $message)
    {
        $exception = new static($message . ' : ' . $formula);
        $exception->formula = $formula;

        return $exception;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }
}
