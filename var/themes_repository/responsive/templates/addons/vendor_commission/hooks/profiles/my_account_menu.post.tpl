{if "MULTIVENDOR"|fn_allowed_for && !$user_info.company_id && $settings.Vendors.apply_for_vendor == "Y" && $addons.vendor_commission.show_apply_for_vendor_link == "Y"}
    <li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"companies.apply_for_vendor?return_previous_url=`$return_current_url`"|fn_url}" rel="nofollow">{__("apply_for_vendor_account")}</a></li>
{/if}
