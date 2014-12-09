<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */


require_once(__DIR__ . '/../src/loader.php');

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

    $c =  compileTemplate(
        'auth',
        [
            'errors'    => $errors,
            'typedName' => $typedName
        ]
    );

    echo  compileTemplate(
        'layout',
        [
            'container' => $c,
        ]
    );
}

run();