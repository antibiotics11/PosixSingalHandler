# posix-signal-manager

A PHP library for POSIX signal handling.

```php
use antibiotics11\PosixSignalManager\{Signal, SignalHandler, SignalManager};

// Registering a custom handler for SIGINT. (Ctrl+C)
SignalManager::getManager()->addHandler(
    signal:  Signal::SIGINT,
    handler: new SignalHandler(function (): void {
        printf("Ctrl+C has been pressed! Exiting...\r\n");
        exit(0);
    })
);
```

## Requirements

- PHP >= 8.1
- <a href="https://www.php.net/manual/en/intro.posix.php">ext-posix</a>
- <a href="https://www.php.net/manual/en/intro.pcntl.php">ext-pcntl</a>

## Installation

```shell
composer require antibiotics11/posix-signal-manager
```