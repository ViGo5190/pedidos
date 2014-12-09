<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/system/mysql.php');

const PEDIDOS_DB_ACCOUNT_READ = 'account';
const PEDIDOS_DB_ACCOUNT_WRITE = 'account';

const PEDIDOS_ACCOUNT_TYPE_USER = 1;
const PEDIDOS_ACCOUNT_TYPE_TRANSFER = 2;
const PEDIDOS_ACCOUNT_TYPE_SYSTEM = 3;

//UPDATE `account` SET `balance`=IF(`id`=1, balance-100,IF(`id`=2,balance+100,balance)) WHERE id=1 or id=2

function accountGetAccountById($accountId, $force = false)
{
    static $accountStore = [];

    $id = (int) $accountId;

    if ((!$force) && (isset($accountStore[$id]))) {
        return $accountStore[$id];
    }

    $connection = mysqlGetConnection(PEDIDOS_DB_ACCOUNT_READ);

    $query = 'select id, type, balance from account where id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($query_stmt, 'i', $id);

    mysqli_stmt_bind_result($query_stmt, $accountId, $accountType, $accountBalance);

    $accounts = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $accounts[] = [
                    'id'      => $accountId,
                    'type'    => $accountType,
                    'balance' => $accountBalance,
                ];
            }
        }
    }

    if (count($accounts) === 1) {
        $account = $accounts[0];
        $accountStore[$id] = $account;
        return $account;
    }
    return false;
}

function accountGetTransferAccount()
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ACCOUNT_READ);

    $query = 'select id, type, balance from account where type=?';

    $query_stmt = mysqli_prepare($connection, $query);

    $type = PEDIDOS_ACCOUNT_TYPE_TRANSFER;
    mysqli_stmt_bind_param($query_stmt, 'i', $type);

    mysqli_stmt_bind_result($query_stmt, $accountId, $accountType, $accountBalance);

    $accounts = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $accounts[] = [
                    'id'      => $accountId,
                    'type'    => $accountType,
                    'balance' => $accountBalance,
                ];
            }
        }
    }

    if (count($accounts) === 1) {
        $account = $accounts[0];
        return $account;
    }
    return false;
}

function accountGetCommissionAccount()
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ACCOUNT_READ);

    $query = 'select id, type, balance from account where type=?';

    $query_stmt = mysqli_prepare($connection, $query);

    $type = PEDIDOS_ACCOUNT_TYPE_SYSTEM;
    mysqli_stmt_bind_param($query_stmt, 'i', $type);

    mysqli_stmt_bind_result($query_stmt, $accountId, $accountType, $accountBalance);

    $accounts = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $accounts[] = [
                    'id'      => $accountId,
                    'type'    => $accountType,
                    'balance' => $accountBalance,
                ];
            }
        }
    }

    if (count($accounts) === 1) {
        $account = $accounts[0];
        return $account;
    }
    return false;
}

function accountLoanMoneyAccount($accountId, $amount)
{
    $id = (int) $accountId;
    $connection = mysqlGetConnection(PEDIDOS_DB_ACCOUNT_WRITE);
    $query = 'UPDATE `account` SET `balance`=balance+? WHERE id=? ';
    $query_stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param(
        $query_stmt,
        'ii',
        $amount,
        $id
    );
    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function accountMinusMoneyFromAccount($accountId, $amount)
{
    $id = (int) $accountId;
    $connection = mysqlGetConnection(PEDIDOS_DB_ACCOUNT_WRITE);
    $query = 'UPDATE `account` SET `balance`=balance-? WHERE id=? and balance>=? ';
    $query_stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param(
        $query_stmt,
        'iii',
        $amount,
        $id,
        $amount
    );
    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}
