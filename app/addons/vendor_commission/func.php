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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_vendor_commission_update_company_pre(&$company_data, &$company_id, &$lang_code, &$can_update)
{
    if (fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.company_id')) {
        unset($company_data['comission'], $company_data['comission_type'], $company_data['categories']);
    }
}

function fn_vendor_commission_get_categories(&$params, &$join, &$condition, &$fields, &$group_by, &$sortings, &$lang_code)
{
    if (Registry::get('runtime.company_id')) {
        $company_id = Registry::get('runtime.company_id');
    } elseif (!empty($params['company_ids'])) {
        $company_id = (int) $params['company_ids'];
    }
    if (!empty($company_id)) {
        $company_data = fn_get_company_data($company_id);
        if (!empty($company_data['category_ids'])) {
            $company_condition = db_quote(' AND ?:categories.category_id IN (?n)', $company_data['category_ids']);
            Registry::set('runtime.vendor_commission_company_condition', $company_condition);
            $condition .= $company_condition;
        }
    }
}

function fn_vendor_commission_get_categories_after_sql(&$categories, &$params, &$join, &$condition, &$fields, &$group_by, &$sortings, &$sorting, &$limit, &$lang_code)
{
    // we can't build the correct tree for vendors if there are not available parent categories
    if ($company_condition = Registry::get('runtime.vendor_commission_company_condition')) {
        Registry::del('runtime.vendor_commission_company_condition');
        $selected_ids = array_keys($categories);

        // so get skipped parent categories ids
        $parent_ids = array();
        foreach ($categories as $v) {
            if ($v['parent_id'] && !in_array($v['parent_id'], $selected_ids)) {
                $parent_ids = array_merge($parent_ids, explode('/', $v['id_path']));
            }
        }

        if ($parent_ids) {
            $_condition = str_replace($company_condition, '', $condition);
            $_condition .= db_quote(' AND ?:categories.category_id IN (?a)', array_unique($parent_ids));
            $fields[] = '1 as disabled'; //mark such categories as disabled
            $parent_categories = db_get_hash_array(
                "SELECT " . implode(',', $fields)
                . " FROM ?:categories"
                . " LEFT JOIN ?:category_descriptions"
                . "  ON ?:categories.category_id = ?:category_descriptions.category_id"
                . "  AND ?:category_descriptions.lang_code = ?s $join"
                . " WHERE 1 ?p $group_by $sorting ?p",
                'category_id', $lang_code, $_condition, $limit
            );

            $categories = $categories + $parent_categories;
        }
    }
}

function fn_vendor_commission_get_category_data(&$category_id, &$field_list, &$join, &$lang_code, &$conditions)
{
    if ($company_id = Registry::get('runtime.company_id')) {
        $company_data = fn_get_company_data($company_id);
        if (!empty($company_data['category_ids'])) {
            $conditions .= db_quote(" AND ?:categories.category_id IN(?n)", $company_data['category_ids']);
        }
    }
}

function fn_vendor_commission_delete_category_after(&$category_id)
{
    db_query("UPDATE ?:companies SET categories = ?p", fn_remove_from_set('categories', $category_id));
}

function fn_vendor_commission_update_product_pre(&$product_data, &$product_id, &$lang_code, &$can_update)
{
    if (!$can_update) {
        return;
    }
    
    if (Registry::get('runtime.company_id')) {
        $company_id = Registry::get('runtime.company_id');
    } elseif (isset($product_data['company_id'])) {
        $company_id = $product_data['company_id'];
    } elseif (!empty($product_id)) {
        $company_id = db_get_field('SELECT company_id FROM ?:products WHERE product_id = ?i', $product_id);
    } else {
        $can_update = false;
    }

    if ($company_id) {
        $company_data = fn_get_company_data($company_id);

        if (!empty($company_data['category_ids'])) {

            if (
                !empty($product_data['main_category'])
                && !in_array($product_data['main_category'], $company_data['category_ids'])
            ) {
                unset($product_data['main_category']);
            }

            if (empty($product_data['category_ids'])) {
                $product_data['category_ids'] = db_get_fields(
                    "SELECT category_id FROM ?:products_categories WHERE product_id = ?i", $product_id
                );
            }
            $product_data['category_ids'] = array_intersect($product_data['category_ids'], $company_data['category_ids']);
            if (empty($product_data['category_ids'])) {
                $can_update = false;
            }
        }

        if (!$can_update) {
            fn_set_notification('E', __('error'), __('vendor_commission.category_is_empty'));
        }
    }
}

