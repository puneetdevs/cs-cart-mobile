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

use Tygh\Enum\ProductFeatures;
use Tygh\Api\AEntity;
use Tygh\Api\Response;
use Tygh\Registry;
use Tygh\Storage;

use Tygh\Session;
include 'App/Addons/Wishlist/Controllers/Frontend/wishlist.php';

class Postcarts extends AEntity
{
    public function index($id = 0, $params = array())
    {
        $cart = '';
        $status = Response::STATUS_BAD_REQUEST;
        if($id){
            $type = "W";
            if($params['type']){
                $type = "C";
            }
            $sessions = db_get_array('SELECT * FROM ?:user_session_products WHERE user_id = ?s AND type = ?s', $id, $type);

            if(!empty($sessions)){
                foreach($sessions as $session){
                    $cart['products'][$session['item_id']] = unserialize($session['extra']);
                }
                $status = Response::STATUS_OK;
            }
            else{
                $status = Response::STATUS_NOT_FOUND;
            }
        }
        return array(
            'status' => $status,
            'data' => $cart
        );
    }

    public function create($params)
    {		
        $auth         = & $_SESSION['auth'];
        $cart         = & $_SESSION['cart'];
        $status       = Response::STATUS_BAD_REQUEST;
		$AmountStatus = '';
        if(isset($params['user_id'])){
            if(!isset($params['type'])){
                $params['type'] = 'C';
            }
			if($params['type'] == 'C'){
			/**Check Amount*/
			
			foreach($params['product_data'] as $ProductData){
				$ProdutId =  $ProductData['product_id'];
				$Result   = db_get_row('SELECT * FROM ?:products WHERE product_id = ?s', $ProdutId);
				if($Result['amount'] >= $ProductData['amount']){
					$AmountStatus = 1;
				}else{
					$AmountStatus = 0;
					$cart['message'] = 'The number of products in the inventory is not enough for your order.';
				}
			}
				
			if($AmountStatus == 1){	
            $sessions = db_get_array('SELECT * FROM ?:user_session_products WHERE user_id = ?s AND type = ?s', $params['user_id'], $params['type']);
				if(!empty($sessions)){
					foreach($sessions as $session){
						$cart['products'][$session['item_id']] = unserialize($session['extra']);
					}
				}
            fn_add_product_to_cart($params['product_data'], $cart, $auth);
            fn_save_cart_content($cart, $params['user_id'],$params['type'],'U');
            $cart['recalculate'] = true;
            $cart['change_cart_products'] = true;
            fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);
            $status = Response::STATUS_CREATED;
			}
			}elseif($params['type'] == 'W'){
				fn_add_product_to_wishlist($params['product_data'],$cart,$auth);
				fn_save_cart_content($cart,$params['user_id'],'W');
				$status = Response::STATUS_CREATED;
			}
           
        }

       

        return array(
            'status' => Response::STATUS_CREATED,
            'data' => $cart
        );
    }

    public function update($id, $params)
    {
        $auth = & $_SESSION['auth'];
        $return = '';
        $status = Response::STATUS_BAD_REQUEST;
        if(isset($id)){
            if(!empty($params['product_id'])){
                 if(!isset($params['type'])){
                        $params['type'] = 'C';
                    }
                 if($params['delete']){
                    //delete code
                    $return = db_query('DELETE FROM ?:user_session_products WHERE user_id = ?i AND product_id = ?i AND type = ?s', $id, $params['product_id'],  $params['type']);
                    $return = 'deleted';
                 }
                 else{
                    if($params['amount'] < 1){
                    //delete code
                    $return = db_query('DELETE FROM ?:user_session_products WHERE user_id = ?i AND product_id = ?i AND type = ?s', $id, $params['product_id'],  $params['type']);
                    $return = 'deleted';
                    }
                    else{
				    /*Update Query */
					  /*Check Amount*/
						$ProdutId  = $params['product_id'];
						$Result    = db_get_row('SELECT * FROM ?:products WHERE product_id = ?s', $ProdutId);
						
						if(($Result['amount'] >= $params['amount'])){
							
                         $extra = db_get_field("SELECT extra FROM ?:user_session_products WHERE user_id = ?i AND product_id = ?i AND type = ?s", $id, $params['product_id'], $params['type']);
                        $extra = unserialize($extra);

                         if(!empty($extra)){
                            $product['product_data'][$extra['product_id']]['product_id'] = $extra['product_id'];
                            $product['product_data'][$extra['product_id']]['amount'] = $params['amount'];
                            if(!empty($extra['product_options'])){
                                foreach($extra['product_options'] as $option=>$selection){
                                    $product['product_data'][$extra['product_id']]['product_options'][$option] = $selection;
                                }
                            }
                         }
                         db_query('DELETE FROM ?:user_session_products WHERE user_id = ?i AND product_id = ?i AND type = ?s', $id, $params['product_id'],  $params['type']);

                         $sessions = db_get_array('SELECT * FROM ?:user_session_products WHERE user_id = ?s AND type = ?s', $id, $params['type']);

                         if(!empty($sessions)){
                             foreach($sessions as $session){
                                 $cart['products'][$session['item_id']] = unserialize($session['extra']);
                             }
                         }
                        fn_add_product_to_cart($product['product_data'], $cart, $auth);
                        fn_save_cart_content($cart, $id,$params['type'],'U');
                        $return = 'updated';
						
						}else{
							 $return['message'] = 'The number of products in the inventory is not enough for your order.';
						} 
                    }
                 }
                               
               $status = Response::STATUS_OK;
               
            }          
            
        }
        else{
            $status = Response::STATUS_NOT_FOUND;
        }

        return array(
            'status' => $status,
            'data' => $return
        );
    }

    public function delete($id)
    {
        $type         = 'C';
        $UserResult   = db_get_row('SELECT * FROM ?:users WHERE user_id = ?s',$id);
        $status = Response::STATUS_OK;
        if(!empty($UserResult)){
            db_query('DELETE FROM ?:user_session_products WHERE user_id = ?i AND type = ?s', $id,$type);
            $status = Response::STATUS_OK;
            $response = 'cart cleared';
        } 
        return array(
            'status' => $status,
            'data' => $response
        );
    }

    public function privileges()
    {
        return array(
            'create' => 'manage_catalog',
            'update' => 'manage_catalog',
            'delete' => 'manage_catalog',
            'index'  => 'view_catalog'
        );
    }

}
