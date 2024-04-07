<?php

namespace antibiotics11\PosixSignalManager;
use RuntimeException;
use function count, array_push, array_values, array_key_last;
use function pcntl_async_signals, pcntl_signal, pcntl_signal_get_handler;
use const SIG_DFL;

pcntl_async_signals(true);

class SignalManager {

  private static self $manager;
  public static function getManager(): self {
    self::$manager ??= new self();
    return self::$manager;
  }

  /** @var SignalHandler[][] */
  protected array $handlers = [];

  /**
   * Check if handlers exist for a specific signal.
   *
   * @param Signal $signal
   * @return bool
   */
  public function handlerExists(Signal $signal): bool {
    return isset($this->handlers[$signal->value]) && count($this->handlers[$signal->value]) > 0;
  }

  /**
   * Handle the signal by executing its handlers.
   *
   * @param Signal|int $signal
   * @return void
   */
  public function handleSignal(Signal|int $signal): void {

    if (!($signal instanceof Signal)) {
      $signal = Signal::from($signal);
    }

    if (!$this->handlerExists($signal)) {
      return;
    }

    foreach ($this->handlers[$signal->value] as $handler) {
      $handler->execute();
    }

  }

  /**
   * Add a handler for a specific signal.
   *
   * @param Signal $signal
   * @param SignalHandler $signalHandler
   * @return int
   * @throws RuntimeException
   */
  public function addHandler(Signal $signal, SignalHandler $signalHandler): int {

    $this->handlers[$signal->value] ??= [];

    if (pcntl_signal_get_handler($signal->value) == SIG_DFL) {
      if (!pcntl_signal($signal->value, [ $this, "handleSignal"])) {
        throw new RuntimeException("pcntl_signal failed for " . $signal->name);
      }
    }

    return array_push($this->handlers[$signal->value], $signalHandler) - 1;

  }

  /**
   * Get all handlers for a specific signal.
   *
   * @param Signal $signal
   * @return SignalHandler[]|null
   */
  public function getHandlers(Signal $signal): array|null {
    return $this->handlers[$signal->value] ?? null;
  }

  /**
   * Remove a specific handler for a signal.
   *
   * @param Signal $signal
   * @param SignalHandler|null $signalHandler
   * @return void
   * @throws RuntimeException
   */
  public function removeHandler(Signal $signal, ?SignalHandler $signalHandler = null): void {

    if (!$this->handlerExists($signal)) {
      return;
    }

    $targetHandlerKey = array_key_last($this->handlers[$signal->value]);
    if ($targetHandlerKey === null) {
      $this->removeHandlers($signal);
      return;
    }

    // If a specific handler is provided
    if ($signalHandler instanceof SignalHandler) {
      foreach ($this->handlers[$signal->value] as $key => $handler) {
        if ($handler === $signalHandler) {
          $targetHandlerKey = $key;
          break;
        }
      }
    }

    unset($this->handlers[$signal->value][$targetHandlerKey]);
    $this->handlers[$signal->value] = array_values($this->handlers[$signal->value]);

    // If no handlers left, remove all handlers for the signal
    if (count($this->handlers[$signal->value]) == 0) {
      $this->removeHandlers($signal);
    }

  }

  /**
   * Remove all handlers for a specific signal.
   *
   * @param Signal $signal
   * @return void
   * @throws RuntimeException
   */
  public function removeHandlers(Signal $signal): void {

    if (!$this->handlerExists($signal)) {
      return;
    }

    unset($this->handlers[$signal->value]);

    // Reset the signal handler to its default behavior
    if (!pcntl_signal($signal->value, SIG_DFL)) {
      throw new RuntimeException("pcntl_signal failed for " . $signal->name);
    }

  }

  /**
   * Remove all handlers for all signals.
   *
   * @return void
   * @throws RuntimeException
   */
  public function removeAllHandlers(): void {

    foreach ($this->handlers as $signal => $handlers) {
      $this->removeHandlers(Signal::from($signal));
    }

    $this->handlers = [];

  }

  private function __construct() {}
  private function __clone(): void {}

}
