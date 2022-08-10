<?php

spl_autoload_register(function ($class) {

    $class = strtolower($class);

    if (strpos($class, 'awpps') !== false) {

        $class = str_replace('awpps_', '', $class);
        $class = str_replace('_', '-', $class);

        include_once 'includes/' . $class . '.php';
    }
});
