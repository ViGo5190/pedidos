<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/loader.php');


function run()
{
    if (!authCheckAuthorized()) {
        authRedirectToAuthPage();
    }

    $user = userGetUserByID(userGetUserIdFromSession());
    $name = $user['username'];

    if (isset(sessionGetAll()['user_' . userGetUserIdFromSession() . '_count'])){
        $count = (int) sessionGetAll()['user_' . userGetUserIdFromSession() . '_count'];
        $count++;
    } else {
        $count = 0;
    }


    sessionSetData('user_' . userGetUserIdFromSession() . '_count', $count);

    $appName = configGetAll()['app']['name'];

    echo compileTemplate(
        'test',
        [
            'name'    => $name,
            'count'   => $count,
            'appName' => $appName,
        ]
    );
}

run();
