<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/14
 * Time: 19:07
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareControllerDevices extends JControllerAdmin
{
    public function getModel($name = 'Device', $prefix = 'HeartCareModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function delete()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $ids = $this->input->get('cid', array(), 'array');

        if (!JFactory::getUser()->authorise('core.admin', $this->option))
        {
            JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
            jexit();
        }
        elseif (empty($ids))
        {
            JError::raiseWarning(500, JText::_('COM_HEARTCARE_NO_DEVICE_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            JArrayHelper::toInteger($ids);

            // Remove the items.
            if (!$model->delete($ids))
            {
                JError::raiseWarning(500, $model->getError());
            }
            else
            {
                $this->setMessage(JText::plural('COM_HEARTCARE_N_DEVICE_DELETED', count($ids)));
            }
        }

        $this->setRedirect('index.php?option=com_heartcare&view=devices');
    }

}