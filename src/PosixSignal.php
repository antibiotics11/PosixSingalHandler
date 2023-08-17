<?php

namespace Signal;

enum PosixSignal: int {

  case SIGHUP  = SIGHUP;   // Hangup signal
  case SIGINT  = SIGINT;   // Interrupt signal
  case SIGQUIT = SIGQUIT;  // Quit signal
  case SIGILL  = SIGILL;   // Illegal instruction signal
  case SIGABRT = SIGABRT;  // Abort signal
  case SIGKILL = SIGKILL;  // Kill signal
  case SIGSEGV = SIGSEGV;  // Segmentation violation signal
  case SIGPIPE = SIGPIPE;  // Broken pipe signal
  case SIGALRM = SIGALRM;  // Alarm signal
  case SIGTERM = SIGTERM;  // Termination signal
  case SIGUSR1 = SIGUSR1;  // User-defined signal 1
  case SIGUSR2 = SIGUSR2;  // User-defined signal 2
  case SIGCHLD = SIGCHLD;  // Child process status changed signal
  case SIGCONT = SIGCONT;  // Continue signal
  case SIGSTOP = SIGSTOP;  // Stop signal
  case SIGTSTP = SIGTSTP;  // Terminal stop signal
  case SIGTTIN = SIGTTIN;  // Terminal input signal
  case SIGTTOU = SIGTTOU;  // Terminal output signal

};
