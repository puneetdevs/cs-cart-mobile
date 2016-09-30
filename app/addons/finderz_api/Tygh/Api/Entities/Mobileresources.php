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
use Tygh\BlockManager\Block;

class Mobileresources extends AEntity
{

    public function index($id = 0, $params = array())
    {

         $lang_code = $this->safeGet($params, 'lang_code', DEFAULT_LANGUAGE);

        if ($this->getParentName() == 'categories') {
            $parent_category = $this->getParentData();
            $params['cid'] = $parent_category['category_id'];
        }

        if (!empty($id)) {
            $data = fn_get_banner_data($id, $this->auth, $lang_code, '', true, true, true, false, false, false, false);

            if (empty($data)) {
                $status = Response::STATUS_NOT_FOUND;
            } else {
                $status = Response::STATUS_OK;
            }

        } else {
			
			if (isset($params['block_id']) && $params['block_id'] != '') {
				$block_id = $params['block_id'];
				$data = Block::instance()->getById($block_id, 0, array(), $lang_code);
				if (isset($data['content']['items']['item_ids']))
				{
					$params['item_ids'] = $data['content']['items']['item_ids'];
				}			   
				list($products) = fn_get_banners($params, $lang_code);			   
			}
			else {			
				list($products) = fn_get_banners();
			}
						
            $data = array(
                'banners' => array_values($products),
                'params' => $search
            );
            $status = Response::STATUS_OK;
        }

        return array(
            'status' => $status,
            'data' => $data
        );
    }
public function create($params)
    {
        $data = array();
        $valid_params = true;
        $status = Response::STATUS_BAD_REQUEST;   
		$lang_code = $this->safeGet($params, 'lang_code', DEFAULT_LANGUAGE);		

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function update($id, $params)
    {
        $data = array();
        $status = Response::STATUS_BAD_REQUEST;

        $lang_code = $this->safeGet($params, 'lang_code', DEFAULT_LANGUAGE);
        

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function delete($id)
    {
        $data = array();
        $status = Response::STATUS_BAD_REQUEST;

        return array(
            'status' => $status,
            'data' => $data
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

    public function privilegesCustomer()
    {
        return array(
            'index' => true
        );
    }
  
}
