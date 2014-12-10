<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/loader.php');
securePreventFrame();

$token = authGetToken();
cookieSetCookie('pedidost', $token);

function run()
{
    $errors = [];
    $typedName = '';

    $pedidosTokenPost = "";
    $pedidosTokenSession = "";
    $pedidosTokenCookie = "";

    if (isset(requestGetPOSTData()['pedidost'])) {
        $pedidosTokenPost = requestGetPOSTData()['pedidost'];
    }
    if (isset(sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_TOKEN])) {
        $pedidosTokenSession = sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_TOKEN];
    }
    if (isset(cookieGetAll()['pedidost'])) {
        $pedidosTokenCookie = cookieGetAll()['pedidost'];
    }

    if (isset(requestGetPOSTData()['username'])) {
        $typedName = requestGetPOSTData()['username'];
        if (isset(requestGetPOSTData()['password'])) {

            if ( ($pedidosTokenPost==$pedidosTokenSession) && ($pedidosTokenSession==$pedidosTokenCookie)){
                $checkAuth = authCheckAuth(
                    requestGetPOSTData()['username'],
                    requestGetPOSTData()['password']
                );
                if (!$checkAuth) {
                    $errors[] = 'Wrong password or username!';
                } else {
                    var_dump('ok!');
                    authRedirectAfterSuccessAuth();
                }
            } else {
                $errors[] = 'Fatal error.';
            }

        } else {
            $errors[] = 'Please, type password!';
        }
    }

    $container = compileTemplate(
        'auth',
        [
            'errors'    => $errors,
            'typedName' => $typedName
        ]
    );

    echo compileTemplate(
        'layout',
        [
            'container' => $container,
        ]
    );
}

run();