function fn_vendor_commission_get_company_data_post(&$company_id, &$lang_code, &$extra, &$company_data)
{
    if ($company_data) {
        $company_data['category_ids'] = array();
        if (!empty($company_data['categories'])) {
            $company_data['category_ids'] = explode(',', $company_data['categories']);
        }
    }
}

function fn_vendor_commission_take_payment_surcharge(&$products, &$take_surcharge_from_vendor)
{
    if (Registry::get('addons.vendor_commission.include_payment_surcharge') == 'Y') {
        $take_surcharge_from_vendor = true;
    }
}

function fn_vendor_commission_mve_place_order(&$order_info, &$company_data, &$action, &$order_status, &$cart, &$data, &$payout_id, &$auth)
{
    $commission = $order_info['total'] > 0 ? $company_data['commission'] : 0;
    $commission_amount = 0;

    if ($company_data['commission_type'] == 'P') {
        // Calculate commission amount and check if we need to include shipping cost
        $shipping_cost = Registry::get('addons.vendor_commission.include_shipping') == 'N' ? $order_info['shipping_cost'] : 0;
        $commission_amount = ($order_info['total'] - $shipping_cost) * $commission / 100;
    } else {
        $commission_amount = $company_data['commission'];
    }


    //Check if we need to take payment surcharge from vendor
    if (Registry::get('addons.vendor_commission.include_payment_surcharge') == 'Y') {
        $commission_amount += $order_info['payment_surcharge'];
    }

    if ($commission_amount > $order_info['total']) {
        $commission_amount = $order_info['total'];
    }

    $data['commission'] = $commission;
    $data['commission_amount'] = $commission_amount;
    $data['commission_type'] = $company_data['commission_type'];
}

function fn_vendor_commission_mve_companies_get_payouts(&$bcf_query, &$current_payouts_query, &$payouts_query, &$join, &$total, &$condition, &$date_condition)
{
    $bcf_query = str_replace(
        'SUM(payouts.order_amount) + SUM(payouts.payout_amount) AS BCF',
        'SUM(payouts.order_amount) + SUM(payouts.payout_amount) - SUM(payouts.commission_amount) AS BCF',
        $bcf_query
    );
    $current_payouts_query = str_replace(
        'SUM(payouts.order_amount) + SUM(payouts.payout_amount) AS LPM',
        'SUM(payouts.order_amount) + SUM(payouts.payout_amount) - SUM(payouts.commission_amount) AS LPM',
        $current_payouts_query
    );
}

// Handlers

function fn_settings_actions_addons_vendor_commission($new_status, $old_status, $on_install)
{
    if ($old_status == 'A') {
        fn_vendor_commission_disable_notification();
    }
}

function fn_vendor_commission_install()
{
    // companies table
    $fields = fn_get_table_fields('companies');
    if (!in_array('categories', $fields)) {
        db_query("ALTER TABLE ?:companies ADD `categories` text");
    }
    if (!in_array('commission', $fields)) {
        db_query("ALTER TABLE ?:companies ADD `commission` decimal(12,2) NOT NULL default '0'");
    }
    if (!in_array('commission_type', $fields)) {
        db_query("ALTER TABLE ?:companies ADD `commission_type` char(1) NOT NULL default 'A'");
    }
    
    // vendor_payouts table. These fields shouldn't remove: They are used by vendor_plans
    $fields = fn_get_table_fields('vendor_payouts');
    if (!in_array('commission_amount', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission_amount` decimal(12,2) NOT NULL default '0'");
    }
    if (!in_array('commission', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission` decimal(12,2) NOT NULL default '0'");
    }
    if (!in_array('commission_type', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission_type` char(1) NOT NULL default 'A'");
    }
}

function fn_vendor_commission_uninstall()
{
    db_query("ALTER TABLE ?:companies DROP `categories`");
    db_query("ALTER TABLE ?:companies DROP `commission`");
    db_query("ALTER TABLE ?:companies DROP `commission_type`");

    fn_vendor_commission_disable_notification();
}

function fn_vendor_commission_disable_notification()
{
    fn_set_notification('W', __('warning'), __('vendor_commission.uninstall_text', array(
        '[menu_href]' => fn_url('menus.manage'),
        '[layouts_href]' => fn_url('block_manager.manage'),
    )));
}

