<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/15
 * Time: 20:57
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareTableDevice extends JTable
{
    protected $_jsonEncode = array('images','sensors');

    function __construct(&$db)
    {
        parent::__construct('#__health_device', 'id', $db);
    }

    public function delete($pk = null)
    {
        return parent::delete($pk);
    }
}