<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/aphiria/aphiria/blob/1.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Exceptions;

use Closure;
use Exception;
use Psr\Log\LogLevel;

/**
 * Defines a factory for PSR-3 log levels
 */
class LogLevelFactory
{
    /** @var array<class-string<Exception>, Closure(mixed): string> The mapping of exception types to log level factories */
    private array $logLevelFactories = [];

    /**
     * Creates a PSR-3 log level from an exception
     *
     * @param Exception $ex The exception that was thrown
     * @return string The PSR-3 log level
     */
    public function createLogLevel(Exception $ex): string
    {
        if (isset($this->logLevelFactories[$ex::class])) {
            return $this->logLevelFactories[$ex::class]($ex);
        }

        return LogLevel::ERROR;
    }

    /**
     * Registers an exception log level factory
     *
     * @param class-string<Exception> $exceptionType The exception whose factory we're registering
     * @param Closure(mixed): string $factory The factory that takes in an exception of the input type and returns a PSR-3 log level
     */
    public function registerLogLevelFactory(string $exceptionType, Closure $factory): void
    {
        $this->logLevelFactories[$exceptionType] = $factory;
    }

    /**
     * Registers an exception log level factory for an exception type
     *
     * @param array<class-string<Exception>, Closure(mixed): string> $exceptionTypesToFactories The exception types to factories
     */
    public function registerManyLogLevelFactories(array $exceptionTypesToFactories): void
    {
        foreach ($exceptionTypesToFactories as $exceptionType => $responseFactory) {
            $this->registerLogLevelFactory($exceptionType, $responseFactory);
        }
    }
}
