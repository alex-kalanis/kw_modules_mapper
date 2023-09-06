<?php

function autoload($className)
{
    if (!defined('AUTHOR_NAME')) {
        define('AUTHOR_NAME', '.');
    }
    if (!defined('PROJECT_NAME')) {
        define('PROJECT_NAME', '.');
    }
    if (!defined('PROJECT_DIR')) {
        define('PROJECT_DIR', 'src');
    }
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $shortName = preg_replace('#^' . AUTHOR_NAME . DIRECTORY_SEPARATOR . PROJECT_NAME . '#', '', $className);

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . $shortName . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . $shortName . '.php');
    }

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'external' . DIRECTORY_SEPARATOR . $className . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'external' . DIRECTORY_SEPARATOR . $className . '.php');
    }

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $className . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $className . '.php');
    }

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . PROJECT_DIR . DIRECTORY_SEPARATOR . $className . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . PROJECT_DIR . DIRECTORY_SEPARATOR . $className . '.php');
    }

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . PROJECT_DIR . DIRECTORY_SEPARATOR . $shortName . '.php')) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . PROJECT_DIR . DIRECTORY_SEPARATOR . $shortName . '.php');
    }
}

spl_autoload_register('autoload');
