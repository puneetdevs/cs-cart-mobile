<?php

use Tygh\Api\Response;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

function fn_api_login_authenticate_user($login, $password) {
    $request = array(
        "user_login" => $login,
        "password" => $password
    );

    list($status, $user_data, $user_login, $c_password, $salt) = fn_api_auth_routines($request, $auth);
    

    if ($status === false) {

        return array(Response::STATUS_FORBIDDEN, "Login is forbidden");
    }

    if (!empty($user_data) && !empty($c_password) && fn_generate_salted_password($c_password, $salt) == $user_data['password']) {
        //
        // Success login
        //
        return array(Response::STATUS_OK, "User authenticated",$user_data);
    } else {
        //
        // Login incorrect
        //
        return array(Response::STATUS_UNAUTHORIZED, "User not authenticated");
    }
}

function fn_api_auth_routines($request, $auth) {
    $status = true;

    $user_login = (!empty($request['user_login'])) ? trim($request['user_login']) : '';
    $password = (!empty($request['password'])) ? $request['password'] : '';
    $field = 'email';

    $condition = '';

    if (fn_allowed_for('ULTIMATE')) {
        if (Registry::get('settings.Stores.share_users') == 'N' && AREA != 'A') {
            $condition = fn_get_company_condition('?:users.company_id');
        }
    }

    $user_data = db_get_row("SELECT * FROM ?:users WHERE $field = ?s" . $condition, $user_login);

    if (empty($user_data)) {
        $user_data = db_get_row("SELECT * FROM ?:users WHERE $field = ?s AND user_type IN ('A', 'V', 'P')", $user_login);
    }

    if (!empty($user_data)) {
        $user_data['usergroups'] = fn_get_user_usergroups($user_data['user_id']);
    }

    if (!empty($user_data['status']) && $user_data['status'] == 'D') {
        fn_set_notification('E', __('error'), __('error_account_disabled'));
        $status = false;
    }

    $salt = isset($user_data['salt']) ? $user_data['salt'] : '';

    return array($status, $user_data, $user_login, $password, $salt);
}

function fn_api_login_api_check_access($this, $entity, $method_name, &$can_access) {
    $can_access = true;
}
