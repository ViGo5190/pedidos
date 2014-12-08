<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

/**
 * @return mixed
 */
function requestGetPOSTData()
{
    return $_POST;
}

function requestGetGETData()
{
    return $_GET;
}

function requestGetSEREVRData()
{
    return $_SERVER;
}

