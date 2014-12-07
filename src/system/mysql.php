<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/config.php');

function mysqlGetConnection($name)
{
    static $connections = [];

    if (isset($connections[$name])) {
        return $connections[$name];
    }

    if (!isset(configGetAll()['dbs'][$name])) {
        die('DB ' . $name . ' config not find!');
    }

    $config = configGetAll()['dbs'][$name];

    $mysqli = mysqli_connect($config['host'], $config['user'], $config['password'], $config['db']);
    if (mysqli_connect_errno($mysqli)) {
        die('Cannot connect to db:' . $name);
    }

    $connections[$name] = $mysqli;

    return $connections[$name];
}