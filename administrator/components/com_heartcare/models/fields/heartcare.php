<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/4
 * Time: 12:42
 *
 * 定义了site/views/heartcare/tmpl/default.xml 中的field
 */

defined('_JEXEC') or die('Restricred access');

JFormHelper::loadFieldClass('list');

class JFormFieldHeartCare extends JFormFieldList
{
    //field type
    protected $type = 'User';

    protected function getOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, username');
        $query->from('#__users');
        $db->setQuery((string) $query);
        $users = $db->loadObjectList();
        $options = array();
        
        if ($users)
        {
            foreach($users as $user)
            {
                $options[] = JHtml::_('select.option', $user->id, $user->username);
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}