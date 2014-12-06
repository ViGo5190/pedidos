<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/system/template.php');

echo compileTemplate(
    'test',
    ['name' => 'World']
);