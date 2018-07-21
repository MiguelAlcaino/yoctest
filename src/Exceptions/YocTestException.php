<?php
/**
 * Created by PhpStorm.
 * User: malcaino
 * Date: 20/07/18
 * Time: 17:07
 */

namespace App\Exceptions;


use Throwable;

class YocTestException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}