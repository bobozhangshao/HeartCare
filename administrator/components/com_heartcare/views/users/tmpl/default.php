<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/5
 * Time: 09:44
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->filter_order);
$listDirn  = $this->escape($this->filter_order_Dir);
?>
<form action="index.php?option=com_heartcare&view=users" method="post" id="adminForm" name="adminForm">
    <div class="row-fluid">
        <div class="span6">
            <?php echo JText::_('COM_HEARTCARE_USERS_FILTER'); ?>
            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th width="1%"><?php echo JText::_('COM_HEARTCARE_NUM'); ?></th>
            <th width="2%"><?php echo JHtml::_('grid.checkall'); ?></th>
            <th width="20%"><?php echo JHtml::_('grid.sort','COM_HEARTCARE_NAME','name', $listDirn, $listOrder); ?></th>
            <th width="20%"><?php echo JHtml::_('grid.sort','COM_HEARTCARE_USERNAME','username', $listDirn, $listOrder); ?></th>
            <th width="25%"><?php echo JText::_('COM_HEARTCARE_EMAIL'); ?></th>
            <th width="20%"><?php echo JHtml::_('grid.sort','COM_HEARTCARE_REGISTERDATE','registerDate', $listDirn, $listOrder); ?></th>
            <th width="7%"><?php echo JHtml::_('grid.sort','COM_HEARTCARE_ID','id', $listDirn, $listOrder); ?></th>
            <th width="5%"><?php echo JHtml::_('grid.sort','COM_HEARTCARE_USER_IS_DOCTOR','cb_is_doctor', $listDirn, $listOrder); ?></th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <td colspan="7">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>

        <tbody>
        <?php if(!empty($this->items)) : ?>
        <?php foreach($this->items as $i => $row) :
                $link = JRoute::_('index.php?option=com_heartcare&task=user.edit&id='.$row->id);
                ?>
        <tr>
            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
            <td><?php if($row->username!='admin'){echo JHtml::_('grid.id', $i, $row->id);} else{echo "";} ?></td>
            <td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HEARTCARE_EDIT_USER_INFO'); ?>" ><?php echo $row->name; ?></a></td>
            <td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HEARTCARE_EDIT_USER_INFO'); ?>" ><?php echo $row->username; ?></a></td>
            <td><?php echo $row->email; ?></td>
            <td><?php echo $row->registerDate; ?></td>
            <td><?php echo $row->id; ?></td>
            <td align="center">
                <?php if($row->cb_is_doctor) :?>
                    <span class="icon-ok"> </span>
                <?php elseif(!$row->cb_is_doctor) :?>
                    <span class="icon-cancel"> </span>
                <?php endif;?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
