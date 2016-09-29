<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:14:36
         compiled from "C:\wamp64\www\cscart\design\themes\responsive\templates\blocks\static_templates\logo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3187557eb980c358156-83227962%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6277a5a8fd91ca782bcf7513af1f440091dc469a' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\themes\\responsive\\templates\\blocks\\static_templates\\logo.tpl',
      1 => 1475057511,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '3187557eb980c358156-83227962',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'runtime' => 0,
    'logos' => 0,
    'auth' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb980c4a4869_34446476',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb980c4a4869_34446476')) {function content_57eb980c4a4869_34446476($_smarty_tpl) {?><?php if (!is_callable('smarty_function_set_id')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\function.set_id.php';
?><?php if ($_smarty_tpl->tpl_vars['runtime']->value['customization_mode']['design']=="Y"&&@constant('AREA')=="C") {
$_smarty_tpl->_capture_stack[0][] = array("template_content", null, null); ob_start(); ?>
<div class="ty-logo-container">
    <a href="<?php echo htmlspecialchars(fn_url(''), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['alt'], ENT_QUOTES, 'UTF-8');?>
">
        <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_path'], ENT_QUOTES, 'UTF-8');?>
" width="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_x'], ENT_QUOTES, 'UTF-8');?>
" height="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_y'], ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['alt'], ENT_QUOTES, 'UTF-8');?>
" class="ty-logo-container__image" />
    </a>
</div><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();
if (trim(Smarty::$_smarty_vars['capture']['template_content'])) {
if ($_smarty_tpl->tpl_vars['auth']->value['area']=="A") {?><span class="cm-template-box template-box" data-ca-te-template="blocks/static_templates/logo.tpl" id="<?php echo smarty_function_set_id(array('name'=>"blocks/static_templates/logo.tpl"),$_smarty_tpl);?>
"><div class="cm-template-icon icon-edit ty-icon-edit hidden"></div><?php echo Smarty::$_smarty_vars['capture']['template_content'];?>
<!--[/tpl_id]--></span><?php } else {
echo Smarty::$_smarty_vars['capture']['template_content'];
}
}
} else { ?>
<div class="ty-logo-container">
    <a href="<?php echo htmlspecialchars(fn_url(''), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['alt'], ENT_QUOTES, 'UTF-8');?>
">
        <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_path'], ENT_QUOTES, 'UTF-8');?>
" width="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_x'], ENT_QUOTES, 'UTF-8');?>
" height="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['image_y'], ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['theme']['image']['alt'], ENT_QUOTES, 'UTF-8');?>
" class="ty-logo-container__image" />
    </a>
</div><?php }?><?php }} ?>
