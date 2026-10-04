<?php
spl_autoload_register(function (string $class_name) {
    $file = __DIR__ . '/model/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
