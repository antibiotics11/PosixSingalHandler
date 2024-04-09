<?php

namespace antibiotics11\PosixSignalManager;
use InvalidArgumentException;
use RuntimeException;
use function pcntl_async_signals, pcntl_signal, pcntl_signal_get_handler;
use function count, in_array, array_values, array_key_last;
use const SIG_DFL;

pcntl_async_signals(true);

class SignalManager {

  private static self $manager;

  /**
   * Get the singleton instance of SignalManager.
   *
   * @return SignalManager
   */
  public static function getManager(): self {
    self::$manager ??= new self();
    return self::$manager;
  }

  /** @var SignalHandler[][] */
  protected array $handlers = [];

  protected function registerSignalFunction(Signal $signal, ?callable $handler = null): void {
    if (!pcntl_signal($signal->value, $handler ?? SIG_DFL)) {
      throw new RuntimeException("pcntl_signal() failed for " . $signal->name);
    }
  }

  /**
   * Check if a handler exists for a specific signal.
   *
   * @param Signal $signal the signal to check.
   * @param SignalHandler|null $handler the specific handler to check for.
   *                                    if null, check if any handler is registered for the signal.
   * @return bool
   */
  public function hasHandler(Signal $signal, ?SignalHandler $handler = null): bool {

    if (!isset($this->handlers[$signal->value]) || count($this->handlers[$signal->value]) < 1) {
      return false;
    }

    return $handler === null || in_array($handler, $this->handlers[$signal->value]);

  }

  /**
   * Handle the signal by executing its registered handlers.
   *
   * @param Signal|int $signal the signal to handle.
   * @return void
   * @throws InvalidArgumentException if an undefined signal is provided.
   */
  public function handle(Signal|int $signal): void {

    if (!($signal instanceof Signal)) {
      $signalInstance = Signal::tryFrom($signal);
      if ($signalInstance === null) {
        throw new InvalidArgumentException("Undefined signal " . $signal);
      }
      $signal = $signalInstance;
    }

    if (!$this->hasHandler($signal)) {
      return;
    }

    foreach ($this->handlers[$signal->value] as $handler) {
      $handler->execute();
    }

  }

  /**
   * Add a handler for a specific signal.
   *
   * @param Signal $signal the signal to add the handler for.
   * @param SignalHandler $handler the handler to add.
   * @return void
   * @throws RuntimeException if registering the signal handler fails.
   * @throws InvalidArgumentException if the handler already exists.
   */
  public function addHandler(Signal $signal, SignalHandler $handler): void {

    $this->handlers[$signal->value] ??= [];

    if (pcntl_signal_get_handler($signal->value) == SIG_DFL) {
      $this->registerSignalFunction($signal, [ $this, "handle" ]);
    }

    if ($this->hasHandler($signal, $handler)) {
      throw new InvalidArgumentException("Handler already exists.");
    }

    $this->handlers[$signal->value][] = $handler;

  }

  /**
   * Get all handlers for a specific signal.
   *
   * @param Signal $signal
   * @return SignalHandler[]|null an array of handlers for the signal,
   *                              or null if no handlers are registered.
   */
  public function getHandlers(Signal $signal): array|null {
    return $this->handlers[$signal->value] ?? null;
  }

  /**
   * Remove a specific handler for a signal.
   *
   * @param Signal $signal the signal to remove the handler from.
   * @param SignalHandler|null $handler the handler to remove. if null, remove the last registered one.
   * @return void
   * @throws RuntimeException if unregistering the signal handler fails.
   */
  public function removeHandler(Signal $signal, ?SignalHandler $handler = null): void {

    if (!$this->hasHandler($signal)) {
      return;
    }

    $targetHandlerKey = array_key_last($this->handlers[$signal->value]);
    if ($targetHandlerKey === null) {
      $this->removeHandlers($signal);
      return;
    }

    // If a specific handler is provided
    if ($handler instanceof SignalHandler) {
      foreach ($this->handlers[$signal->value] as $key => $existingHandler) {
        if ($existingHandler === $handler) {
          $targetHandlerKey = $key;
          break;
        }
      }
    }

    unset($this->handlers[$signal->value][$targetHandlerKey]);
    $this->handlers[$signal->value] = array_values($this->handlers[$signal->value]);

    // If no handlers left, remove all handlers for the signal
    if (count($this->handlers[$signal->value]) < 1) {
      $this->removeHandlers($signal);
    }

  }

  /**
   * Remove all handlers for a specific signal.
   *
   * @param Signal $signal the signal to remove all handlers from.
   * @return void
   * @throws RuntimeException if unregistering the signal handler fails.
   */
  public function removeHandlers(Signal $signal): void {

    // Reset the signal handler to its default behavior
    $this->registerSignalFunction($signal);

    if (isset($this->handlers[$signal->value])) {
      unset($this->handlers[$signal->value]);
    }

  }

  private function __construct() {}
  private function __clone(): void {}

}
