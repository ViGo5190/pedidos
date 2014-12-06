<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

const SESSION_NAME = 'pedidos';

/**
 * Start session. fix problem with a lot of start session. work only on php >  5.4
 */
function sessionStartSession()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

function sessionGetAll()
{
    sessionStartSession();

    return $_SESSION;
}

function sessionSetData($name, $value)
{
    sessionStartSession();

    $_SESSION[$name] = $value;
}


