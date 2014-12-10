<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/loader.php');

function run()
{
    authSetUnAuthorized();
    authRedirectAfterSuccessAuth(false);
}

run();