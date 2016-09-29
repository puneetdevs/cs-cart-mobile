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

/**
 * Getting vendor terms and conditions
 * @param  mixed  $company_ids Array or int
 * @param  string $lang_code   Language code
 * @return array
 */
function fn_get_vendor_terms($company_ids = 0, $lang_code = DESCR_SL)
{
    $conditions = array(
        db_quote("TRIM(terms) <> '' AND d.lang_code = ?s", $lang_code)
    );

    if ($company_ids) {
        $conditions[] = db_quote("company_id IN(?n)", (array)$company_ids);
    }

    $terms = db_get_array(
        "SELECT company_id, company, terms"
        . " FROM ?:companies c"
        . " JOIN ?:company_descriptions d USING(company_id)"
        . " WHERE " . implode(' AND ', $conditions)
    );

    return $terms;
}
