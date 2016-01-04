<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/12
 * Time: 08:45
 */
defined('_JEXEC') or die ('Restricted Access');

JFactory::getDocument()->addScriptDeclaration('

jQuery(document).ready(function() {
       if("'.sizeof($this->measureData).'"==0){
           jQuery("#table").after("<br><br><legend>'.JText::_('COM_HEARTCARE_USER_NOT_BEEN_MEASURED').'</legend>");
           jQuery("#table").hide();
       }
       if("'.sizeof($this->devices).'"==0){
           jQuery("#devices").after("<br><br><legend>'.JText::_('COM_HEARTCARE_USER_HAVE_NO_DEVICE').'</legend>");
           jQuery("#devices").hide();
       }
       jQuery("legend").css({"background":"aliceblue"});
});
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_heartcare&view=user&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm">
    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_HEARTCARE_USER_DETAILS', true)); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HEARTCARE_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!--            TODO 用户测量数据管理 -->
            <div id="table">
                <legend><?php echo JText::_('COM_HEARTCARE_USERDATA_MANAGE'); ?></legend>
                <div class="row-fluid">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th width="10%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_ID'); ?></th>
                            <th width="20%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_MEASURETIME'); ?></th>
                            <th width="20%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DATATYPE'); ?></th>
                            <th width="20%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_ID'); ?></th>
                            <th width="30%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DATAROUTE'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if(!empty($this->measureData)):  ?>
                            <?php foreach($this->measureData as $i => $row ):
                                ?>
                                <tr>
                                    <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" id="title-<?php echo$row->id; ?>"><?php if($row->id){echo $row->id;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?></a></td>
                                    <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" id="title-<?php echo$row->id; ?>"><?php if($row->measure_time){echo $row->measure_time;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?></a></td>
                                    <td><?php echo $row->data_type; ?></td>
                                    <td><?php echo $row->device_id; ?></td>
                                    <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" id="title-<?php echo$row->id; ?>">
                                            <?php
                                            if($row->data_route)
                                            {
                                                echo $row->data_route;
                                            }
                                            else{
                                                echo JText::_('COM_HEARTCARE_HEALTHDATA_DATAROUTE_NULL');
                                            }
                                            ?>
                                        </a>
                                    </td>

                                    <?php
                                    $link = JRoute::_('index.php?option=com_heartcare&view=waves&layout=edit&id='.$row->id);
                                    echo JHtml::_(
                                        'bootstrap.renderModal',
                                        'measureData'.$row->id.'Modal',
                                        array(
                                            'url' => $link,
                                            'title' => JText::_('COM_HEARTCARE_WAVESHOW'),
                                            'height' => '400px',
                                            'width' => '3000px',
                                            'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
                                                . JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
                                        )
                                    );
                                    ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php echo JHtml::_('form.token'); ?>

                </div>
            </div>
        </fieldset>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'devices', JText::_('COM_HEARTCARE_USER_DEVICES', true)); ?>
        <div id="devices">
            <legend><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_SHOW'); ?></legend>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th width="5"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_GROW_ID'); ?></th>
                    <th width="20"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_ID'); ?></th>
                    <th width="20"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_TYPE'); ?></th>
                    <th width="20"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_REGISTER_TIME'); ?></th>
                    <th width="35"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_PRODUCE_DATE'); ?></th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <td colspan="7"></td>
                </tr>
                </tfoot>

                <tbody>
                <?php if(!empty($this->devices)):  ?>
                    <?php foreach($this->devices as $i => $row ):
                        $link = JRoute::_('index.php?option=com_heartcare&task=device.edit&id='.$row->id);
                        ?>
                        <tr>
                            <td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HEARTCARE_EDIT_DEVICE_INFO'); ?>" ><?php echo $row->id;?></a></td>
                            <td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HEARTCARE_EDIT_DEVICE_INFO'); ?>"  ><?php echo $row->device_id;?></a></td>
                            <td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HEARTCARE_EDIT_DEVICE_INFO'); ?>" ><?php echo $row->device_type;?></a></td>
                            <td><?php echo $row->register_time;?></td>
                            <td><?php echo $row->produce_date;?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
    <input type="hidden" name="task" value="user.edit" />
    <?php echo JHtml::_('form.token') ?>
</form>
