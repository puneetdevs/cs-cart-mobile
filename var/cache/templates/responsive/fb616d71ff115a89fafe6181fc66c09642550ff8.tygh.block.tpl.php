<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:14:30
         compiled from "C:\wamp64\www\cscart\design\themes\responsive\templates\views\block_manager\render\block.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1472257eb9806207243-56409459%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb616d71ff115a89fafe6181fc66c09642550ff8' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\themes\\responsive\\templates\\views\\block_manager\\render\\block.tpl',
      1 => 1475057512,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '1472257eb9806207243-56409459',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'block' => 0,
    'content_alignment' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb98062de5c9_53800358',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb98062de5c9_53800358')) {function content_57eb98062de5c9_53800358($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['block']->value['user_class']||$_smarty_tpl->tpl_vars['content_alignment']->value=='RIGHT'||$_smarty_tpl->tpl_vars['content_alignment']->value=='LEFT') {?>
    <div class="<?php if ($_smarty_tpl->tpl_vars['block']->value['user_class']) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['block']->value['user_class'], ENT_QUOTES, 'UTF-8');
}
if ($_smarty_tpl->tpl_vars['content_alignment']->value=='RIGHT') {?> ty-float-right<?php } elseif ($_smarty_tpl->tpl_vars['content_alignment']->value=='LEFT') {?>
    ty-float-left<?php }?>">
<?php }?>
        <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php if ($_smarty_tpl->tpl_vars['block']->value['user_class']||$_smarty_tpl->tpl_vars['content_alignment']->value=='RIGHT'||$_smarty_tpl->tpl_vars['content_alignment']->value=='LEFT') {?>
    </div>
<?php }?><?php }} ?>
