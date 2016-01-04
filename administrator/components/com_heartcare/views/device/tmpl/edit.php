<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/18
 * Time: 08:38
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=com_heartcare&view=device&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'device_details')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'device_details', JText::_('COM_HEARTCARE_DEVICE_DETAILS', true)); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HEARTCARE_DEVICE_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($this->form->getFieldset('device_details') as $field): ?>
                        <div class="control-group">
                            <?php if ($field->fieldname != 'service'):?>
                                <div>
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('JGLOBAL_FIELDSET_IMAGE_OPTIONS', true)); ?>
        <div class="row-fluid">
            <div class="span6">
                <?php echo $this->form->getControlGroup('images'); ?>
                <?php foreach ($this->form->getGroup('images') as $field) : ?>
                    <?php echo $field->getControlGroup(); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'sensors', JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_SENSORS', true)); ?>
        <div class="row-fluid">
            <div class="span6">
                <?php echo $this->form->getControlGroup('sensors'); ?>
                <?php foreach ($this->form->getGroup('sensors') as $field) : ?>
                    <?php echo $field->getControlGroup(); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'service', JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_SERVICE', true)); ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_SERVICE'); ?></legend>
            <div class="row-fluid">
                <div class="span9">
                    <?php foreach ($this->form->getFieldset() as $field): ?>

                        <?php if ($field->fieldname == 'service'):?>
                            <div>
                                <div><?php echo $field->label; ?></div>
                                <br>
                                <div><?php echo $field->input; ?></div>
                            </div>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
    <input type="hidden" name="task" value="device.edit" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
