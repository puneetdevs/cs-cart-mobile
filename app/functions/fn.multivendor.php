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
use Tygh\Mailer;
use Tygh\Navigation\LastView;

/* HOOKS */

function fn_mve_get_product_filter_fields(&$filters)
{
    $filters['S'] = array(
        'db_field' => 'company_id',
        'table' => 'products',
        'description' => 'vendor',
        'condition_type' => 'F',
        'variant_name_field' => 'companies.company'
    );
}

function fn_mve_delete_user(&$user_id, &$user_data)
{
    if ($user_data['is_root'] == 'Y') {
        db_query("UPDATE ?:users SET is_root = 'Y' WHERE company_id = ?i LIMIT 1", $user_data['company_id']);
    }
}

function fn_mve_get_user_type_description(&$type_descr)
{
    $type_descr['S']['V'] = 'vendor_administrator';
    $type_descr['P']['V'] = 'vendor_administrators';
}

function fn_mve_get_user_types(&$types)
{
    $company_id = Registry::get('runtime.company_id');
    if ($company_id) {
        unset($types['A']);
    }

    $types['V'] = 'add_vendor_administrator';
}

function fn_mve_user_need_login(&$types)
{
    $types[] = 'V';
}

function fn_mve_place_order(&$order_id, &$action, &$order_status, &$cart, &$auth)
{
    $order_info = fn_get_order_info($order_id);
    if ($order_info['is_parent_order'] != 'Y' && !empty($order_info['company_id'])) {
        // Check if the order already placed
        $payout_id = db_get_field('SELECT payout_id FROM ?:vendor_payouts WHERE order_id = ?i', $order_id);

        $company_data = fn_get_company_data($order_info['company_id']);
        
        $data = array(
            'company_id'   => $order_info['company_id'],
            'order_id'     => $order_id,
            'payout_date'  => TIME,
            'start_date'   => TIME,
            'end_date'     => TIME,
            'order_amount' => $order_info['total'],
        );

        /**
         * Actions before save vendor payout
         *
         * @param array  $order_info   Order info
         * @param array  $company_data Company data info
         * @param string $action       Action
         * @param string $order_status Order status
         * @param array  $cart         Cart array
         * @param array  $data         Data that will be save
         * @param int    $payout_id    Payout ID
         * @param array  $auth         Auth
         */
        fn_set_hook('mve_place_order', $order_info, $company_data, $action, $order_status, $cart, $data, $payout_id, $auth);

        if (empty($payout_id)) {
            $payout_id = db_query('INSERT INTO ?:vendor_payouts ?e', $data);
        } else {
            db_query('UPDATE ?:vendor_payouts SET ?u WHERE payout_id = ?i', $data, $payout_id);
        }
    }

    /**
     * Actions after save vendor payout
     *
     * @param int    $order_id     Order ID
     * @param string $action       Action
     * @param string $order_status Order status
     * @param array  $cart         Cart array
     * @param array  $auth         Auth
     * @param array  $order_info   Order info
     * @param array  $company_data Company data info
     * @param array  $data         Data that will be save
     * @param int    $payout_id    Payout ID
     */
    fn_set_hook('mve_place_order_post', $order_id, $action, $order_status, $cart, $auth, $order_info, $company_data, $data, $payout_id);

    return $payout_id;
}

function fn_mve_get_categories(&$params, &$join, &$condition, &$fields, &$group_by, &$sortings, &$lang_code)
{
    // Restrict categories list for microstore
    if (AREA == 'C' && !empty($params['company_ids'])) {
        $company_id = (int) $params['company_ids'];
        $id_paths = db_get_fields(
            "SELECT id_path FROM ?:categories c"
            . " JOIN ?:category_vendor_product_count p USING(category_id)"
            . " WHERE p.company_id = ?i",
            $company_id
        );
        // Getting not empty categories and their parents
        $vendor_category_ids = array();
        foreach ($id_paths as $id_path) {
            foreach (explode('/', $id_path) as $category_id) {
                $vendor_category_ids[] = $category_id;
            }
        }
        if ($vendor_category_ids) {
            $condition .= db_quote(" AND ?:categories.category_id IN(?n)", array_unique($vendor_category_ids));
        } else {
            $condition .= db_quote(" AND 0");
        }
    }
}

