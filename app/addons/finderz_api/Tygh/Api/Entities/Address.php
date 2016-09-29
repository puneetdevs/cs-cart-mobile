<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Api\Entities;

use Tygh\Api\AEntity;
use Tygh\Api\Response;
use Tygh\Registry;

class Address extends AEntity
{
    /**
     * Gets user data for a specified id; if no id specified, gets user list
     * satisfying filter conditions specified  in params
     *
     * @param  int   $id     User identifier
     * @param  array $params Filter params (user_ids param ignored on getting one user)
     * @return mixed
     */
    public function index($id = 0, $params = array())
    {
    	$status = Response::STATUS_NOT_FOUND;
        if(empty($params['country_code'])){
        	$data = fn_get_simple_countries(true, CART_LANGUAGE);
        	if(!empty($data)){
        		$status = Response::STATUS_OK;
        	}
        }
        else{
        	$data = fn_get_country_states($params['country_code'], true, CART_LANGUAGE);
        	if(!empty($data)){
        		$status = Response::STATUS_OK;
        	}
        	
        }

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function create($params)
    {
        
    }

    public function update($id, $params)
    {
        $auth = $this->auth;
        $data = 'Not found';
        $profile_id = '';
        $profile_type = "P";
        $status = Response::STATUS_NOT_FOUND;
        
        if($id){
            $profiles = fn_get_user_profiles($id);
            if(!empty($profiles)){
                foreach($profiles as $profile){
                    $profile_id = $profile['profile_id'];
                    $profile_type = $profile['profile_type'];
                }
            }
            if(empty($params['shipping'])){
                $params['shipping'] = false;
            }
            $rs = fn_update_user($id, $params['user_data'], $auth, $params['shipping'], false);
            $status = Response::STATUS_NO_CONTENT;
            if(!empty($rs)){
               $status = Response::STATUS_OK;
			   $data = 'Not found';
            }
            
            
        }      


        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function delete($id)
    {
        $data = array();
        $status = Response::STATUS_BAD_REQUEST;

        if (fn_delete_user($id)) {
            $status = Response::STATUS_NO_CONTENT;
        } elseif (!fn_notification_exists('extra', 'user_delete_no_permissions')) {
            $status = Response::STATUS_NOT_FOUND;
        }

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function privileges()
    {
        return array(
            'create' => 'manage_users',
            'update' => 'manage_users',
            'delete' => 'manage_users',
            'index'  => 'view_users'
        );
    }

    public function childEntities()
    {
        return array(
            'usergroups',
        );
    }
}
