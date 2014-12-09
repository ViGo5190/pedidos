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

function helpersMakeApiPrivate()
{
    if (!authCheckAuthorized()) {
        header('HTTP/1.0 401 Forbidden');
    }
}

function helpersGetProjectName(){
    if (isset(configGetAll()['app']['name'])){
        return configGetAll()['app']['name'];
    }
    return "";
}

function helpersMoneyConvertToDecimal($money){
    return (int)$money / 100;
}

function helpersMoneyConvertToINT($money){
    return $money * 100;
}

function helpersGetCurrentUsername(){
    $user = userGetUserByID(userGetUserIdFromSession());
    if (isset($user['username'])){
        return $user['username'];
    }
    return false;
}

function helpersGetCurrentUserBalance()
{
    $user = userGetUserByID(userGetUserIdFromSession());
    if ($user){
        $account = accountGetAccountById($user['accountId']);
        if ($account){
            return helpersMoneyConvertToDecimal($account['balance']);
        }
    }

    return false;
}