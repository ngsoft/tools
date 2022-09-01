<?php

declare(strict_types=1);

/*
  Copyright (c) 2018-2022 Fabien Potencier

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is furnished
  to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */

/**
 * Symfony Event dispatcher interfaces polyfill
 *
 *
 * @phan-file-suppress PhanTypeMismatchDeclaredParam, PhanTypeMismatchDeclaredReturn, PhanTemplateTypeNotDeclaredInFunctionParams, PhanRedefineClass
 */

namespace Symfony\Contracts\EventDispatcher
{


    // if symfony/event-dispatcher imported no need to load file
    if (interface_exists(EventDispatcherInterface::class)) {
        return;
    }

    /*
     * This file is part of the Symfony package.
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    use Psr\EventDispatcher\{
        EventDispatcherInterface as PsrEventDispatcherInterface, StoppableEventInterface
    };

    /**
     * Allows providing hooks on domain-specific lifecycles by dispatching events.
     */
    interface EventDispatcherInterface extends PsrEventDispatcherInterface
    {

        /**
         * Dispatches an event to all registered listeners.
         *
         * @template T of object
         *
         * @param T           $event     The event to pass to the event handlers/listeners
         * @param string|null $eventName The name of the event to dispatch. If not supplied,
         *                               the class of $event should be used instead.
         *
         * @return T The passed $event MUST be returned
         */
        public function dispatch(object $event, string $eventName = null): object;
    }

    /**
     * Event is the base class for classes containing event data.
     *
     * This class contains no event data. It is used by events that do not pass
     * state information to an event handler when an event is raised.
     *
     * You can call the method stopPropagation() to abort the execution of
     * further listeners in your event listener.
     *
     * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
     * @author Jonathan Wage <jonwage@gmail.com>
     * @author Roman Borschel <roman@code-factory.org>
     * @author Bernhard Schussek <bschussek@gmail.com>
     * @author Nicolas Grekas <p@tchwork.com>
     */
    class Event implements StoppableEventInterface
    {

        private bool $propagationStopped = false;

        /**
         * {@inheritdoc}
         */
        public function isPropagationStopped(): bool
        {
            return $this->propagationStopped;
        }

        /**
         * Stops the propagation of the event to further event listeners.
         *
         * If multiple event listeners are connected to the same event, no
         * further event listener will be triggered once any trigger calls
         * stopPropagation().
         */
        public function stopPropagation(): void
        {
            $this->propagationStopped = true;
        }

    }

}

namespace Symfony\Component\EventDispatcher
{

    use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

    /**
     * The EventDispatcherInterface is the central point of Symfony's event listener system.
     * Listeners are registered on the manager and events are dispatched through the
     * manager.
     *
     * @author Bernhard Schussek <bschussek@gmail.com>
     */
    interface EventDispatcherInterface extends ContractsEventDispatcherInterface
    {

        /**
         * Adds an event listener that listens on the specified events.
         *
         * @param int $priority The higher this value, the earlier an event
         *                      listener will be triggered in the chain (defaults to 0)
         */
        public function addListener(string $eventName, callable $listener, int $priority = 0);

        /**
         * Adds an event subscriber.
         *
         * The subscriber is asked for all the events it is
         * interested in and added as a listener for these events.
         */
        public function addSubscriber(EventSubscriberInterface $subscriber);

        /**
         * Removes an event listener from the specified events.
         */
        public function removeListener(string $eventName, callable $listener);

        public function removeSubscriber(EventSubscriberInterface $subscriber);

        /**
         * Gets the listeners of a specific event or all listeners sorted by descending priority.
         *
         * @return array<callable[]|callable>
         */
        public function getListeners(string $eventName = null): array;

        /**
         * Gets the listener priority for a specific event.
         *
         * Returns null if the event or the listener does not exist.
         */
        public function getListenerPriority(string $eventName, callable $listener): ?int;

        /**
         * Checks whether an event has any registered listeners.
         */
        public function hasListeners(string $eventName = null): bool;
    }

    /**
     * An EventSubscriber knows itself what events it is interested in.
     * If an EventSubscriber is added to an EventDispatcherInterface, the manager invokes
     * {@link getSubscribedEvents} and registers the subscriber as a listener for all
     * returned events.
     *
     * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
     * @author Jonathan Wage <jonwage@gmail.com>
     * @author Roman Borschel <roman@code-factory.org>
     * @author Bernhard Schussek <bschussek@gmail.com>
     */
    interface EventSubscriberInterface
    {

        /**
         * Returns an array of event names this subscriber wants to listen to.
         *
         * The array keys are event names and the value can be:
         *
         *  * The method name to call (priority defaults to 0)
         *  * An array composed of the method name to call and the priority
         *  * An array of arrays composed of the method names to call and respective
         *    priorities, or 0 if unset
         *
         * For instance:
         *
         *  * ['eventName' => 'methodName']
         *  * ['eventName' => ['methodName', $priority]]
         *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
         *
         * The code must not depend on runtime state as it will only be called at compile time.
         * All logic depending on runtime state must be put into the individual methods handling the events.
         *
         * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
         */
        public static function getSubscribedEvents();
    }

}

namespace Symfony\Component\EventDispatcher\Attribute
{

    /**
     * Service tag to autoconfigure event listeners.
     *
     * @author Alexander M. Turek <me@derrabus.de>
     */
    #[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
    class AsEventListener
    {

        public function __construct(
                public ?string $event = null,
                public ?string $method = null,
                public int $priority = 0,
                public ?string $dispatcher = null,
        )
        {

        }

    }

}