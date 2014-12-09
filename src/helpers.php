<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */


function helpersMakePrivate()
{
    if (!authCheckAuthorized()) {
        authRedirectToAuthPage();
    }
}