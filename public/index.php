<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/system/request.php');
require_once(__DIR__ . '/../src/system/cookie.php');
require_once(__DIR__ . '/../src/system/session.php');
require_once(__DIR__ . '/../src/system/template.php');

function run()
{
    if (isset(requestGetGETData()['name'])) {
        $name = requestGetGETData()['name'];
        cookieSetCookie('name', $name);
    } elseif (cookieGetByName('name')) {
        $name = cookieGetByName('name');
    } else {
        $name = 'World';
    }

    $count = (int) sessionGetAll()['count'];
    $count++;

    sessionSetData('count', $count);

    echo compileTemplate(
        'test',
        [
            'name'  => $name,
            'count' => $count,
        ]
    );
}

run();
