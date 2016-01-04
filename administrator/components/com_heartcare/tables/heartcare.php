<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/4
 * Time: 16:29
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareTableHeartCare extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__users', 'id', $db);
    }
}