<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/../src/loader.php');

const PEDIDOS_API_ANSWER_STATUS_OK=1;
const PEDIDOS_API_ANSWER_STATUS_ERROR=2;
const PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL=3;
const PEDIDOS_API_ANSWER_STATUS_EMPTY_ACTION=4;
const PEDIDOS_API_ANSWER_STATUS_WRONG_ACTION=5;

helpersMakeApiPrivate();

function apiGetBalance(){
    $userId = userGetUserIdFromSession();
    if (!$userId){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $account = accountGetAccountById($user['accountId']);
    if (!$account){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $balance = helpersMoneyConvertToDecimal( $account['balance']);
    showResponce(PEDIDOS_API_ANSWER_STATUS_OK,['balance' => $balance]);
}

function apiGetUserInfo(){
    $userId = userGetUserIdFromSession();
    if (!$userId){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    $userData = [
        'username' => $user['username'],
        'type' => $user['type']
    ];
    showResponce(PEDIDOS_API_ANSWER_STATUS_OK,$userData);
}

function apiLoadOrders(){
    $userId = userGetUserIdFromSession();
    if (!$userId){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }
    $user = userGetUserByID($userId);

    if (!$user){
        showResponce(PEDIDOS_API_ANSWER_STATUS_ERROR_FATAL);
    }

    if ($user['type']==PEDIDOS_USER_TYPE_AUTHOR){
        $ids = orderGetOrderIdsByAuthorIdAndStatus($userId);
        $orders = orderGetOrdersByIds($ids);
        showResponce(PEDIDOS_API_ANSWER_STATUS_OK,$orders);
    } elseif($user['type']==PEDIDOS_USER_TYPE_EXECUTOR)  {
        $ids = orderGetOrderIdsByStatus(PEDIDOS_ORDER_STATUS_READY);
        $orders = orderGetOrdersByIds($ids);
        showResponce(PEDIDOS_API_ANSWER_STATUS_OK,$orders);
    }

}


////


function showResponce($status, $data=[], $errors=[] )
{
    $data = [
        'data' => $data,
        'errors' => $errors,
        'status' =>$status
    ];

    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function run()
{
    if (isset(requestGetGETData()['action'])){
        if (requestGetGETData()['action'] == 'getBalance'){
            apiGetBalance();

        }
        elseif (requestGetGETData()['action'] == 'getUserInfo'){
            apiGetUserInfo();
        }
        elseif (requestGetGETData()['action'] == 'loadOrders'){
            apiLoadOrders();
        }

    }else{

        showResponce(PEDIDOS_API_ANSWER_STATUS_EMPTY_ACTION);
    }



}

run();