w<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/19
 * Time: 15:02
 */
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.core');
?>

<form action="<?php
echo JRoute::_('index?option=com_heartcare&task=heartcare.remove_measure');
//echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
    <div id="front-data">
        <div class="row-fluid">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th width="10%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_ID'); ?></th>
                    <th width="20%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_MEASURETIME'); ?></th>
                    <th width="10%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DATATYPE'); ?></th>
                    <th width="20%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_ID'); ?></th>
                    <th width="30%"><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DATAROUTE'); ?></th>
                    <th width="10%"><?php echo ""; ?></th>
                </tr>
                </thead>

                <tbody>
                <?php if(!empty($this->items)):  ?>
                    <?php foreach($this->items as $i => $row ):
                        ?>
                        <tr>
                            <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" >
                                    <?php if($row->id){echo $row->id;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?>
                                </a>
                            </td>
                            <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" >
                                    <?php if($row->measure_time){echo $row->measure_time;} else{echo JText::_('COM_HEARTCARE_HEALTHDATA_NODATA');}  ?>
                                </a>
                            </td>
                            <td><?php echo $row->data_type; ?></td>
                            <td><?php echo $row->device_id; ?></td>
                            <td><a href="#measureData<?php echo $row->id; ?>Modal" role="button" class="btn btn-link" data-toggle="modal" title="<?php echo JText::_('COM_HEARTCARE_MEASUREDATA_SHOW_DESC'); ?>" >
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
                            <td>
                                <input type="button" onclick="document.getElementById('data_id').value = document.getElementById('measureData<?php echo $row->id; ?>id').value;document.getElementById('data_route').value = document.getElementById('measureData<?php echo $row->id; ?>route').value;document.adminForm.submit()" class="btn btn-mini btn-danger" value="delete"/>
                                <input type="hidden" id="measureData<?php echo $row->id; ?>id" value="<?php echo $row->id; ?>" />
                                <input type="hidden" id="measureData<?php echo $row->id; ?>route" value="<?php echo $row->data_route; ?>" />
                            </td>

                            <?php
                            $link = JRoute::_('index.php?option=com_heartcare&amp;tmpl=component&amp;view=heartcare&amp;layout=modal_weixin&amp;wave_id='.$row->id.'&amp;wave_type='.$row->data_type);
                            echo JHtml::_(
                                'bootstrap.renderModal',
                                'measureData'.$row->id.'Modal',
                                array(
                                    'url' => $link,
                                    'title' => JText::_('COM_HEARTCARE_WAVESHOW'),
                                    'height' => '400px',
                                    'width' => '5000px',
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
    <input type="hidden" id="data_id" name="data_id" value=""/>
    <input type="hidden" id="data_route" name="data_route" value=""/>
</form>