<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:14:43
         compiled from "C:\wamp64\www\cscart\design\themes\responsive\templates\blocks\wrappers\mainbox_simple.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2928257eb9813ca3d54-27108209%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bdf05bb7b718cb0e4e0568333d8cdd6b18d375bd' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\themes\\responsive\\templates\\blocks\\wrappers\\mainbox_simple.tpl',
      1 => 1475057511,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '2928257eb9813ca3d54-27108209',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'runtime' => 0,
    'content' => 0,
    'hide_wrapper' => 0,
    'block' => 0,
    'content_alignment' => 0,
    'title' => 0,
    'auth' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb9813e48218_60856151',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb9813e48218_60856151')) {function content_57eb9813e48218_60856151($_smarty_tpl) {?><?php if (!is_callable('smarty_block_hook')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\block.hook.php';
if (!is_callable('smarty_function_set_id')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\function.set_id.php';
?><?php if ($_smarty_tpl->tpl_vars['runtime']->value['customization_mode']['design']=="Y"&&@constant('AREA')=="C") {
$_smarty_tpl->_capture_stack[0][] = array("template_content", null, null); ob_start();
if (trim($_smarty_tpl->tpl_vars['content']->value)) {?>
    <div class="ty-mainbox-simple-container clearfix<?php if (isset($_smarty_tpl->tpl_vars['hide_wrapper']->value)) {?> cm-hidden-wrapper<?php }
if ($_smarty_tpl->tpl_vars['hide_wrapper']->value) {?> hidden<?php }
if ($_smarty_tpl->tpl_vars['block']->value['user_class']) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['block']->value['user_class'], ENT_QUOTES, 'UTF-8');
}
if ($_smarty_tpl->tpl_vars['content_alignment']->value=="RIGHT") {?> ty-float-right<?php } elseif ($_smarty_tpl->tpl_vars['content_alignment']->value=="LEFT") {?> ty-float-left<?php }?>">
        <?php if ($_smarty_tpl->tpl_vars['title']->value||trim(Smarty::$_smarty_vars['capture']['title'])) {?>
            <h2 class="ty-mainbox-simple-title">
                <?php $_smarty_tpl->smarty->_tag_stack[] = array('hook', array('name'=>"wrapper:mainbox_simple_title")); $_block_repeat=true; echo smarty_block_hook(array('name'=>"wrapper:mainbox_simple_title"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php if (trim(Smarty::$_smarty_vars['capture']['title'])) {?>
                    <?php echo Smarty::$_smarty_vars['capture']['title'];?>

                <?php } else { ?>
                    <?php echo $_smarty_tpl->tpl_vars['title']->value;?>

                <?php }?>
                <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_hook(array('name'=>"wrapper:mainbox_simple_title"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </h2>
        <?php }?>
        <div class="ty-mainbox-simple-body"><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</div>
    </div>
<?php }
list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();
if (trim(Smarty::$_smarty_vars['capture']['template_content'])) {
if ($_smarty_tpl->tpl_vars['auth']->value['area']=="A") {?><span class="cm-template-box template-box" data-ca-te-template="blocks/wrappers/mainbox_simple.tpl" id="<?php echo smarty_function_set_id(array('name'=>"blocks/wrappers/mainbox_simple.tpl"),$_smarty_tpl);?>
"><div class="cm-template-icon icon-edit ty-icon-edit hidden"></div><?php echo Smarty::$_smarty_vars['capture']['template_content'];?>
<!--[/tpl_id]--></span><?php } else {
echo Smarty::$_smarty_vars['capture']['template_content'];
}
}
} else {
if (trim($_smarty_tpl->tpl_vars['content']->value)) {?>
    <div class="ty-mainbox-simple-container clearfix<?php if (isset($_smarty_tpl->tpl_vars['hide_wrapper']->value)) {?> cm-hidden-wrapper<?php }
if ($_smarty_tpl->tpl_vars['hide_wrapper']->value) {?> hidden<?php }
if ($_smarty_tpl->tpl_vars['block']->value['user_class']) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['block']->value['user_class'], ENT_QUOTES, 'UTF-8');
}
if ($_smarty_tpl->tpl_vars['content_alignment']->value=="RIGHT") {?> ty-float-right<?php } elseif ($_smarty_tpl->tpl_vars['content_alignment']->value=="LEFT") {?> ty-float-left<?php }?>">
        <?php if ($_smarty_tpl->tpl_vars['title']->value||trim(Smarty::$_smarty_vars['capture']['title'])) {?>
            <h2 class="ty-mainbox-simple-title">
                <?php $_smarty_tpl->smarty->_tag_stack[] = array('hook', array('name'=>"wrapper:mainbox_simple_title")); $_block_repeat=true; echo smarty_block_hook(array('name'=>"wrapper:mainbox_simple_title"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                <?php if (trim(Smarty::$_smarty_vars['capture']['title'])) {?>
                    <?php echo Smarty::$_smarty_vars['capture']['title'];?>

                <?php } else { ?>
                    <?php echo $_smarty_tpl->tpl_vars['title']->value;?>

                <?php }?>
                <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_hook(array('name'=>"wrapper:mainbox_simple_title"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </h2>
        <?php }?>
        <div class="ty-mainbox-simple-body"><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</div>
    </div>
<?php }
}?><?php }} ?>
