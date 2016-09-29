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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($dispatch_extra)) {
        if (!empty($_REQUEST['approval_data'][$dispatch_extra])) {
            $_REQUEST['approval_data'] = $_REQUEST['approval_data'][$dispatch_extra];
        }
    }

    if ($mode == 'products_approval' && !empty($_REQUEST['approval_data'])) {
        $status = Registry::get('runtime.action') == 'approve' ? 'Y' : 'N';
        db_query(
            'UPDATE ?:products SET approved = ?s WHERE product_id = ?i',
            $status, $_REQUEST['approval_data']['product_id']
        );

        fn_set_notification('N', __('notice'), __('status_changed'));

        if (
            isset($_REQUEST['approval_data']['notify_user_' . $status])
            && $_REQUEST['approval_data']['notify_user_' . $status] == 'Y'
        ) {
            $lang_code = fn_get_company_language($_REQUEST['approval_data']['company_id']);

            Mailer::sendMail(array(
                'to' => 'company_support_department',
                'from' => 'default_company_support_department',
                'data' => array(
                    'products' => fn_get_product_name(array($_REQUEST['approval_data']['product_id']), $lang_code),
                    'status' => $status,
                    'reason' => $_REQUEST['approval_data']['reason_' . $status]
                ),
                'tpl' => 'addons/vendor_data_premoderation/notification.tpl',
                'company_id' => $_REQUEST['approval_data']['company_id'],
            ), 'A', $lang_code);
        }

    } elseif (($mode == 'm_approve' || $mode == 'm_decline') && !empty($_REQUEST['product_ids'])) {

        if ($mode == 'm_approve') {
            $status = 'Y';
            $reason = $_REQUEST['action_reason_approved'];
            $send_notification =
                isset($_REQUEST['action_notification_approved']) && $_REQUEST['action_notification_approved'] == 'Y';
        } else {
            $status = 'N';
            $reason = $_REQUEST['action_reason_declined'];
            $send_notification =
                isset($_REQUEST['action_notification_declined']) && $_REQUEST['action_notification_declined'] == 'Y';
        }

        if ($send_notification) {
            list($products_data) = fn_get_products(array('pid' => $_REQUEST['product_ids']));

            // Group updated products by companies
            $_companies = array();
            foreach ($products_data as $product) {
                if ($product['approved'] != $status) {
                    $_companies[$product['company_id']]['product_ids'][] = $product['product_id'];
                    if (empty($_companies[$product['company_id']]['lang_code'])) {
                        $_companies[$product['company_id']]['lang_code'] = fn_get_company_language($product['company_id']);
                    }
                }
            }
        }

        db_query('UPDATE ?:products SET approved = ?s WHERE product_id IN (?n)', $status, $_REQUEST['product_ids']);
        fn_set_notification('N', __('notice'), __('status_changed'));

        if ($send_notification) {
            foreach ($_companies as $company_id => $_data) {
                Mailer::sendMail(array(
                    'to' => 'company_support_department',
                    'from' => 'default_company_support_department',
                    'data' => array(
                        'products' => fn_get_product_name($_data['product_ids'], $_data['lang_code']),
                        'status' => $status,
                        'reason' => $reason
                    ),
                    'tpl' => 'addons/vendor_data_premoderation/notification.tpl',
                    'company_id' => $company_id,
                ), 'A', $_data['lang_code']);
            }
        }
    }
}

if ($mode == 'products_approval' && !Registry::get('runtime.company_id')) {
    $params = $_REQUEST;
    $params['extend'][] = 'companies';

    list($products, $search) = fn_get_products(
        $params,
        Registry::get('settings.Appearance.admin_products_per_page'),
        DESCR_SL
    );

    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);
}
