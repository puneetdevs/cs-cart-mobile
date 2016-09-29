<?php
namespace Tygh\Api\Entities;

use Tygh\Api\AEntity;
use Tygh\Api\Response;

class Login extends AEntity
{
    public function index($id = 0, $params = array())
    {
        list($code, $message,$user_data) = fn_api_login_authenticate_user($params['user_login'], $params['password']);
       
        return array(
            'status' => Response::STATUS_OK,
            'data' => array(
                "message_code" => $code,
                "auth_message" => $message,
                "user_data" => $user_data,
                "security_hash" => fn_generate_security_hash()
            ),
        );
    }

    public function create($params)
    {
        error_log("creteindex");
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data' => array()
        );
    }

    public function update($id, $params)
    {
        error_log("updateindex");
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data' => array()
        );
    }

    public function delete($id)
    {
        error_log("deleteindex");
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        );
    }
}