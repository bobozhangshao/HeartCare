<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/25
 * Time: 14:33
 */
defined('_JEXEC') or die('Restricted Access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_heartcare&task=choose.choosedoctors&user_id='.JFactory::getUser()->id); ?>" method="post" name="adminForm" id="adminForm">
    <div id="front-data">
        <div class="row-fluid">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th width="30%"><?php echo JText::_('COM_HEARTCARE_FRONTEND_DOCTOR_NAME'); ?></th>
                    <th width="25%"><?php echo JText::_('COM_HEARTCARE_FRONTEND_DOCTOR_USERNAME'); ?></th>
                    <th width="25%"><?php echo JText::_('COM_HEARTCARE_FRONTEND_DOCTOR_AVATAR'); ?></th>
                    <th width="20%"><?php echo JText::_('COM_HEARTCARE_FRONTEND_DOCTOR_CHOOSE'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php if(!empty($this->items)):  ?>
                    <?php foreach($this->items as $i => $row ):
                        ?>
                        <tr>
                            <td><a href="#doctor<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>">
                                    <?php if($row->name){echo $row->name;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?>
                                </a>
                            </td>
                            <td><a href="#doctor<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>">
                                    <?php if($row->username){echo $row->username;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?>
                                </a>
                            </td>
                            <td><img width="60" src="<?php if($row->avatar != '') {echo "./images/comprofiler/".$row->avatar;} else{echo "./images/comprofiler/gallery/cartoon_sheep.png";} ?>"></td>
                            <td><input type="checkbox" id="doctor<?php echo $row->id;?>" name="choosed_doctors[]" <?php  if(in_array($row->id,$this->doctors)){echo 'checked="checked"';} ?> value="<?php echo $row->id;?>" onchange="document.adminForm.submit();" title=""></td>

                            <?php
                            $link = JRoute::_('index.php?option=com_heartcare&tmpl=component&view=choose&layout=modal&doctor_id='.$row->id);
                            echo JHtml::_(
                                'bootstrap.renderModal',
                                'doctor'.$row->id.'Modal',
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
</form>