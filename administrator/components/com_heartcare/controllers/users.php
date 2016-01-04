<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/5
 * Time: 16:39
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareControllerUsers extends JControllerAdmin
{
    public function getModel($name = 'user', $prefix = 'HeartCareModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}