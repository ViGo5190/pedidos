<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

const COOKIE_LIFE_TIME = 2592000;

function cookieGetAll()
{
    return $_COOKIE;
}

function cookieGetByName($name)
{
    if (isset($_COOKIE[$name])) {
        return $_COOKIE[$name];
    }
    return false;
}

function cookieSetCookie($name, $value, $cookieLifeTime = COOKIE_LIFE_TIME)
{
    return setcookie($name, $value, time() + $cookieLifeTime);
}