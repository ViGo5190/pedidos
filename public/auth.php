<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/system/config.php');
require_once(__DIR__ . '/../src/system/request.php');
require_once(__DIR__ . '/../src/system/cookie.php');
require_once(__DIR__ . '/../src/system/session.php');
require_once(__DIR__ . '/../src/system/template.php');
require_once(__DIR__ . '/../src/system/mysql.php');
require_once(__DIR__ . '/../src/auth.php');
require_once(__DIR__ . '/../src/user.php');

function run()
{
    $errors = [];
    $typedName = '';
    if (isset(requestGetPOSTData()['username'])) {
        $typedName = requestGetPOSTData()['username'];
        if (isset(requestGetPOSTData()['password'])) {
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
            $errors[] = 'Please, type password!';
        }
    }

    echo compileTemplate(
        'auth',
        [
            'errors'    => $errors,
            'typedName' => $typedName
        ]
    );
}

run();