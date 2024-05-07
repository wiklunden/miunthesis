<?php

if (isset($_SESSION['file-name'])) {
    unset($_SESSION['file-name']);
    unset($_SESSION['stmt-results']);
    unset($_SESSION['sqli-results']);
    unset($_SESSION['complexity']);
}