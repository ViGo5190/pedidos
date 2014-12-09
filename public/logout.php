<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

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
    authSetUnAuthorized();
    authRedirectAfterSuccessAuth(false);
}

run();