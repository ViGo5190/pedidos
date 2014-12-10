<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

const PEDIDOS_SECURE_TOKEN_COOKIE_NAME = 'pedidost';

require_once('loader.php');

function securePreventFrame()
{
    header('X-Frame-Options: DENY');
}

function secureSetToken()
{
    $token = authGetToken();
    cookieSetCookie(PEDIDOS_SECURE_TOKEN_COOKIE_NAME, $token);
}