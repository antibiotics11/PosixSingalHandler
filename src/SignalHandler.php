<?php

namespace antibiotics11\PosixSignalManager;
use Throwable;

class SignalHandler {

  protected const STATE_IDLE     = 0;
  protected const STATE_RUNNING  = 1;
  protected const STATE_FINISHED = 2;

  protected            $callback;
  protected ?Throwable $callbackError;
  protected array      $arguments;
  protected mixed      $result;
  protected int        $state;

  /**
   * @param callable $callback The callback function to execute.
   * @param mixed ...$arguments Optional arguments to pass to the callback function.
   */
  public function __construct(callable $callback, ... $arguments) {
    $this->callback      = $callback;
    $this->callbackError = null;
    $this->arguments     = $arguments;
    $this->result        = null;
    $this->state         = self::STATE_IDLE;
  }

  /**
   * Execute the callback function.
   *
   * @return void
   */
  public function execute(): void {

    $this->callbackError = null;
    $this->state         = self::STATE_RUNNING;

    try {
      $this->result = ($this->callback)(... $this->arguments);
      $this->state  = self::STATE_FINISHED;
    } catch (Throwable $e) {
      $this->callbackError = $e;
      $this->result        = null;
      $this->state         = self::STATE_IDLE;
    }

  }

  public function __invoke(): void {
    $this->execute();
  }

  /**
   * Get the result of the last execution.
   *
   * @return mixed
   */
  public function getResult(): mixed {
    return $this->result;
  }

  /**
   * Get the error of the last execution.
   *
   * @return Throwable|null
   */
  public function getLastError(): ?Throwable {
    return $this->callbackError;
  }

  /**
   * Check if the SignalHandler is idle.
   *
   * @return bool
   */
  public function isIdle(): bool {
    return $this->state === self::STATE_IDLE;
  }

  /**
   * Check if the SignalHandler is currently executing.
   *
   * @return bool
   */
  public function isStarted(): bool {
    return $this->state === self::STATE_RUNNING;
  }

  /**
   * Check if the SignalHandler has finished execution.
   *
   * @return bool
   */
  public function isFinished(): bool {
    return $this->state === self::STATE_FINISHED;
  }

}