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
                foreach($ids as $id){
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->delete($db->quoteName('#__comprofiler'));
                    $query->where($db->quoteName('id') . ' = ' .(int) $id);
                    $db->setQuery($query);
                    $db->execute();
                }

                $this->setMessage(JText::plural('COM_HEARTCARE_N_DEVICE_DELETED', count($ids)));
            }
        }

        $this->setRedirect('index.php?option=com_heartcare&view=users');
    }
}