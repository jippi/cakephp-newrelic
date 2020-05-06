<?php
declare(strict_types=1);

namespace NewRelic\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware;
use NewRelic\Lib\NewRelic;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * {@inheritDoc}
 */
class NewRelicErrorHandlerMiddleware extends ErrorHandlerMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function handleException(Throwable $exception, ServerRequestInterface $request): ResponseInterface
    {
        NewRelic::collect();
        NewRelic::sendException($exception);

        return parent::handleException($exception, $request);
    }
}
