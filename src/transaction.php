<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

const PEDIDOS_TRANSACTION_STATUS_NEW = 1;
const PEDIDOS_TRANSACTION_STATUS_LOCK = 2;
const PEDIDOS_TRANSACTION_STATUS_EXECUTED = 3;
const PEDIDOS_TRANSACTION_STATUS_FAILED = 4;
const PEDIDOS_TRANSACTION_STATUS_CANCELED = 5;

const PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_USER = 1;
const PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_USER_FOR_TRANSFERRING_TO_TRANSFER_ACCOUNT = 2;
const PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_TRANSFER_ACCOUNT_AFTER_TRANSFERRING_FROM_USER = 4;
const PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_COMMISSION_ACCOUNT = 5;
const PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_USER_ACCOUNT = 6;
const PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_USER_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT = 7;
const PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_COMMISSION_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT = 8;

const PEDIDOS_TRANSACTION_MAX_LOCK_TIME = 60;

const PEDIDOS_DB_TRANSACTION_READ = 'transaction';
const PEDIDOS_DB_TRANSACTION_WRITE = 'transaction';

function transactionCreateLoanMoneyToUser($accountId, $amount)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);
    $query = 'INSERT INTO `transaction` (accountId,type,amount,status,createdTime) VALUES (?,?,?,?,?) ';
    $query_stmt = mysqli_prepare($connection, $query);
    $type = PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_USER;
    $status = PEDIDOS_TRANSACTION_STATUS_NEW;
    $time = time();

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiii',
        $accountId,
        $type,
        $amount,
        $status,
        $time
    );

    $res = mysqli_stmt_execute($query_stmt);
    return mysqli_stmt_insert_id($query_stmt);
}

function transactionCreateTransaction($accountId, $type, $amount, $orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);
    $query = 'INSERT INTO `transaction` (accountId,type,amount,status,createdTime,orderId) VALUES (?,?,?,?,?,?) ';
    $query_stmt = mysqli_prepare($connection, $query);
    $status = PEDIDOS_TRANSACTION_STATUS_NEW;
    $time = time();

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $accountId,
        $type,
        $amount,
        $status,
        $time,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    return mysqli_stmt_insert_id($query_stmt);
}

function transactionLockTransactionById($transactionId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

    $query = 'UPDATE `transaction` SET status=?, `changedTime`=?  where status=? and id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    $statusLock = PEDIDOS_TRANSACTION_STATUS_LOCK;
    $time = time();

    $statusNew = PEDIDOS_TRANSACTION_STATUS_NEW;
    $maxLockTime = PEDIDOS_TRANSACTION_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiii',
        $statusLock,
        $time,
        $statusNew,
        $transactionId
    );

    $res = mysqli_stmt_execute($query_stmt);

    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * It could be executed or failed status ( if timeout )
 * @param $transactionId
 * @return bool
 */
function transactionExecuteTransactionById($transactionId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

//    $query = 'UPDATE `transaction` SET status=?, `changedTime`=?  where status=? and (changedTime+?<? or changedTime=0)  and id=?';
    $query = 'UPDATE `transaction` SET status=?, `changedTime`=?  where status=? and changedTime+?>? and id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    $statusExecuted = PEDIDOS_TRANSACTION_STATUS_EXECUTED;
    $time = time();

    $statusLock = PEDIDOS_TRANSACTION_STATUS_LOCK;
    $maxLockTime = PEDIDOS_TRANSACTION_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $statusExecuted,
        $time,
        $statusLock,
        $maxLockTime,
        $time,
        $transactionId
    );

    $res = mysqli_stmt_execute($query_stmt);

    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function transactionFailTransactionById($transactionId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

    $query = 'UPDATE `transaction` SET status=?  where  id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusFailed = PEDIDOS_TRANSACTION_STATUS_FAILED;
    mysqli_stmt_bind_param(
        $query_stmt,
        'ii',
        $statusFailed,
        $transactionId
    );

    $res = mysqli_stmt_execute($query_stmt);

    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}