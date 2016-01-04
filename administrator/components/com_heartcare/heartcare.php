<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/10/8
 * Time: 下午2:33
 */
defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tabstate');

//阻止将URL直接放到浏览器中进行浏览.
//if (!JFactory::getUser()->authorise('core.manage', 'com_heartcare'))
//{
//    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
//}

$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-heartcare {background-image: url(./media/com_heartcare/images/ECG-48x48.png);}');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_heartcare'))
{
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('HeartCareHelper', JPATH_COMPONENT.'/helpers/heartcare.php');

$controller	= JControllerLegacy::getInstance('heartCare');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
