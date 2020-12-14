<?php

    /**
     * WordPress options item loader
     *
     * If you put the app in your-domain.com/wp-content/libraries use a loader.php like below
     * <code>
     * <?php
     * require_once WP_CONTENT_DIR . '/libraries/wp_options_item/wpoiLibs/loader.php';
     * $users = new \wpoiLibs\samples\users;
     * ?>
     * </code>
     */

spl_autoload_register(function ($class_path) {

    $app_catalog = 'wpoiLibs\\';
    $dir = __DIR__ .'/' ;
    $length = strlen($app_catalog);

    if (strncmp($app_catalog, $class_path, $length) !== 0) {
        return;
    }

    $class_name = substr($class_path, $length);
    $class = $dir . str_replace('\\', '/', $class_name) . '.php';

    if (file_exists($class)) {
        require $class;
    }
});
