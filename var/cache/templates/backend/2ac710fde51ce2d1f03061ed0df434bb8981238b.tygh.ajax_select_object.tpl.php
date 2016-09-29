<?php /* Smarty version Smarty-3.1.21, created on 2016-09-28 13:12:56
         compiled from "C:\wamp64\www\cscart\design\backend\templates\common\ajax_select_object.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2684157eb97a90025a1-98133577%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2ac710fde51ce2d1f03061ed0df434bb8981238b' => 
    array (
      0 => 'C:\\wamp64\\www\\cscart\\design\\backend\\templates\\common\\ajax_select_object.tpl',
      1 => 1468469536,
      2 => 'tygh',
    ),
  ),
  'nocache_hash' => '2684157eb97a90025a1-98133577',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'type' => 0,
    'id' => 0,
    'text' => 0,
    'label' => 0,
    'js_action' => 0,
    'objects' => 0,
    'runtime' => 0,
    'item' => 0,
    'name' => 0,
    'data_url' => 0,
    'result_elm' => 0,
    'extra_content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_57eb97a90fb907_00709118',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57eb97a90fb907_00709118')) {function content_57eb97a90fb907_00709118($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\modifier.truncate.php';
if (!is_callable('smarty_block_inline_script')) include 'C:/wamp64/www/cscart/app/functions/smarty_plugins\\block.inline_script.php';
?><?php
fn_preload_lang_vars(array('search','loading'));
?>
<?php $_smarty_tpl->_capture_stack[0][] = array("ajax_select_content", null, null); ob_start(); ?>

<a class="<?php if ($_smarty_tpl->tpl_vars['type']->value!='list') {?>btn-text<?php }?> dropdown-toggle" data-toggle="dropdown">
    <span id="sw_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
_wrap_"><?php echo htmlspecialchars(smarty_modifier_truncate($_smarty_tpl->tpl_vars['text']->value,40,"...",true), ENT_QUOTES, 'UTF-8');?>
</span>
    <b class="caret"></b>
</a>

<?php if ($_smarty_tpl->tpl_vars['label']->value) {?><label><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['label']->value, ENT_QUOTES, 'UTF-8');?>
</label><?php }?>

<?php if ($_smarty_tpl->tpl_vars['js_action']->value) {?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('inline_script', array()); $_block_repeat=true; echo smarty_block_inline_script(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo '<script'; ?>
 type="text/javascript">
(function(_, $) {
    $.ceEvent('on', 'ce.picker_js_action_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
', function(elm) {
        <?php echo $_smarty_tpl->tpl_vars['js_action']->value;?>

    });
}(Tygh, Tygh.$));
<?php echo '</script'; ?>
><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_inline_script(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>

<ul class="dropdown-menu <?php if ($_smarty_tpl->tpl_vars['type']->value=="opened") {?>dropdown-opened<?php }?>" id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
_ajax_select_object">
    <li>
        <div id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
_wrap_" class="search-shop cm-smart-position">
            <input type="text" value="<?php echo $_smarty_tpl->__("search");?>
..." class="span3 input-text cm-hint cm-ajax-content-input" data-ca-target-id="content_loader_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
" size="16">
        </div>
    </li>
    <li>
        <div class="ajax-popup-tools" id="scroller_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
">
            <ul class="cm-select-list" id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
">
            <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_smarty_tpl->tpl_vars["object_id"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['objects']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
 $_smarty_tpl->tpl_vars["object_id"]->value = $_smarty_tpl->tpl_vars["item"]->key;
?>
                <?php if ($_smarty_tpl->tpl_vars['runtime']->value['customization_mode']['live_editor']) {?>
                    <?php $_smarty_tpl->tpl_vars["name"] = new Smarty_variable($_smarty_tpl->tpl_vars['item']->value['name'], null, 0);?>
                <?php } else { ?>
                    <?php $_smarty_tpl->tpl_vars["name"] = new Smarty_variable(smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['name'],40,"...",true), null, 0);?>
                <?php }?>
                <li><a data-ca-action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['value'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['name'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8');?>
</a></li>
            <?php } ?>
            <!--<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
--></ul>
            <ul>
                <li id="content_loader_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
" class="cm-ajax-content-more ajax-content-more" data-ca-target-url="<?php echo htmlspecialchars(fn_url($_smarty_tpl->tpl_vars['data_url']->value), ENT_QUOTES, 'UTF-8');?>
" data-ca-target-id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8');?>
" data-ca-result-id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result_elm']->value, ENT_QUOTES, 'UTF-8');?>
"><span><?php echo $_smarty_tpl->__("loading");?>
</span></li>
            </ul>
        </div>
    </li>
    <?php echo $_smarty_tpl->tpl_vars['extra_content']->value;?>

</ul>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['type']->value=='list') {?>
    <li class="dropdown vendor-submenu"><?php echo Smarty::$_smarty_vars['capture']['ajax_select_content'];?>
</li>
<?php } else { ?>
    <div class="btn-group"><?php echo Smarty::$_smarty_vars['capture']['ajax_select_content'];?>
</div>
<?php }?><?php }} ?>
