<?php

use PHPUnit\Framework\TestCase;
use antibiotics11\PosixSignalManager\{Signal, SignalHandler, SignalManager};

class SignalManagerTest extends TestCase {

  public function testAddHandler(): void {

    $signal = Signal::SIGHUP;
    $signalHandler = $this->getMockBuilder(SignalHandler::class)
                          ->setConstructorArgs([ fn() => true ])
                          ->getMock();

    $signalManager = SignalManager::getManager();
    $signalManager->addHandler($signal, $signalHandler);

    $handlers = $signalManager->getHandlers($signal);
    $this->assertContains($signalHandler, $handlers);

  }

  public function testRemoveHandler(): void {

    $signal = Signal::SIGHUP;
    $signalHandler = $this->getMockBuilder(SignalHandler::class)
                          ->setConstructorArgs([ fn() => true ])
                          ->getMock();

    $signalManager = SignalManager::getManager();
    $signalManager->addHandler($signal, $signalHandler);

    $signalManager->removeHandler($signal, $signalHandler);

    $handlers = $signalManager->getHandlers($signal);
    $this->assertNotContains($signalHandler, $handlers);

  }

  public function testHandle(): void {

    $signal = Signal::SIGUSR1;
    $signalHandler = $this->getMockBuilder(SignalHandler::class)
                          ->setConstructorArgs([ fn() => true ])
                          ->getMock();

    $signalHandler->expects($this->once())->method("execute");

    $signalManager = SignalManager::getManager();
    $signalManager->addHandler($signal, $signalHandler);

    posix_kill(posix_getpid(), $signal->value);

  }

}