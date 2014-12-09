<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/system/mysql.php');
require_once(__DIR__ . '/system/mysql.php');

const PEDIDOS_DB_ORDER_READ = 'order';
const PEDIDOS_DB_ORDER_WRITE = 'order';

const PEDIDOS_ORDER_MAX_LOCK_TIME = 60;

const PEDIDOS_ORDER_STATUS_NEW = 1;
const PEDIDOS_ORDER_STATUS_PROCEED_LOCK_MONEY = 2;
const PEDIDOS_ORDER_STATUS_READY = 3;
const PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_MONEY = 4;
const PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_COMMISSION = 5;
const PEDIDOS_ORDER_STATUS_DONE = 6;
const PEDIDOS_ORDER_STATUS_FAILED = 7;
const PEDIDOS_ORDER_STATUS_CANCELED = 8;

function orderValidateOrderData($orderData = [])
{
    $authorIdExist = isset($orderData['authorId']);
    $executorId = isset($orderData['executorId']);
    $describe = isset($orderData['describe']);
    $cost = isset($orderData['cost']);

    return $authorIdExist && $executorId && $describe && $cost;
}

function orderCreateOrder($orderData)
{
    if (!orderValidateOrderData($orderData)) {
        return false;
    }

    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);

    $query = 'INSERT INTO `order` (authorId,executorId,`describe`,cost,createdTime,status) VALUES (?,?,?,?,?,?)';

    $query_stmt = mysqli_prepare($connection, $query);

    $status = PEDIDOS_ORDER_STATUS_NEW;
    $time = time();

    mysqli_stmt_bind_param(
        $query_stmt,
        'iisiii',
        $orderData['authorId'],
        $orderData['executorId'],
        $orderData['describe'],
        $orderData['cost'],
        $time,
        $status
    );

    $res = mysqli_stmt_execute($query_stmt);

    return mysqli_stmt_insert_id($query_stmt);
}

function orderGetOrderById($orderId)
{
    $id = (int) $orderId;

    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_READ);

    $query = 'select id, authorId, executorId, `describe`, cost, createdTime,status,lastStatusChangedTimeCreation,lastStatusChangedTimeExecution  from order where id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($query_stmt, 'i', $id);

    mysqli_stmt_bind_result(
        $query_stmt,
        $orderId,
        $orderAuthorId,
        $orderExecutorId,
        $orderDescribe,
        $orderCost,
        $orderCreatedTime,
        $orderStatus,
        $orderLastStatusChangedTimeCreation,
        $orderLastStatusChangedTimeExecution
    );

    $orders = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $orders[] = [
                    'id'                             => $orderId,
                    'authorId'                       => $orderAuthorId,
                    'executorId'                     => $orderExecutorId,
                    'describe'                       => $orderDescribe,
                    'cost'                           => $orderCost,
                    'createdTime'                    => $orderCreatedTime,
                    'status'                         => $orderStatus,
                    'lastStatusChangedTimeCreation'  => $orderLastStatusChangedTimeCreation,
                    'lastStatusChangedTimeExecution' => $orderLastStatusChangedTimeExecution,
                ];
            }
        }
    }

    if (count($orders) === 1) {
        $order = $orders[0];
        return $order;
    }
    return false;
}

function orderSetStatusLockForProceedLockMoneyById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=?, `lastStatusChangedTimeCreation`=?  where status=? and id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusLock = PEDIDOS_ORDER_STATUS_PROCEED_LOCK_MONEY;
    $time = time();
    $statusNew = PEDIDOS_ORDER_STATUS_NEW;
    $maxLockTime = PEDIDOS_ORDER_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiii',
        $statusLock,
        $time,
        $statusNew,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusReadyToExecuteById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=?, `lastStatusChangedTimeCreation`=?  where status=? and `lastStatusChangedTimeCreation`+?>?  and id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusReady = PEDIDOS_ORDER_STATUS_READY;
    $time = time();
    $statusLock = PEDIDOS_ORDER_STATUS_PROCEED_LOCK_MONEY;
    $maxLockTime = PEDIDOS_ORDER_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $statusReady,
        $time,
        $statusLock,
        $maxLockTime,
        $time,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusProceedMoneyTransferById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=?, `lastStatusChangedTimeExecution`=?  where status=? and id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusProceedTransferMoney = PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_MONEY;
    $time = time();
    $statusReady = PEDIDOS_ORDER_STATUS_READY;
    $maxLockTime = PEDIDOS_ORDER_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiii',
        $statusProceedTransferMoney,
        $time,
        $statusReady,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusTransferCommissionById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=?, `lastStatusChangedTimeExecution`=?  where status=? and `lastStatusChangedTimeExecution`+?>?  and id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusTransferCommission = PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_COMMISSION;
    $time = time();
    $statusProceedTransferMoney = PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_MONEY;
    $maxLockTime = PEDIDOS_ORDER_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $statusTransferCommission,
        $time,
        $statusProceedTransferMoney,
        $maxLockTime,
        $time,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusDoneById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=?, `lastStatusChangedTimeExecution`=?  where status=? and `lastStatusChangedTimeExecution`+?>?  and id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusDone = PEDIDOS_ORDER_STATUS_DONE;
    $time = time();
    $statusProceedTransferCommission = PEDIDOS_ORDER_STATUS_PROCEED_TRANSFER_COMMISSION;
    $maxLockTime = PEDIDOS_ORDER_MAX_LOCK_TIME;

    mysqli_stmt_bind_param(
        $query_stmt,
        'iiiiii',
        $statusDone,
        $time,
        $statusProceedTransferCommission,
        $maxLockTime,
        $time,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusFailedById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=? WHERE id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusFailed = PEDIDOS_ORDER_STATUS_FAILED;
    mysqli_stmt_bind_param(
        $query_stmt,
        'ii',
        $statusFailed,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

function orderSetStatusCanceledById($orderId)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_ORDER_WRITE);
    $query = 'UPDATE `order` SET status=? WHERE id=?';
    $query_stmt = mysqli_prepare($connection, $query);
    $statusCanceled = PEDIDOS_ORDER_STATUS_CANCELED;
    mysqli_stmt_bind_param(
        $query_stmt,
        'ii',
        $statusCanceled,
        $orderId
    );

    $res = mysqli_stmt_execute($query_stmt);
    if (mysqli_stmt_affected_rows($query_stmt) === 1) {
        return true;
    } else {
        return false;
    }
}

