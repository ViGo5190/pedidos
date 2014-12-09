<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/loader.php');

helpersMakePrivate();

function makePageForAuthor()
{

    $container = compileTemplate(
        'orderCreateForm',
        [
            'typedCost' => '',
            'typedDesc' => '',
            'typedName' => ''
        ]
    );

    $container .= "<div id=\"orders\" class=\"row text-center\"> </div>";
    return $container;
}

function makePageForExecutor()
{
    $container = "<div id=\"orders\" class=\"row text-center\"> </div>";
    return $container;
}

function run()
{

    $containerData = "";
    $user = userGetUserByID(userGetUserIdFromSession());
    if ($user['type'] == PEDIDOS_USER_TYPE_AUTHOR) {
        $containerData = makePageForAuthor();
    }

    if ($user['type'] == PEDIDOS_USER_TYPE_EXECUTOR) {
        $containerData = makePageForExecutor();
    }

    echo compileTemplate(
        'layout',
        [
            'container' => $containerData,
        ]
    );
}

run();
