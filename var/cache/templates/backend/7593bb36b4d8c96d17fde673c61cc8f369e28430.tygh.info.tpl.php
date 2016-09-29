<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:12:36
         compiled from "C:\wamp64\www\cscart\design\backend\templates\addons\call_requests\settings\info.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3023057eb9794a21ed1-33464998%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7593bb36b4d8c96d17fde673c61cc8f369e28430' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\backend\\templates\\addons\\call_requests\\settings\\info.tpl',
      1 => 1468469536,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '3023057eb9794a21ed1-33464998',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'settings' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb9794ae5451_43770928',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb9794ae5451_43770928')) {function content_57eb9794ae5451_43770928($_smarty_tpl) {?><?php
fn_preload_lang_vars(array('call_requests.phone_from_settings'));
?>
<div class="control-group setting-wide call_requests">

    <label for="addon_option_call_requests_phone" class="control-label "><?php echo $_smarty_tpl->__("call_requests.phone_from_settings");?>
:</label>

    <div class="controls">
        <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value['Company']['company_phone'], ENT_QUOTES, 'UTF-8');?>
</p>
    </div>

</div><?php }} ?>