function fn_mve_get_categories_after_sql(&$categories, &$params)
{
    // Rewrite product_count for vendor
    if (!$params['simple'] && $company_id = Registry::get('runtime.company_id')) {
        $products_count = db_get_hash_single_array(
            "SELECT category_id, product_count FROM ?:category_vendor_product_count WHERE company_id = ?i",
            array('category_id', 'product_count'), $company_id
        );
        foreach ($categories as &$category) {
            $category['product_count'] = 0;
            if (!empty($products_count[$category['category_id']])) {
                $category['product_count'] = $products_count[$category['category_id']];
            }
        }
    }
}

function fn_mve_update_product_count_post(&$category_ids)
{
    // Recalculate vendor product count for particular categories
    db_query("DELETE FROM ?:category_vendor_product_count WHERE category_id IN(?n)", $category_ids);
    db_query(
        "INSERT INTO ?:category_vendor_product_count (company_id, category_id, product_count)"
        . " SELECT company_id, category_id, COUNT(product_id)"
        . " FROM ?:products_categories c"
        . " INNER JOIN ?:products p USING(product_id)"
        . " WHERE category_id IN(?n)"
        . " GROUP BY p.company_id, c.category_id",
        $category_ids
    );
}

function fn_mve_delete_category_after(&$category_id)
{
    db_query("UPDATE ?:companies SET categories = ?p", fn_remove_from_set('categories', $category_id));
}

function fn_mve_export_process(&$pattern, &$export_fields, &$options, &$conditions, &$joins, &$table_fields, &$processes)
{
    if (Registry::get('runtime.company_id')) {
        if ($pattern['section'] == 'products') {
            // Limit scope to the current vendor's products only (if in vendor mode)
            $company_condition = fn_get_company_condition('products.company_id', false);
            if (!empty($company_condition)) {
                $conditions[] = $company_condition;
            }
        }

        if ($pattern['section'] == 'products' && $pattern['pattern_id'] == 'product_combinations') {
            $joins[] = 'INNER JOIN ?:products AS products ON (products.product_id = product_options_inventory.product_id)';
        }

        if ($pattern['section'] == 'orders') {
            $company_condition = fn_get_company_condition('orders.company_id', false);

            if (!empty($company_condition)) {
                $conditions[] = $company_condition;
            }
        }

        if ($pattern['section'] == 'users') {
            $company_condition = fn_get_company_condition('orders.company_id', false);

            if (!empty($company_condition)) {
                $u_ids = db_get_fields('SELECT users.user_id FROM ?:users AS users LEFT JOIN ?:orders AS orders ON (users.user_id = orders.user_id) WHERE ' . $company_condition . ' GROUP BY users.user_id');

                if (!empty($u_ids)) {
                    $conditions[] = db_quote('users.user_id IN (?n)', $u_ids);
                }
            }
        }
    }
}

function fn_mve_get_users(&$params, &$fields, &$sortings, &$condition, &$join)
{
    if (isset($params['company_id']) && $params['company_id'] != '') {
        $condition['company_id'] = db_quote(' AND ?:users.company_id = ?i ', $params['company_id']);
    }

    if (Registry::get('runtime.company_id')) {
        if (empty($params['user_type'])) {
            $condition['users_company_id'] = db_quote(" AND (?:users.user_id IN (?n) OR (?:users.user_type != ?s AND" . fn_get_company_condition('?:users.company_id', false) . ")) ", fn_get_company_customers_ids(Registry::get('runtime.company_id')), 'C');
        } elseif (fn_check_user_type_admin_area ($params['user_type'])) {
            $condition['users_company_id'] = fn_get_company_condition('?:users.company_id');
        } elseif ($params['user_type'] == 'C') {
            $condition['users_company_id'] = db_quote(" AND ?:users.user_id IN (?n) ", fn_get_company_customers_ids(Registry::get('runtime.company_id')));
        }
    }
}

/**
 * Hook is used for changing query that selects primary object ID.
 *
 * @param array $pattern Array with import pattern data
 * @param array $_alt_keys Array with key=>value data of possible primary object (used for 'where' condition)
 * @param array $v Array with importing data (one row)
 * @param boolean $skip_get_primary_object_id Skip or not getting Primary object ID
 */
