# PosixSignalHandler

A utility class for handling POSIX signals in PHP applications.

```php

use antibiotics11\PosixSignalHandler\PosixSignal;
use antibiotics11\PosixSignalHandler\PosixSignalHandler;

// Registering a custom handler for SIGINT (Ctrl+C)
PosixSignalHandler::addHandler(PosixSignal::SIGINT, function (): void {
  printf("Ctrl+C pressed! Exiting...\r\n");
  exit(0);
});

```

## Requirements

- PHP >= 8.1
- <a href="https://www.php.net/manual/en/intro.pcntl.php">ext-pcntl</a>

## Installation

```shell
composer require antibiotics11/posix-signal-handler
```

