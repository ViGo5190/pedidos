<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/system/request.php');
require_once(__DIR__ . '/../src/system/cookie.php');
require_once(__DIR__ . '/../src/system/template.php');

if (isset(requestGetGETData()['name'])) {
    $name = requestGetGETData()['name'];
    cookieSetCookie('name', $name);
} elseif (cookieGetByName('name')) {
    $name = cookieGetByName('name');
} else {
    $name = 'World';
}

echo compileTemplate(
    'test',
    ['name' => $name]
);