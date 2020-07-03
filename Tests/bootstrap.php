<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists('../../../vendor/autoload.php')) {
    require '../../../vendor/autoload.php';
} elseif (file_exists('./vendor/autoload.php')) {
    require './vendor/autoload.php';
}

if (file_exists('../../../config/bootstrap.php')) {
    require '../../../config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv('../../../.env');
}
