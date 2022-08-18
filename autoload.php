<?php
defined('ABSPATH') || exit;

spl_autoload_register(function ($class) {

    if (strpos($class, 'Awps\\') !== false) {

        $class = str_replace('Awps\\', '', $class);
        include_once 'includes/' . $class . '.php';
    }
});
