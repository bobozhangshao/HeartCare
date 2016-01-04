<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/3
 * Time: 09:35
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareTableWaves extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__health_data', 'id', $db);
    }
}