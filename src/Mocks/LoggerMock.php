<?php

namespace App\Mocks;

use Psr\Log\LoggerInterface;
use Stringable;

class LoggerMock implements LoggerInterface
{
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log('>>EMERGENCY: ', $message);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log('>>ALERT: ', $message);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log('>>WARNING: ', $message);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log('>>ERROR: ', $message);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log('>>INFO: ', $message);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log('>>CRITICAL: ', $message);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log('>>NOTICE: ', $message);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log('DEBUG: ', $message);
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        echo (string)$level . $message;
    }
}
