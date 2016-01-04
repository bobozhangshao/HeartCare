<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/5
 * Time: 17:21
 */

defined('_JEXEC') or die('Restricted access');

class HeartCareViewUser extends JViewLegacy
{
    //view form
    protected $form;
    protected $item;
    //protected $canDo;
    protected $measureData;
    protected $devices;
    //display user视图
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        //item就是一个用户的对象实例
        $this->item = $this->get('Item');
        $this->measureData = $this->get('MeasureData');
        $this->state = $this->get('State');
        $this->devices = $this->get('Devices');

        //check errors
        if(count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        $this->addToolBar();

        parent::display($tpl);

        $this->setDocument();

    }


    protected function addToolBar()
    {
        $input = JFactory::getApplication()->input;

        //隐藏Joomla 管理主菜单
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        $title = JText::_('COM_HEARTCARE_MANAGER_HEARTCARE_USER_EDIT');

        JToolbarHelper::title($title, 'user');
        JToolbarHelper::apply('user.apply');
        JToolbarHelper::save('user.save');
        JToolbarHelper::cancel('user.cancel', $isNew? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }


    //设置浏览器title
    protected function setDocument()
    {
        //$isNew = ($this->item->id<1);
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_HEARTCARE_USER_EDITING'));
    }
}