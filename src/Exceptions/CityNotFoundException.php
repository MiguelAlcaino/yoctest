<?php
/**
 * Created by PhpStorm.
 * User: malcaino
 * Date: 20/07/18
 * Time: 17:10
 */

namespace App\Exceptions;


use Throwable;

class CityNotFoundException extends YocTestException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}