function fn_mve_import_get_primary_object_id(&$pattern, &$_alt_keys, &$v, &$skip_get_primary_object_id)
{
    if ($pattern['section'] == 'products' && $pattern['pattern_id'] == 'products') {
        if (Registry::get('runtime.company_id')) {
            $_alt_keys['company_id'] = Registry::get('runtime.company_id');
        } elseif (!empty($v['company'])) {
            // field vendor is set
            $company_id = fn_get_company_id_by_name($v['company']);

            if ($company_id !== null) {
                $_alt_keys['company_id'] = $company_id;
            } else {
                $skip_get_primary_object_id = true;
            }
        } else {
            // field vendor is not set, so import for the base company
            $_alt_keys['company_id'] = 0;
        }
    }
}

function fn_mve_import_check_product_data(&$v, $primary_object_id, &$options, &$processed_data, &$skip_record)
{
    if (Registry::get('runtime.company_id')) {
        $v['company_id'] = Registry::get('runtime.company_id');
    }

    if (!empty($primary_object_id['product_id'])) {
        $v['product_id'] = $primary_object_id['product_id'];
    } else {
        unset($v['product_id']);
    }

    // Check the category name
    if (!empty($v['Category'])) {
        if (!fn_mve_import_check_exist_category($v['Category'], $options['category_delimiter'], $v['lang_code'])) {
            $processed_data['S']++;
            $skip_record = true;
        }
    }

    if (!empty($v['Secondary categories']) && !$skip_record) {
        $delimiter = ';';
        $categories = explode($delimiter, $v['Secondary categories']);
        array_walk($categories, 'fn_trim_helper');

        foreach ($categories as $key => $category) {
            if (!fn_mve_import_check_exist_category($category, $options['category_delimiter'], $v['lang_code'])) {
                unset($categories[$key]);
            }
        }

        $v['Secondary categories'] = implode($delimiter . ' ', $categories);
    }

    return true;
}


/**
 * Check on exists import category in database
 *
 * @param array $category
 * @param string $delimiter
 * @param string $lang
 * @return bool
 */
function fn_mve_import_check_exist_category($category, $delimiter, $lang)
{
    if (empty($category)) {
        return false;
    }

    static $company_categories_ids = null;

    if ($company_categories_ids === null) {
        $company_categories_ids = Registry::get('runtime.company_data.category_ids');
    }

    if (strpos($category, $delimiter) !== false) {
        $paths = explode($delimiter, $category);
        array_walk($paths, 'fn_trim_helper');
    } else {
        $paths = array($category);
    }

    if (!empty($paths)) {
        $parent_id = 0;

        foreach ($paths as $name) {
            $sql = "SELECT ?:categories.category_id FROM ?:category_descriptions"
                . " INNER JOIN ?:categories ON ?:categories.category_id = ?:category_descriptions.category_id"
                . " WHERE ?:category_descriptions.category = ?s AND lang_code = ?s AND parent_id = ?i";

            $category_id = db_get_field($sql, $name, $lang, $parent_id);

            if (empty($category_id)) {
                return false;
            }

            $parent_id = $category_id;
        }

        if (!empty($company_category_ids) && !in_array($parent_id, $company_category_ids)) {
            return false;
        }

        return true;
    }

    return false;
}

function fn_mve_import_check_object_id(&$primary_object_id, &$processed_data, &$skip_record, $object = 'products')
{
    if (!empty($primary_object_id)) {
        $value = reset($primary_object_id);
        $field = key($primary_object_id);
        $company_id = db_get_field("SELECT company_id FROM ?:$object WHERE $field = ?s", $value);
        if ($company_id != Registry::get('runtime.company_id')) {
            $processed_data['S']++;
            $skip_record = true;
        }
    }

    return true;
}

function fn_import_reset_company_id($import_data)
{
    foreach ($import_data as $key => $data) {
        $import_data[$key]['company_id'] = Registry::get('runtime.company_id');
        unset($import_data[$key]['company']);
    }
}

function fn_mve_import_check_company_id(&$primary_object_id, &$v,  &$processed_data, &$skip_record)
{
    if (!empty($primary_object_id)) {
        $value = reset($primary_object_id);
        $field = key($primary_object_id);

        $company_id = db_get_field('SELECT company_id FROM ?:products WHERE ' . $field . ' = ?s', $value);
    } else {
        $company_id = db_get_field('SELECT company_id FROM ?:products WHERE product_id = ?i', $v['product_id']);
    }

    if ($company_id != Registry::get('runtime.company_id')) {
        $processed_data['S']++;
        $skip_record = true;

        return false;
    }

    return true;
}


