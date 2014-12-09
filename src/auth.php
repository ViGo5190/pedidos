<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/system/session.php');
require_once(__DIR__ . '/system/request.php');

const PEDIDOS_AUTH_SESSION_KEY_FOR_AUTHORIZED = 'pedidos_auth_is';
const PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID = 'pedidos_auth_user_id';
const PEDIDOS_AUTH_SESSION_KEY_FOR_REQUESTED_PAGE = 'pedidos_auth_requested_page';

function authCheckAuthorized()
{
    if (isset(sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_AUTHORIZED])) {
        return true;
    }

    return false;
}

function authGetUserId()
{
    if (authCheckAuthorized()) {
        if (isset(sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID])) {
            return sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID];
        }
    }

    return false;
}

function authRedirectToAuthPage()
{
    $requestedPage = requestGetSEREVRData()[REQUEST_URI];

    sessionSetData(PEDIDOS_AUTH_SESSION_KEY_FOR_REQUESTED_PAGE, $requestedPage);

    header('HTTP/1.0 403 Forbidden');
//    echo "Forbidden";
    header('Location: ' . '/auth.php');
}

function authRedirectAfterSuccessAuth($redirectToRequestedPage = true)
{
    var_dump(sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_REQUESTED_PAGE]);
    if (($redirectToRequestedPage) && (isset(sessionGetAll()['PEDIDOS_AUTH_SESSION_KEY_FOR_REQUESTED_PAGE']))) {
        header('Location: ' . sessionGetAll()['PEDIDOS_AUTH_SESSION_KEY_FOR_REQUESTED_PAGE']);
    } else {
        header('Location: /');
    }
}

function authCheckAuth($username, $password)
{
    $users = userGetUsersByUserNameAndPassword($username, $password);
    if (count($users) !== 1) {
        return false;
    }
    authSetAuthorized($users[0]['id']);
    return true;
}

function authSetAuthorized($userId)
{
    sessionSetData(PEDIDOS_AUTH_SESSION_KEY_FOR_AUTHORIZED,1);
    sessionSetData(PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID,$userId);
}

function authSetUnAuthorized()
{
    sessionUnSetData(PEDIDOS_AUTH_SESSION_KEY_FOR_AUTHORIZED);
    sessionUnSetData(PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID);
}