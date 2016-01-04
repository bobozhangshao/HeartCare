<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/17
 * Time: 09:43
 */
defined('_JEXEC') or die('Restricted Access');


//加载 form field的类型,此处为list
JFormHelper::loadFieldClass('list');

class JFormFieldDataType extends JFormFieldList
{
    //field type
    protected $type = 'DataType';

    protected function getOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, data_type');
        $query->from('#__health_data_category');
        $db->setQuery((string) $query);
        $datatypes = $db->loadObjectList();
        $options = array();

        if ($datatypes)
        {
            foreach ($datatypes as $datatype)
            {
                $options[] = JHtml::_('select.option', $datatype->id, $datatype->data_type);
            }
        }

//        echo "<pre>";
//        var_dump($options);
//        echo "</pre>";

        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}