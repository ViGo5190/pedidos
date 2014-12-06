<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/system/template.php');
require_once(__DIR__ . '/../src/system/request.php');

if (isset(requestGetGETData()['name'])) {
    $name = requestGetGETData()['name'];
} else {
    $name = 'World';
}

echo compileTemplate(
    'test',
    ['name' => $name]
);