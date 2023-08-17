<?php

namespace Signal;
use Closure;

declare(ticks = 1);
pcntl_async_signals(true);

class PosixSignalHandler {

  private static Array $handlers = [];

  private static function getSignalValue(PosixSignal|int $signal): int {
    return ($signal instanceof PosixSignal) ? $signal->value : $signal;
  }

  /**
   * Call the handler function associated with the specified signal.
   *
   * @param PosixSignal|int $signal The PosixSignal instance or integer value.
   */
  public static function handleSignal(PosixSignal|int $signal): void {

    $signalValue = self::getSignalValue($signal);
    if (!self::isRegistered($signalValue)) {
      return;
    }

    $handler = self::$handlers[$signalValue]["handler"];
    $params = self::$handlers[$signalValue]["params"];
    $handler($params);

  }

  /**
   * Check if a signal has been registered with a valid Closure handler.
   *
   * @param PosixSignal|int $signal The PosixSignal instance or integer value.
   * @return bool True if the signal is registered with a valid handler, False otherwise.
   */
  public static function isRegistered(PosixSignal|int $signal): bool {

    $signalValue = self::getSignalValue($signal);
    if (isset(self::$handlers[$signalValue])) {
      if (self::$handlers[$signalValue]["handler"] instanceof Closure) {
        return true;
      }
    }
    return false;

  }

  /**
   * Register a signal handler for the specified signal.
   *
   * @param PosixSignal|int $signal The PosixSignal instance or integer value.
   * @param Closure $handler The handler function to be associated with the signal.
   * @param Array $params Optional parameters to be passed to the handler function when called.
   * @return bool True if the signal handler was successfully registered, False otherwise.
   */
  public static function register(PosixSignal|int $signal, Closure $handler, Array $params = []): bool {

    $signalValue = self::getSignalValue($signal);
    if (self::isRegistered($signalValue)) {
      self::unregister($signalValue);
    }

    self::$handlers[$signalValue] = [ 
      "handler" => $handler, 
      "params"  => $params 
    ];
    return pcntl_signal($signalValue, [ self::class, "handleSignal" ]);

  }

  /**
   * Unregister a previously registered signal handler.
   *
   * @param PosixSignal|int $signal The PosixSignal instance or integer value.
   * @return bool True if the signal handler was successfully unregistered, False otherwise.
   */
  public static function unregister(PosixSignal|int $signal): bool {

    $signalValue = self::getSignalValue($signal);
    if (!self::isRegistered($signalValue)) {
      return false;
    }

    unset(self::$handlers[$signalValue]);
    return pcntl_signal($signalValue, SIG_DFL);

  }

};
