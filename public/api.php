<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/loader.php');

const PEDIDOS_API_ANSWER_STATUS_OK = 1;
const PEDIDOS_API_ANSWER_STATUS_ERROR = 2;
const PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL = 3;
const PEDIDOS_API_ANSWER_STATUS_EMPTY_ACTION = 4;
const PEDIDOS_API_ANSWER_STATUS_WRONG_ACTION = 5;
const PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_MONEY = 6;
const PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_FORM_DATA = 7;
const PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER = 8;
const PEDIDOS_API_ANSWER_STATUS_CANNOT_PROCEED_ORDER_IT_PROCEEDING = 9;

helpersMakeApiPrivate();

function makeOrder()
{

    if (!isset(requestGetGETData()['orderId'])) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_FORM_DATA);
    }
    $orderId = requestGetGETData()['orderId'];

    $order = orderGetOrderById($orderId);

    if (!$order) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!orderSetStatusProceed($orderId)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_PROCEED_ORDER_IT_PROCEEDING);
    }
    $userId = userGetUserIdFromSession();
    if (!$userId) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $user = userGetUserByID($userId);

    if (!$user) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $account = accountGetAccountById($user['accountId']);
    if (!$account) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $amount = helpersMoneyConvertToINT($order['cost']);

    $amountProceed = $amount * (100 - PEDIDOS_DEFAULT_COMMISSION_PERCENTAGE) / 100;
    $amoutCommission = $amount - $amountProceed;

    $transferAccountId = accountGetTransferAccount();
    $comissionAccountId = accountGetCommissionAccount();

    $tr3Id = transactionCreateTransaction($transferAccountId,
        PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_USER_ACCOUNT, $amountProceed,
        $orderId);

    if (!$tr3Id) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $tr4Id = transactionCreateTransaction($transferAccountId,
        PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_COMMISSION_ACCOUNT,
        $amoutCommission, $orderId);
    if (!$tr4Id) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $accountIdTo = $user['accountId'];
    $tr5Id = transactionCreateTransaction($accountIdTo,
        PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_USER_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT, $amountProceed,
        $orderId);

    if (!$tr5Id) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $tr6Id = transactionCreateTransaction($comissionAccountId,
        PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_COMMISSION_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT,
        $amoutCommission, $orderId);

    if (!$tr6Id) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionLockTransactionById($tr3Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!accountMinusMoneyFromAccount($transferAccountId, $amountProceed)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);;
    }

    if (!transactionExecuteTransactionById($tr3Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionLockTransactionById($tr4Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!accountMinusMoneyFromAccount($transferAccountId, $amoutCommission)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionExecuteTransactionById($tr4Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionLockTransactionById($tr5Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!accountLoanMoneyAccount($accountIdTo, $amountProceed)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    if (!transactionExecuteTransactionById($tr5Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionLockTransactionById($tr6Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    if (!accountLoanMoneyAccount($comissionAccountId, $amoutCommission)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!transactionExecuteTransactionById($tr6Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if (!orderSetStatusDoneById($orderId)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    showResponce(PEDIDOS_API_ANSWER_STATUS_OK,
        ['orderId' => $orderId, 'amount' => helpersMoneyConvertToDecimal($amountProceed)]);
}

function createOrder()
{

    if (!isset(requestGetGETData()['name'])) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_FORM_DATA);
    }

    if (!isset(requestGetGETData()['cost'])) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_FORM_DATA);
    }

    if (!isset(requestGetGETData()['desc'])) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_FORM_DATA);
    }

    $name = requestGetGETData()['name'];
    $cost = helpersMoneyConvertToINT(requestGetGETData()['cost']);
    $desc = requestGetGETData()['desc'];

    $userId = userGetUserIdFromSession();
    if (!$userId) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $user = userGetUserByID($userId);

    if (!$user) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $account = accountGetAccountById($user['accountId']);
    if (!$account) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if ($account['balance'] < $cost) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_NOT_ENOUGH_MONEY, [], ['недостаточно денег']);
    }

    $orderData = [
        'authorId'   => $userId,
        'executorId' => 0,
        'cost'       => $cost,
        'describe'   => $desc,
        'name'       => $name
    ];

    $orderId = orderCreateOrder($orderData);
    if (!$orderId) {
        return false;
    }

    $transferAccountId = accountGetTransferAccount();
    $comissionAccountId = accountGetCommissionAccount();

    if (!orderSetStatusLockForProceedLockMoneyById($orderId)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    $tr1Id = transactionCreateTransaction($user['accountId'],
        PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_USER_FOR_TRANSFERRING_TO_TRANSFER_ACCOUNT, $cost, $orderId);
    $tr2Id = transactionCreateTransaction($user['accountId'],
        PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_TRANSFER_ACCOUNT_AFTER_TRANSFERRING_FROM_USER, $cost, $orderId);

    if (!$tr1Id) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!transactionLockTransactionById($tr1Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!accountMinusMoneyFromAccount($user['accountId'], $cost)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!transactionExecuteTransactionById($tr1Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!transactionLockTransactionById($tr2Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!accountLoanMoneyAccount($transferAccountId, $cost)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!transactionExecuteTransactionById($tr2Id)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    if (!orderSetStatusReadyToExecuteById($orderId)) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_CANNOT_CREATE_ORDER);
    }

    showResponce(PEDIDOS_API_ANSWER_STATUS_OK, ['orderId' => $orderId]);
}

function apiGetBalance()
{
    $userId = userGetUserIdFromSession();
    if (!$userId) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $account = accountGetAccountById($user['accountId']);
    if (!$account) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $balance = helpersMoneyConvertToDecimal($account['balance']);
    showResponce(PEDIDOS_API_ANSWER_STATUS_OK, ['balance' => $balance]);
}

function apiGetUserInfo()
{
    $userId = userGetUserIdFromSession();
    if (!$userId) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $userData = [
        'username' => $user['username'],
        'type'     => $user['type']
    ];
    showResponce(PEDIDOS_API_ANSWER_STATUS_OK, $userData);
}

function apiLoadOrders()
{
    $userId = userGetUserIdFromSession();
    if (!$userId) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user) {
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if ($user['type'] == PEDIDOS_USER_TYPE_AUTHOR) {
        $ids = orderGetOrderIdsByAuthorIdAndStatus($userId);
        $orders = orderGetOrdersByIds($ids);
        showResponce(PEDIDOS_API_ANSWER_STATUS_OK, $orders);
    } elseif ($user['type'] == PEDIDOS_USER_TYPE_EXECUTOR) {
        $ids = orderGetOrderIdsByStatus(PEDIDOS_ORDER_STATUS_READY);
        $orders = orderGetOrdersByIds($ids);
        showResponce(PEDIDOS_API_ANSWER_STATUS_OK, $orders);
    }
}

////

function showResponce($status, $data = [], $errors = [])
{
    $data = [
        'data'   => $data,
        'errors' => $errors,
        'status' => $status
    ];

    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function run()
{
    if (isset(requestGetGETData()['action'])) {
        if (requestGetGETData()['action'] == 'getBalance') {
            apiGetBalance();
        } elseif (requestGetGETData()['action'] == 'getUserInfo') {
            apiGetUserInfo();
        } elseif (requestGetGETData()['action'] == 'loadOrders') {
            apiLoadOrders();
        } elseif (requestGetGETData()['action'] == 'createOrder') {
            createOrder();
        } elseif (requestGetGETData()['action'] == 'makeOrder') {
            makeOrder();
        }
        showResponce(PEDIDOS_API_ANSWER_STATUS_WRONG_ACTION);
    } else {
        showResponce(PEDIDOS_API_ANSWER_STATUS_EMPTY_ACTION);
    }
}

run();