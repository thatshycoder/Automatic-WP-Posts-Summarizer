<?php
defined('ABSPATH') || exit;

spl_autoload_register(function ($class) {

    if (strpos($class, 'Awpps\\') !== false) {

        $class = str_replace('Awpps\\', '', $class);
        include_once 'includes/' . $class . '.php';
    }

    // $class = strtolower($class);

    // if (strpos($class, 'awpps') !== false) {

    //     $class = str_replace('awpps_', '', $class);
    //     $class = str_replace('_', '-', $class);

    //     include_once 'includes/' . $class . '.php';
    // }
});
