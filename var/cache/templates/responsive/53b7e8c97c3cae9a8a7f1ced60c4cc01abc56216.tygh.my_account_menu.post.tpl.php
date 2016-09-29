<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:14:32
         compiled from "C:\wamp64\www\cscart\design\themes\responsive\templates\addons\vendor_commission\hooks\profiles\my_account_menu.post.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2759857eb9808b64b09-76708523%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53b7e8c97c3cae9a8a7f1ced60c4cc01abc56216' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\themes\\responsive\\templates\\addons\\vendor_commission\\hooks\\profiles\\my_account_menu.post.tpl',
      1 => 1475057526,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '2759857eb9808b64b09-76708523',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'runtime' => 0,
    'user_info' => 0,
    'settings' => 0,
    'addons' => 0,
    'return_current_url' => 0,
    'auth' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb9808ccd6e2_65864207',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb9808ccd6e2_65864207')) {function content_57eb9808ccd6e2_65864207($_smarty_tpl) {?><?php if (!is_callable('smarty_function_set_id')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\function.set_id.php';
?><?php
fn_preload_lang_vars(array('apply_for_vendor_account','apply_for_vendor_account'));
?>
<?php if ($_smarty_tpl->tpl_vars['runtime']->value['customization_mode']['design']=="Y"&&@constant('AREA')=="C") {
$_smarty_tpl->_capture_stack[0][] = array("template_content", null, null); ob_start();
if (fn_allowed_for("MULTIVENDOR")&&!$_smarty_tpl->tpl_vars['user_info']->value['company_id']&&$_smarty_tpl->tpl_vars['settings']->value['Vendors']['apply_for_vendor']=="Y"&&$_smarty_tpl->tpl_vars['addons']->value['vendor_commission']['show_apply_for_vendor_link']=="Y") {?>
    <li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="<?php echo htmlspecialchars(fn_url("companies.apply_for_vendor?return_previous_url=".((string)$_smarty_tpl->tpl_vars['return_current_url']->value)), ENT_QUOTES, 'UTF-8');?>
" rel="nofollow"><?php echo $_smarty_tpl->__("apply_for_vendor_account");?>
</a></li>
<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();
if (trim(Smarty::$_smarty_vars['capture']['template_content'])) {
if ($_smarty_tpl->tpl_vars['auth']->value['area']=="A") {?><span class="cm-template-box template-box" data-ca-te-template="addons/vendor_commission/hooks/profiles/my_account_menu.post.tpl" id="<?php echo smarty_function_set_id(array('name'=>"addons/vendor_commission/hooks/profiles/my_account_menu.post.tpl"),$_smarty_tpl);?>
"><div class="cm-template-icon icon-edit ty-icon-edit hidden"></div><?php echo Smarty::$_smarty_vars['capture']['template_content'];?>
<!--[/tpl_id]--></span><?php } else {
echo Smarty::$_smarty_vars['capture']['template_content'];
}
}
} else {
if (fn_allowed_for("MULTIVENDOR")&&!$_smarty_tpl->tpl_vars['user_info']->value['company_id']&&$_smarty_tpl->tpl_vars['settings']->value['Vendors']['apply_for_vendor']=="Y"&&$_smarty_tpl->tpl_vars['addons']->value['vendor_commission']['show_apply_for_vendor_link']=="Y") {?>
    <li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="<?php echo htmlspecialchars(fn_url("companies.apply_for_vendor?return_previous_url=".((string)$_smarty_tpl->tpl_vars['return_current_url']->value)), ENT_QUOTES, 'UTF-8');?>
" rel="nofollow"><?php echo $_smarty_tpl->__("apply_for_vendor_account");?>
</a></li>
<?php }?>
<?php }?><?php }} ?>
