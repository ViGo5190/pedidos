<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../src/loader.php');

helpersMakePrivate();

function run()
{

    if ((isset(requestGetGETData()['action'])) && (requestGetGETData()['action'] = 'create')) {

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

        $accountIdTo =4;

        $transferAccountId = accountGetTransferAccount();
        $comissionAccountId = accountGetCommissionAccount();
        var_dump($comissionAccountId);

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

return true;

        ////


        if (!orderSetStatusProceed($orderId)){
            echo 'do1';
            return false;
        }

        $amountProceed = $amount * 0.9;
        $amoutCommission = $amount - $amountProceed;

        $tr3Id = transactionCreateTransaction($transferAccountId,
            PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_USER_ACCOUNT, $amountProceed, $orderId);

        if (!$tr3Id){
            echo "do2";
            return false;
        }

        $tr4Id = transactionCreateTransaction($transferAccountId,
            PEDIDOS_TRANSACTION_TYPE_MINUS_MONEY_FROM_TRANSFER_ACCOUNT_FOR_TRANSFERRING_TO_COMMISSION_ACCOUNT, $amoutCommission, $orderId);
        if (!$tr4Id){
            echo "do3";
            return false;
        }


        $tr5Id = transactionCreateTransaction($accountIdTo,
            PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_USER_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT, $amountProceed, $orderId);

        if (!$tr5Id){
            echo "do4";
            return false;
        }

        $tr6Id = transactionCreateTransaction($comissionAccountId,
            PEDIDOS_TRANSACTION_TYPE_LOAN_MONEY_TO_COMMISSION_ACCOUNT_AFTER_TRANSFERRING_FROM_TRANSFER_ACCOUNT, $amoutCommission, $orderId);

        if (!$tr6Id){
            echo "do5";
            return false;
        }


        if (!transactionLockTransactionById($tr3Id)){
            echo 'do6';
            return false;
        }

        if (!accountMinusMoneyFromAccount($transferAccountId,$amountProceed)){
            echo 'do7';
            return false;
        }

        if (!transactionExecuteTransactionById($tr3Id)){
            echo 'do8';
            return false;
        }


        if (!transactionLockTransactionById($tr4Id)){
            echo 'do9';
            return false;
        }

        if (!accountMinusMoneyFromAccount($transferAccountId,$amoutCommission)){
            echo 'do10';
            return false;
        }

        if (!transactionExecuteTransactionById($tr4Id)){
            echo 'do11';
            return false;
        }


        if (!transactionLockTransactionById($tr5Id)){
            echo 'do9';
            return false;
        }

        if (!accountLoanMoneyAccount($accountIdTo,$amountProceed)){
            echo 'do10';
            return false;
        }

        if (!transactionExecuteTransactionById($tr5Id)){
            echo 'do11';
            return false;
        }


        if (!transactionLockTransactionById($tr6Id)){
            echo 'do12';
            return false;
        }

        if (!accountLoanMoneyAccount($comissionAccountId,$amoutCommission)){
            echo 'do13';
            return false;
        }

        if (!transactionExecuteTransactionById($tr6Id)){
            echo 'do14';
            return false;
        }


        if (!orderSetStatusDoneById($orderId)){
            echo 'do15';
        }

        echo 'maked';








    }



}

run();