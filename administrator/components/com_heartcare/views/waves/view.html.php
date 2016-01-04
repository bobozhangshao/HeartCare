<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/16
 * Time: 17:09
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareViewWaves extends JViewLegacy
{
    protected $item;
    protected $form;
    protected $txtData;

    function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->txtData = $this->get('TxtData');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        $this->addToolBar();
        // Display the template
        parent::display($tpl);
    }

    protected function addToolBar()
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

        JToolbarHelper::apply('waves.apply');
    }
}