<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
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
        if ($previous !== null) {
            if ($message !== null) {
                $message .= ': ';
            }

            $message .= $previous->getMessage();
        }

        $exception = new static($message, 0, $previous);
        if ($formula !== null) {
            $exception->formula = $formula;
        }

        return $exception;
    }

    public static function create(string $formula, string $message)
    {
        $exception = new static($message);

        if ($formula !== null) {
            $exception->formula = $formula;
        }

        return $exception;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }
}
