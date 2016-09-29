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

class Transactions extends AEntity
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
        $cart = & $_SESSION['cart'];
        if(isset($id)){
            if(!isset($params['type'])){
                $params['type'] = 'C';
            }
            $sessions = db_get_array('SELECT * FROM ?:user_session_products WHERE user_id = ?s AND type = ?s', $id, $params['type']);

            if(!empty($sessions)){
                foreach($sessions as $session){
                    $cart['products'][$session['item_id']] = unserialize($session['extra']);
                }
            }
            
            $cart['user_data'] = fn_get_user_info($id);
            $cart['pending_coupon'] = strtolower(trim($params['coupon_code']));
            
            $cart['calculate_shipping'] = true;
            $cart['recalculate'] = true;
            $cart['change_cart_products'] = true;

            if($params['type'] == "C"){
                fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);
            }
            else{
                fn_calculate_cart_content($cart, $auth, 'S', false, 'I', false);
            }
           if(!empty($cart['products'])){
                foreach($cart['products'] as $key=>$prod){
                    $desc = db_get_row("SELECT short_description,full_description,promo_text FROM ?:product_descriptions WHERE product_id= ?i", $prod['product_id']);
                    $cart['products'][$key]['short_description'] = $desc['short_description'];
                    $cart['products'][$key]['full_description'] = $desc['full_description'];
                    $cart['products'][$key]['promo_text'] = $desc['promo_text'];
                }
           }

            $cart['total_products'] = count($cart['products']);
            $status = Response::STATUS_CREATED;
        }

        return array(
            'status' => $status,
            'data' => $cart
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
        
        if(!empty($id)){
            if(!empty($params['transaction_id'])){
                if(empty($params['order_status'])){
                    $params['order_status'] = "I";
                }

                if(empty($params['reason_text'])){
                    $params['reason_text'] = "Cancelled";
                }

                fn_finish_payment($id, $params, false);
                //fn_order_placement_routines('route', $id);
                $status = Response::STATUS_NO_CONTENT;
                

           }
        }

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function delete($id)
    {
       
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
