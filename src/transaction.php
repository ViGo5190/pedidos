<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

/**
 * id
 * authorId
 * targetId
 * type
 * amount
 * status
 * createdTime
 * changedTime
 */

const PEDIDOS_TRANSACTION_STATUS_NEW = 1;
const PEDIDOS_TRANSACTION_STATUS_LOCK = 2;
const PEDIDOS_TRANSACTION_STATUS_EXECUTED = 3;
const PEDIDOS_TRANSACTION_STATUS_FAILED = 4;
const PEDIDOS_TRANSACTION_STATUS_CANCELED = 5;

const PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY = 1;
const PEDIDOS_TRANSACTION_TYPE_LOCK_MONEY = 2;
const PEDIDOS_TRANSACTION_TYPE_TRANSFER_MONEY = 3;
const PEDIDOS_TRANSACTION_TYPE_COMMISSION_MONEY = 3;

const PEDIDOS_TRANSACTION_MAX_LOCK_TIME = 60;


const PEDIDOS_DB_TRANSACTION_READ = 'transaction';
const PEDIDOS_DB_TRANSACTION_WRITE = 'transaction';

function transactionCreateLoanMoneyToUser($userId, $amount)
{

    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

    $query = 'INSERT INTO `transaction` (targetId,type,amount,status,createdTime) VALUES (?,?,?,?,?) ';

    $query_stmt = mysqli_prepare($connection, $query);

    $type = PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY;
    $status = PEDIDOS_TRANSACTION_STATUS_NEW;
    $time = time();

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiii',
        $userId,
        $type,
        $amount,
        $status,
        $time
    );

    $res = mysqli_stmt_execute($query_stmt);

    return mysqli_stmt_insert_id($query_stmt);
}


function transactionCreateMoneyLockForAuthor($userId, $amount)
{

    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

    $query = 'INSERT INTO `transaction` (authorId, targetId,type,amount,status,createdTime) VALUES (?,?,?,?,?,?) ';

    $query_stmt = mysqli_prepare($connection, $query);

    $type = PEDIDOS_TRANSACTION_TYPE_LOCK_MONEY;
    $status = PEDIDOS_TRANSACTION_STATUS_NEW;
    $time = time();

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $userId,
        $userId,
        $type,
        $amount,
        $status,
        $time
    );

    $res = mysqli_stmt_execute($query_stmt);

    return mysqli_stmt_insert_id($query_stmt);
}


function transactionLockTransactionById($transactionId){
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

//    $query = 'UPDATE `transaction` SET status=?, `changedTime`=?  where status=? and (changedTime+?<? or changedTime=0)  and id=?';
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

    if (mysqli_stmt_affected_rows($query_stmt) === 1){
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
function transactionExecuteTransactionById($transactionId){
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

    if (mysqli_stmt_affected_rows($query_stmt) === 1){
        return true;
    } else {
        return false;
    }
}

function transactionFaileTransactionById($transactionId){
    $connection = mysqlGetConnection(PEDIDOS_DB_TRANSACTION_WRITE);

//    $query = 'UPDATE `transaction` SET status=?, `changedTime`=?  where status=? and (changedTime+?<? or changedTime=0)  and id=?';
    $query = 'UPDATE `transaction` SET status=?  where  id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    $statusFailed = PEDIDOS_TRANSACTION_STATUS_FAILED;
    $time = time();

    $statusLock = PEDIDOS_TRANSACTION_STATUS_LOCK;
    $maxLockTime = PEDIDOS_TRANSACTION_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'ii',
        $statusFailed,
        $transactionId
    );

    $res = mysqli_stmt_execute($query_stmt);

    if (mysqli_stmt_affected_rows($query_stmt) === 1){
        return true;
    } else {
        return false;
    }
}