function fn_mve_set_admin_notification(&$auth)
{
    if ($auth['company_id'] == 0 && fn_check_permissions('companies', 'manage_vendors', 'admin')) {

        $count = db_get_field("SELECT COUNT(*) FROM ?:companies WHERE status IN ('N', 'P')");

        if ($count > 0) {
            fn_set_notification('W', __('notice'), __('text_not_approved_vendors', array(
                '[link]' => fn_url('companies.manage?status[]=N&status[]=P')
            )), 'K');
        }
    }
}

function fn_mve_get_companies(&$params, &$fields, &$sortings, &$condition, &$join, &$auth, &$lang_code)
{
    if (!empty($params['get_description'])) {
        $fields[] = '?:company_descriptions.company_description';
        $join .= db_quote(' LEFT JOIN ?:company_descriptions ON ?:company_descriptions.company_id = ?:companies.company_id AND ?:company_descriptions.lang_code = ?s ', $lang_code);
    }
}

function fn_mve_delete_order(&$order_id)
{
    $parent_id = db_get_field("SELECT parent_order_id FROM ?:orders WHERE order_id = ?i", $order_id);
    if ($parent_id) {
        $count = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE parent_order_id = ?i", $parent_id);
        if ($count == 1) { //this is the last child order, so we can delete the parent order.
            fn_delete_order($parent_id);
        }
    }
}

function fn_mve_get_user_info_before(&$condition, &$user_id, &$user_fields)
{
    if (trim($condition)) {
        if (Registry::get('runtime.company_id')) {
            $condition = "(user_type = 'V' $condition)";
        }
        $company_customers = db_get_fields("SELECT user_id FROM ?:orders WHERE company_id = ?i", Registry::get('runtime.company_id'));
        if ($company_customers) {
            $condition = db_quote("(user_id IN (?n) OR $condition)", $company_customers);
        }
        $condition = " AND $condition ";
    }
}

function fn_mve_get_product_options(&$fields, &$condition, &$join, &$extra_variant_fields, &$product_ids, &$lang_code)
{
    // FIXME 2tl show admin
    $condition .= fn_get_company_condition('a.company_id', true, '', true);
}

function fn_mve_get_product_global_options_before_select(&$params, &$fields, &$condition, &$join)
{
    // FIXME 2tl show admin
    $condition .= fn_get_company_condition('company_id', true, '', true);
}

function fn_mve_get_product_option_data_pre(&$option_id, &$product_id, &$fields, &$condition, &$join, &$extra_variant_fields, &$lang_code)
{
    // FIXME 2tl show admin
    $condition .= fn_get_company_condition('company_id', true, '', true);
}

function fn_mve_clone_page_pre(&$page_id, &$data)
{
    if (!fn_check_company_id('pages', 'page_id', $page_id)) {
        fn_company_access_denied_notification();
        unset($data);
    }
}

function fn_mve_update_page_post(&$page_data, &$page_id, &$lang_code, &$create, &$old_page_data)
{
    if (empty($page_data['page'])) {
        return false;
    }

    if (!$create) {
        //update page
        $page_childrens = db_get_fields("SELECT page_id FROM ?:pages WHERE id_path LIKE ?l AND parent_id != 0", '%' . $page_id . '%');

        if (!empty($page_childrens)) {
            //update childrens company if we update company for root page.
            if ($page_data['parent_id'] == 0 || $old_page_data['parent_id'] == 0) {
                fn_change_page_company($page_id, $page_data['company_id']);
            }
        }
    }
}
/* FUNCTIONS */

function fn_check_addon_permission($addon)
{
    $schema = fn_get_permissions_schema('vendor');
    $schema = $schema['addons'];

    if (isset($schema[$addon]['permission'])) {
        $permission = $schema[$addon]['permission'];
    }

    return isset($permission) ? $permission : true;
}

