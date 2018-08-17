<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class DataRateUnitException extends Exception
{
    protected $message = 'Invalid unit.';
}
