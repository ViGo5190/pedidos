<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/system/config.php');
require_once(__DIR__ . '/../src/system/request.php');
require_once(__DIR__ . '/../src/system/cookie.php');
require_once(__DIR__ . '/../src/system/session.php');
require_once(__DIR__ . '/../src/system/template.php');
require_once(__DIR__ . '/../src/system/mysql.php');
require_once(__DIR__ . '/../src/auth.php');
require_once(__DIR__ . '/../src/user.php');
require_once(__DIR__ . '/../src/transaction.php');
require_once(__DIR__ . '/../src/order.php');
require_once(__DIR__ . '/../src/account.php');

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
