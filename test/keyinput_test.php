#!/usr/bin/env php
<?php

require_once "../src/PosixSignal.php";
require_once "../src/PosixSignalHandler.php";
use Signal\{PosixSignal, PosixSignalHandler};

// Register the handler for the SIGTSTP (Ctrl+Z) signal.
PosixSignalHandler::register(PosixSignal::SIGTSTP, function() {
  printf("\tCtrl+Z pressed!\r\n");
});

// Register the handler for the SIGQUIT (Ctrl+\) signal.
PosixSignalHandler::register(PosixSignal::SIGQUIT, function() {
  printf("\tCtrl+\\ pressed!\r\n");
});

// Register the handler for the SIGINT (Ctrl+C) signal.
PosixSignalHandler::register(PosixSignal::SIGINT, function() {
  printf("\tCtrl+C pressed!\r\n");
  exit(0);
});

// The process will stay in this loop until a signal is received.
while (true) {}
