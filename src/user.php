<?php
/**
 * @author Stan Gumeniuk i@vigo.su
 */

require_once(__DIR__ . '/system/mysql.php');

const PEDIDOS_DB_USER_READ = 'user';
const PEDIDOS_DB_USER_WRITE = 'user';

const PEDIDOS_DEFAULT_COMMISSION_PERCENTAGE = 10;

function userGetUserByID($id, $force = false)
{
    static $userStore = [];

    $id = (int) $id;

    if ((!$force) && (isset($userStore[$id]))) {
        return $userStore[$id];
    }

    $connection = mysqlGetConnection(PEDIDOS_DB_USER_READ);

    $query = 'select id, username, email, `type`, accountId from user where id=?';

    $query_stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($query_stmt, 'i', $id);

    mysqli_stmt_bind_result($query_stmt, $userId, $userUsername, $userEmail, $userType, $userAccountId);

    $users = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $users[] = [
                    'id'            => $userId,
                    'username'      => $userUsername,
                    'email'         => $userEmail,
                    'type'          => $userType,
                    'accountId'       => $userAccountId,
                ];
            }
        }
    }

    if (count($users) === 1) {
        $user = $users[0];
        $userStore[$id] = $user;
        return $user;
    }
    return false;
}

function userSaveUser($userData)
{
    if ($userData['id'] > 0) {
        $connection = mysqlGetConnection(PEDIDOS_DB_USER_WRITE);

        $query = 'UPDATE user SET username=?, email=?, type=?, accountId=?  WHERE id=?';

        $query_stmt = mysqli_prepare($connection, $query);

        mysqli_stmt_bind_param(
            $query_stmt,
            'ssiii',
            $userData['username'],
            $userData['email'],
            $userData['type'],
            $userData['accountId'],
            $userData['id']
        );

        $res = mysqli_stmt_execute($query_stmt);

        return $res;
    }
}

function userCreatePasswordHash($string)
{
    return sha1($string);
}

function userGetUsersByUserNameAndPassword($username, $password)
{
    $connection = mysqlGetConnection(PEDIDOS_DB_USER_READ);

    $passwordHashed = userCreatePasswordHash($password);

    $query = 'select id, username, email, `type` from user where (username=? or email=?) and (password=?)';

    $query_stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($query_stmt, 'sss', $username, $username, $passwordHashed);

    mysqli_stmt_bind_result($query_stmt, $userId, $userUsername, $userEmail, $userType);

    $users = [];

    if (mysqli_stmt_execute($query_stmt)) {
        mysqli_stmt_store_result($query_stmt);
        if (mysqli_stmt_num_rows($query_stmt) > 0) {
            while (mysqli_stmt_fetch($query_stmt)) {
                $users[] = [
                    'id'       => $userId,
                    'username' => $userUsername,
                    'email'    => $userEmail,
                    'type'     => $userType
                ];
            }
        }
    }

    return $users;
}

function userGetUserIdFromSession()
{
    if (authCheckAuthorized()) {
        if (isset(sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID])) {
            return sessionGetAll()[PEDIDOS_AUTH_SESSION_KEY_FOR_USER_ID];
        }
    }

    return false;
}