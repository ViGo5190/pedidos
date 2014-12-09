<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/loader.php');

helpersMakePrivate();

function makePageForAuthor(){
    return "";
}

function makePageForExecutor(){
    return "";
}



function run()
{

    $containerData = "";
    $user = userGetUserByID(userGetUserIdFromSession());
    if ($user['type'] == PEDIDOS_USER_TYPE_AUTHOR ){
        $containerData = makePageForAuthor();
    }

    if ($user['type'] == PEDIDOS_USER_TYPE_EXECUTOR ){
        $containerData = makePageForAuthor();
    }

//    $name = $user['username'];
//
//    if (isset(sessionGetAll()['user_' . userGetUserIdFromSession() . '_count'])){
//        $count = (int) sessionGetAll()['user_' . userGetUserIdFromSession() . '_count'];
//        $count++;
//    } else {
//        $count = 0;
//    }
//
//
//    sessionSetData('user_' . userGetUserIdFromSession() . '_count', $count);
//
//    $appName = configGetAll()['app']['name'];
//
//    $c = compileTemplate(
//        'test',
//        [
//            'name'    => $name,
//            'count'   => $count,
//            'appName' => $appName,
//        ]
//    );

    echo  compileTemplate(
        'layout',
        [
            'container' => $containerData,
        ]
    );
}

run();
