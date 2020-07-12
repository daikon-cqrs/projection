<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/read-model project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\ReadModel\Projection;

use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\Interop\RuntimeException;
use ReflectionClass;

trait EventHandlerTrait
{
    /** @return static */
    public function applyEvent(DomainEventInterface $event): ProjectionInterface
    {
        return $this->invokeEventHandler($event);
    }

    /** @return static */
    protected function invokeEventHandler(DomainEventInterface $event): ProjectionInterface
    {
        $handlerName = (new ReflectionClass($event))->getShortName();
        $handlerMethod = 'when'.ucfirst($handlerName);
        $projection = clone $this;
        $handler = [$projection, $handlerMethod];
        if (!is_callable($handler)) {
            throw new RuntimeException(
                sprintf("Handler '%s' is not callable on '%s'.", $handlerMethod, static::class)
            );
        }
        return $handler($event);
    }
}
