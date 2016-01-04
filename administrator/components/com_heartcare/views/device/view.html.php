<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/14
 * Time: 19:54
 */

defined('_JEXEC') or die('Restricted access');

class HeartCareViewDevice extends JViewLegacy
{
    //view form
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        //item就是一个用户的对象实例
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $this->script = $this->get('Script');

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


        $title = JText::_('COM_HEARTCARE_MANAGER_HEARTCARE_DEVICE_EDIT');

        JToolbarHelper::title($title, 'device');
        JToolbarHelper::apply('device.apply');
        JToolbarHelper::save('device.save');
        JToolbarHelper::cancel('device.cancel','JTOOLBAR_CLOSE');
    }

    /**
     * 设置浏览器title
     * 添加两个javascript
     **/
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_HEARTCARE_DEVICE_EDITING'));
        $document->addScript(JUri::root().$this->script);
        $document->addScript(JUri::root()."/administrator/components/com_heartcare/views/device/submitbutton.js");
        JText::script('COM_HEARTCARE_DEVICE_ID_OR_TYPE_ERROR_UNACCPTABLE');
    }
}