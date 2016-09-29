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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'checkout' && fn_allowed_for('MULTIVENDOR')) {
    $view = Tygh::$app['view'];
    $cart = &Tygh::$app['session']['cart'];

    if ($view->getTemplateVars('final_step') == $cart['edit_step']) {
        $company_ids = array();
        foreach ($cart['product_groups'] as $group) {
            $company_ids[] = $group['company_id'];
        }
        $view->assign('vendor_terms', fn_get_vendor_terms($company_ids));
    }
}
