<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/10/8
 * Time: 下午2:37
 */

defined('_JEXEC') or die('Restricted Accesss');

$controller = JControllerLegacy::getInstance('HeartCare');

$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

$controller->redirect();