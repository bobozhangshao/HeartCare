<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/5
 * Time: 09:38
 */

defined('_JEXEC') or die('Restricted access');

class HeartCareViewDevices extends JViewLegacy
{
    //display users的视图
    function display($tpl = null)
    {
        //获取application
        $app = JFactory::getApplication();
        $context = "heartcare.list.admin.device";

        //从model获得数据
        $this->items            = $this->get('Items');
        $this->pagination       = $this->get('Pagination');
        $this->state            = $this->get('State');
        $this->filter_order     = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'register_time', 'cmd');
        $this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
        $this->filterForm       = $this->get('FilterForm');
        $this->activeFilters    = $this->get('ActiveFilters');

        //用户有什么权限,他能做什么
        $this->canDo = HeartCareHelper::getActions();

        //检查错误
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        HeartCareHelper::addSubmenu('devices');

        //设置toolbar, 和
        $this->addToolBar();


        parent::display($tpl);

        //set the document
        $this->setDocument();
    }

    protected function addToolBar()
    {
        JToolbarHelper::title(JText::_('COM_HEARTCARE_MANAGER_DEVICES'));
        if($this->canDo->get('core.create'))
        {
            JToolbarHelper::addNew('device.add');
        }
        if($this->canDo->get('core.edit'))
        {
            JToolbarHelper::editList('device.edit','JTOOLBAR_EDIT');
        }

        if ($this->canDo->get('core.delete'))
        {
            JToolbarHelper::deleteList('','devices.delete','JTOOLBAR_DELETE');
        }

        if ($this->canDo->get('core.admin'))
        {
            JToolbarHelper::divider();
            JToolbarHelper::preferences('com_heartcare');
        }
    }

    protected function getSortFields()
    {
        return array(
            'device_id' => JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_ID'),
            'device_type' => JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_TYPE'),
            'register_time' => JText::_('COM_HEARTCARE_HEALTHDATA_DEVICE_REGISTER_TIME'),
            'name' => JText::_('COM_USERS_HEADING_NAME'),
            'username' => JText::_('JGLOBAL_USERNAME'),
            'user_id' => JText::_('COM_HEARTCARE_USER_ID')
        );
    }

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_HEARTCARE_ADMINISTRATION'));
    }
}

