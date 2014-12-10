<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

const PEDIDOS_APP_DEFAULT_NAME = 'pedidos';
const PEDIDOS_APP_DEFAULT_DOMAIN = 'example.com';
const PEDIDOS_APP_DEFAULT_SECURE = false;

function appGetName()
{
    if (isset(configGetAll()['app']['name'])) {
        return (configGetAll()['app']['name']);
    }
    return PEDIDOS_APP_DEFAULT_NAME;
}

function appGetDomain()
{
    if (isset(configGetAll()['app']['domain'])) {
        return (configGetAll()['app']['domain']);
    }
    return PEDIDOS_APP_DEFAULT_DOMAIN;
}

function appGetSecure()
{
    if (isset(configGetAll()['app']['secure'])) {
        return (bool) (configGetAll()['app']['secure']);
    }
    return PEDIDOS_APP_DEFAULT_SECURE;
}

function appGetDomainFull()
{
    if (appGetSecure()) {
        return "https://" . appGetDomain() . "/";
    }
    return "http://" . appGetDomain() . "/";
}