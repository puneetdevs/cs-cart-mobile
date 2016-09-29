{if !$runtime.company_id && "MULTIVENDOR"|fn_allowed_for}
<div class="control-group">
    <label class="control-label" for="elm_company_vendor_commission">{__("vendor_commission.vendor_commission")}:</label>
    <div class="controls">
        <input type="text" name="company_data[commission]" id="elm_company_vendor_commission" value="{$company_data.commission}"  />
        <select name="company_data[commission_type]" class="span1">
            <option value="A" {if $company_data.commission_type == "A"}selected="selected"{/if}>{$currencies.$primary_currency.symbol nofilter}</option>
            <option value="P" {if $company_data.commission_type == "P"}selected="selected"{/if}>%</option>
        </select>
    </div>
</div>
{/if}
