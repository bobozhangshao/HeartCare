<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/19
 * Time: 10:26
 */
defined('_JEXEC') or die('Restricted access');

abstract class HeartCareHelper
{
    public static function addSubmenu($vName)
    {
        JSubMenuHelper::addEntry(
            JText::_('COM_HEARTCARE_MANAGER_USERS'),
            'index.php?option=com_heartcare&view=users',
            $vName == 'users'
        );

        JSubMenuHelper::addEntry(
            JText::_('COM_HEARTCARE_MANAGER_DEVICES'),
            'index.php?option=com_heartcare&view=devices',
            $vName == 'devices'
        );
    }

    public static function getActions($messageId = 0)
    {
        $result = new JObject;
        if (empty($messageId)){
            $assetName = 'com_heartcare';
        }
        else {
            $assetName = 'com_heartcare.message.'.(int)$messageId;
        }

        $actions = JAccess::getActions('com_heartcare','component');

        foreach ($actions as $action){
            $result->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
        }

        return $result;
    }
}