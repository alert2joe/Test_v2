<?php 

namespace Evenement;
use InvalidArgumentException;

class EventEmitter 
{

    static  $listeners = [];
    static  $onceListeners = [];

    static  public function on($event, callable $listener)
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (!isset(self::$listeners[$event])) {
            self::$listeners[$event] = [];
        }

        self::$listeners[$event][] = $listener;

        return true;
    }

    static public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset(self::$listeners[$event]);
        } else {
            self::$listeners = [];
        }

    }

    static public function emit($event, array $arguments = [])
    {
        if ($event === null) {
            throw new InvalidArgumentException('event name must not be null');
        }

        if (isset(self::$listeners[$event])) {
            foreach (self::$listeners[$event] as $listener) {
                $listener(...$arguments);
            }
        }


    }
}
