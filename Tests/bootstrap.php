<?php

use Symfony\Component\Dotenv\Dotenv;

require getenv("DOCUMENT_ROOT") . '/vendor/autoload.php';

if (file_exists(getenv("DOCUMENT_ROOT") . '/config/bootstrap.php')) {
    require getenv("DOCUMENT_ROOT") . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(getenv("DOCUMENT_ROOT") . '/.env');
}
