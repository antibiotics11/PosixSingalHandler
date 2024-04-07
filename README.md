# posix-signal-manager

A PHP library for POSIX signal handling. 

```php
use antibiotics11\PosixSignalManager\{Signal, SignalHandler, SignalManager};

// Registering a custom handler for SIGINT (Ctrl+C)
SignalManager::getManager()->addHandler(Signal::SIGINT, new SignalHandler(function (): void {
  printf("Ctrl+C pressed! Exiting...\r\n");
  exit(0);
}));
```

## Requirements

- PHP >= 8.1
- <a href="https://www.php.net/manual/en/intro.pcntl.php">ext-pcntl</a>

## Installation

```shell
composer require antibiotics11/posix-signal-manager
```

