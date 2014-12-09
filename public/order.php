<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/system/config.php');
require_once(__DIR__ . '/../src/system/request.php');
require_once(__DIR__ . '/../src/system/cookie.php');
require_once(__DIR__ . '/../src/system/session.php');
require_once(__DIR__ . '/../src/system/template.php');
require_once(__DIR__ . '/../src/system/mysql.php');
require_once(__DIR__ . '/../src/auth.php');
require_once(__DIR__ . '/../src/user.php');
require_once(__DIR__ . '/../src/transaction.php');
require_once(__DIR__ . '/../src/order.php');
require_once(__DIR__ . '/../src/account.php');

function run()
{
    if (!authCheckAuthorized()) {
        authRedirectToAuthPage();
    }

    if ((isset(requestGetGETData()['action'])) && (requestGetGETData()['action'] = 'create')) {

//        $id = transactionCreateLoanMoneyToUser(3,100);
//        if (transactionLockTransactionById($id)){
//            if (accountLoanMoneyToUserByAccountId(3,100)){
//                transactionExecuteTransactionById($id);
//            }
//        }

        $amount = 10;
        $orderData = [
            'authorId'   => 1,
            'executorId' => 2,
            'cost'       => $amount,
            'describe'   => 'somw info',
        ];

        $orderId = orderCreateOrder($orderData);
        if (!$orderId) {
            return false;
        }

        $userId = userGetUserIdFromSession();
        $user = userGetUserByID($userId);
        $accountId = $user['accountId'];

        $transferAccountId = accountGetTransferAccount();

        if (!orderSetStatusLockForProceedLockMoneyById($orderId)) {
            echo '0';
            return false;
        }

        $tr1Id = transactionCreateTransaction($accountId,
            PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_USER_FOR_TRANSFERRING_TO_TRANSFER_ACCOUNT, $amount, $orderId);
        $tr2Id = transactionCreateTransaction($accountId,
            PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_TRANSFER_ACCOUNT_AFTER_TRANSFERRING_FROM_USER, $amount, $orderId);

        if (!$tr1Id) {
            echo "1";
            return false;
        }

        if (!transactionLockTransactionById($tr1Id)) {
            echo '2';
            return false;
        }

        if (!accountMinusMoneyFromAccount($accountId, $amount)) {
            echo '3';
            return false;
        }

        if (!transactionExecuteTransactionById($tr1Id)) {
            echo '4';
            return false;
        }

        if (!transactionLockTransactionById($tr2Id)) {
            echo '5';
            return false;
        }

        if (!accountLoanMoneyAccount($transferAccountId, $amount)) {
            echo '6';
            return false;
        }

        if (!transactionExecuteTransactionById($tr2Id)) {
            echo '7';
            return false;
        }

        if (!orderSetStatusReadyToExecuteById($orderId)) {
            echo '8';
            return false;
        }

        echo 'done!';
    }
}

run();