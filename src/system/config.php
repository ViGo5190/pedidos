<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

/**
 * Init config from file
 * @return mixed
 */
function configGetAll()
{
    static $config = false;

    if ($config !== false) {
        return $config;
    }

    if (!file_exists(__DIR__ . "/../../config/config.json")){
        die('config not find.');
    }

    $string = file_get_contents(__DIR__ . "/../../config/config.json");
    $config = json_decode($string, true);

    return $config;
}
