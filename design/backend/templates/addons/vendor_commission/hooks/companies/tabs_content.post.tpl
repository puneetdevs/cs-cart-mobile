<div id="content_categories" class="hidden">
    {hook name="companies:categories"}
        {include file="pickers/categories/picker.tpl" multiple=true input_name="company_data[categories]" item_ids=$company_data.categories data_id="category_ids" no_item_text=__("text_all_categories_included") use_keys="N" but_meta="pull-right"}
    {/hook}
</div>
