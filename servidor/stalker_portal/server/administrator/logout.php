<?php

\session_start();
unset($_SESSION['login'], $_SESSION['pass']);
\header('Location: index.php');
