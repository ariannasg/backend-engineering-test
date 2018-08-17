<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MetricsDataNotFoundException extends Exception
{
    protected $message = 'Metrics data not found in file. Make sure that the file content and format are valid';
}
