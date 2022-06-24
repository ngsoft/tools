<?php

declare(strict_types=1);

/**
 * Symfony Event dispatcher interfaces polyfill
 */
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

namespace Symfony\Contracts\EventDispatcher {



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