function fn_companies_get_payouts($params = array(), $items_per_page = 0)
{
    $params = LastView::instance()->update('balance', $params);

    $default_params = array(
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = array();
    $join = ' ';

    // Define sort fields
    $sortings = array(
        'sort_vendor' => 'companies.company',
        'sort_period' => 'payouts.start_date',
        'sort_amount' => 'payout_amount',
        'sort_date' => 'payouts.payout_date',
    );

    $condition = $date_condition = ' 1 ';

    $join .= ' LEFT JOIN ?:orders AS orders ON (payouts.order_id = orders.order_id)';
    $join .= ' LEFT JOIN ?:companies AS companies ON (payouts.company_id = companies.company_id)';

    // If the sales period not defined, specify it as 'All'
    if (empty($params['time_from']) && empty($params['time_to'])) {
        $params['period'] = 'A';
    }

    if (empty($params['time_from']) && empty($params['period'])) {
        $params['time_from'] = mktime(0, 0, 0, date('n', TIME), 1, date('Y', time()));
    } elseif (!empty($params['time_from'])) {
        $params['time_from'] = fn_parse_date($params['time_from']);
    } else {
        $time_from = true;
    }

    if (empty($params['time_to']) && empty($params['period'])) {
        $params['time_to'] = time();
    } elseif (!empty($params['time_to'])) {
        $params['time_to'] = fn_parse_date($params['time_to']) + 24 * 60 * 60 - 1; //Get the day ending time
    } else {
        $time_to = true;
    }

    if (isset($time_from) || isset($time_to)) {
        $dates = db_get_row('SELECT MIN(start_date) AS time_from, MAX(end_date) AS time_to FROM ?:vendor_payouts');
        if (isset($time_from)) {
            $params['time_from'] = $dates['time_from'];
        }
        if (isset($time_to)) {
            $params['time_to'] = $dates['time_to'];
        }
    }

    // Order statuses condition
    if ($statuses = fn_get_order_payout_statuses()) {
        $condition .= db_quote(' AND (orders.status IN (?a) OR payouts.order_id = 0)', $statuses);
    } else {
        $condition .= db_quote(' AND payouts.order_id = 0');
    }

    $date_sub_conditions = array(
        db_quote(
            "payouts.order_id != 0 AND payouts.start_date >= ?i AND payouts.end_date <= ?i",
            $params['time_from'], $params['time_to']
        ),
        db_quote(
            "payouts.order_id = 0 AND (payouts.start_date BETWEEN ?i AND ?i OR payouts.end_date BETWEEN ?i AND ?i)",
            $params['time_from'], $params['time_to'], $params['time_from'], $params['time_to']
        ),
    );
    $date_condition .= db_quote(' AND ((?p))', implode(') OR (', $date_sub_conditions));

    // Filter by the transaction type
    if (
        !empty($params['transaction_type'])
        && ($params['transaction_type'] == 'income' || $params['transaction_type'] == 'expenditure')
    ) {
        if ($params['transaction_type'] == 'income') {
            $condition .= ' AND (payouts.order_id != 0 OR payouts.payout_amount > 0)';
        } else {
            $condition .= ' AND payouts.payout_amount < 0';
        }
    }

    // Filter by vendor
    if (Registry::get('runtime.company_id')) {
        $params['vendor'] = Registry::get('runtime.company_id');
    }
    if (!empty($params['vendor']) && $params['vendor'] != 'all') {
        $condition .= db_quote(' AND payouts.company_id = ?i', $params['vendor']);
    }

    if (!empty($params['payment'])) {
        $condition .= db_quote(' AND payouts.payment_method like ?l', '%' . $params['payment'] . '%');
    }

    $sorting = db_sort($params, $sortings, 'sort_vendor', 'asc');

    $limit = '';
    $items = db_get_array(
        "SELECT SQL_CALC_FOUND_ROWS * FROM ?:vendor_payouts AS payouts $join"
        . " WHERE $condition AND $date_condition GROUP BY payouts.payout_id $sorting $limit"
    );

    if (!empty($params['items_per_page'])) {
        $params['total_items']= db_get_found_rows();
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    // Calculate balance for the selected period
    $total = array(
        'BCF' => 0, // Ballance carried forward
        'NO'  => 0, // New orders
        'TPP' => 0, // Total period payouts
        'LPM' => 0, // Less Profit Margin
        'TOB' => 0, // Total outstanding balance
    );

    $bcf_query = db_quote(
        "SELECT SUM(payouts.order_amount) + SUM(payouts.payout_amount) AS BCF"
        . " FROM ?:vendor_payouts AS payouts $join"
        . " WHERE $condition AND payouts.start_date < ?i",
        $params['time_from']
    );
    $current_payouts_query = db_quote(
        "SELECT SUM(payouts.order_amount) AS NO,"
         . " SUM(payouts.payout_amount) * (-1) AS TTP,"
         . " SUM(payouts.order_amount) + SUM(payouts.payout_amount) AS LPM"
        . " FROM ?:vendor_payouts AS payouts"
        . " LEFT JOIN ?:orders AS orders ON (payouts.order_id = orders.order_id)"
        . " WHERE $condition AND $date_condition"
    );
    $payouts_query = db_quote(
        "SELECT payouts.*,"
         . " companies.company,"
         . " IF(payouts.order_id <> 0, orders.total, payouts.payout_amount) AS payout_amount,"
         . " IF(payouts.order_id <> 0, payouts.end_date, '') AS date"
        . " FROM ?:vendor_payouts AS payouts $join"
        . " WHERE $condition AND $date_condition"
        . " GROUP BY payouts.payout_id $sorting $limit"
    );

    fn_set_hook('mve_companies_get_payouts', $bcf_query, $current_payouts_query, $payouts_query, $join, $total, $condition, $date_condition);

    $payouts = db_get_array($payouts_query);
    
    $total['BCF'] += db_get_field($bcf_query);

    $current_payouts = db_get_row($current_payouts_query);

    $total['NO'] = $current_payouts['NO'];
    $total['TPP'] = $current_payouts['TTP'];
    $total['LPM'] = $current_payouts['LPM'];
    $total['TOB'] += fn_format_price($total['BCF'] + $total['LPM']);
    $total['LPM'] = $total['LPM'] < 0 ? 0 : $total['LPM'];

    $total['new_period_date'] = db_get_field('SELECT MAX(end_date) FROM ?:vendor_payouts');

    return array($payouts, $params, $total);
}

function fn_get_order_payout_statuses()
{
    $statuses = db_get_fields(
        "SELECT ?:statuses.status"
        . " FROM ?:statuses"
        . " INNER JOIN ?:status_data"
            . " ON ?:status_data.status_id = ?:statuses.status_id"
        . " WHERE type = ?s"
            . " AND param = ?s"
            . " AND value = ?s",
        STATUSES_ORDER,
        'calculate_for_payouts',
        'Y'
    );

    /**
     * Getting order statuses that will be used for vendor payouts
     * @param  array $statuses Statuses
     */
    fn_set_hook('get_order_payout_statuses', $statuses);

    return $statuses;
}

function fn_companies_delete_payout($ids)
{
    if (is_array($ids)) {
        db_query('DELETE FROM ?:vendor_payouts WHERE payout_id IN (?n)', $ids);
    } else {
        db_query('DELETE FROM ?:vendor_payouts WHERE payout_id = ?i', $ids);
    }
}

function fn_companies_add_payout($payment)
{
    $_data = array(
        'company_id' => $payment['vendor'],
        'payout_date' => TIME, // Current timestamp
        'start_date' => fn_parse_date($payment['start_date']),
        'end_date' => fn_parse_date($payment['end_date']),
        'payout_amount' => $payment['amount'] * (-1),
        'payment_method' => $payment['payment_method'],
        'comments' => $payment['comments'],
    );

    if ($_data['start_date'] > $_data['end_date']) {
        $_data['start_date'] = $_data['end_date'];
    }

    db_query('INSERT INTO ?:vendor_payouts ?e', $_data);

    if (isset($payment['notify_user']) && $payment['notify_user'] == 'Y') {
        Mailer::sendMail(array(
            'to' => 'company_support_department',
            'from' => 'default_company_support_department',
            'data' => array(
                'payment' => $payment
            ),
            'tpl' => 'companies/payment_notification.tpl',
            'company_id' => $payment['vendor'],
        ), 'A', fn_get_company_language($payment['vendor']));
    }
}

function fn_get_company_customers_ids($company_id)
{
    return db_get_fields("SELECT DISTINCT(user_id) FROM ?:orders WHERE company_id = ?i", $company_id);
}

function fn_take_payment_surcharge_from_vendor($products = array())
{
    $take_surcharge_from_vendor = false;
    
    /**
     * Getting option 'take payment surcharge from vendor'
     *
     * @param array $products                   Products array
     * @param bool  $take_surcharge_from_vendor Take payment surcharge from vendor flag
     */
    fn_set_hook('take_payment_surcharge', $products, $take_surcharge_from_vendor);

    return $take_surcharge_from_vendor;
}

function fn_mve_update_page_before(&$page_data, &$page_id, &$lang_code)
{
    if (!empty($page_data['page'])) {
        fn_set_company_id($_data, 'company_id', true);
    }
}

function fn_mve_update_product($product_data, $product_id, $lang_code, $create)
{
    if (isset($product_data['company_id'])) {
        // Assign company_id to all product options
        $options_ids = db_get_fields('SELECT option_id FROM ?:product_options WHERE product_id = ?i', $product_id);
        if ($options_ids) {
            db_query("UPDATE ?:product_options SET company_id = ?s WHERE option_id IN (?n)", $product_data['company_id'], $options_ids);
        }
    }
}

/**
 * Changes the result of administrator access to profiles checking
 *
 * @param boolean $result Result of check : true if administeator has access, false otherwise
 * @param string $user_type Types of profiles
 * @return bool Always true
 */
function fn_mve_check_permission_manage_profiles(&$result, &$user_type)
{
    $params = array (
        'user_type' => $user_type
    );
    $result = $result && !fn_is_restricted_admin($params);

    if (Registry::get('runtime.company_id') && $result) {
        $result = ($user_type == 'V' && Registry::get('runtime.company_id'));
    }

    return true;
}

/**
 * Changes defined user type
 *
 * @param char User type
 * @param array $params Request parameters
 * @param string $area current application area
 * @return bool Always true
 */
function fn_mve_get_request_user_type(&$user_type, &$params, &$area)
{
    if ($area == 'A' && empty($params['user_type']) && empty($params['user_id']) && Registry::get('runtime.company_id')) {
        $user_type = 'V';
    }

    return true;
}

function fn_mve_delete_shipping($shipping_id)
{
    db_query("UPDATE ?:companies SET shippings = ?p", fn_remove_from_set('shippings', $shipping_id));
}

/**
 * Applies shipping method to all vendors. Returns count of vendors.
 *
 * @param  int $shipping_id Shipping ID
 * @return int Count of vendors
 */
function fn_apply_shipping_to_vendors($shipping_id)
{
    $companies_count = db_get_field("SELECT COUNT(*) FROM ?:companies WHERE NOT FIND_IN_SET(?i, shippings)",
        $shipping_id
    );

    db_query("UPDATE ?:companies SET shippings = ?p", fn_add_to_set('shippings', $shipping_id));

    return $companies_count;
}

function fn_mve_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by, $lang_code)
{
    // code for products filter by company (vendor)
    if (isset($params['company_id']) && $params['company_id'] != '') {
        $params['company_id'] = intval($params['company_id']);
        $condition .= db_quote(' AND products.company_id = ?i ', $params['company_id']);
    }
}

function fn_mve_logo_types(&$types, &$for_company)
{
    if ($for_company == true) {
        unset($types['favicon']);
        unset($types['theme']['for_layout']);
    }
}

function fn_get_products_companies($products)
{
    $companies = array();

    foreach ($products as $v) {
        $_company_id = !empty($v['company_id']) ? $v['company_id'] : 0;
        $companies[$_company_id] = $_company_id;
    }

    return $companies;
}

function fn_get_vendor_categories($params)
{
    $items = array();

    if (!empty($params['company_ids'])) {
        $items = fn_get_categories($params);
    }

    return $items;
}

function fn_mve_dropdown_object_link_post(&$object_data, &$object_type, &$result)
{
    static $vendor_id;

    if (empty($vendor_id)) {
        $vendor_id = Registry::get('runtime.vendor_id');
    }

    if ($object_type == 'vendor_categories') {
        $result = fn_url('companies.products?category_id=' . $object_data['category_id'] . '&company_id=' . $vendor_id);
    }
}

function fn_mve_settings_variants_image_verification_use_for(&$objects)
{
    $objects['apply_for_vendor_account'] = __('use_for_apply_for_vendor_account');
}

function fn_mve_get_predefined_statuses(&$type, &$statuses, &$status)
{
    if ($type == 'companies') {
        $statuses['companies'] = array(
            'A' => __('active'),
        );
        if ($status == 'N') {
            $statuses['companies']['N'] = __('new');
        }
        $statuses['companies']['P'] = __('pending');
        $statuses['companies']['D'] = __('disabled');
    }
}

function fn_mve_get_company_data(&$company_id, &$lang_code, &$extra, &$fields, &$join, &$condition)
{
    // Vendor shouldn't see another vendor
    $condition .= fn_get_company_condition('companies.company_id');
}
