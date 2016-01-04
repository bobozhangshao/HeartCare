<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/18
 * Time: 09:55
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareControllerDevice extends JControllerForm
{
    /**
     * Method to save a record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   12.2
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app      = JFactory::getApplication();
        $model    = $this->getModel('Device', '', array());
        $data     = $this->input->post->get('jform', array(), 'array');
        $task     = $this->getTask();
        $context  = 'com_heartcare.edit.device';
        $recordId = $this->input->getInt('id');

        // Populate the row id from the session.
        $data['id'] = $recordId;

        // Validate the posted data.
        // This post is made up of two forms, one for the item and one for params.
        $form = $model->getForm($data);

        if (!$form)
        {
            JError::raiseError(500, $model->getError());

            return false;
        }

        if ($data['type'] == 'url')
        {
            $data['link'] = str_replace(array('"', '>', '<'), '', $data['link']);

            if (strstr($data['link'], ':'))
            {
                $segments = explode(':', $data['link']);
                $protocol = strtolower($segments[0]);
                $scheme = array('http', 'https', 'ftp', 'ftps', 'gopher', 'mailto', 'news', 'prospero', 'telnet', 'rlogin', 'tn3270', 'wais', 'url',
                    'mid', 'cid', 'nntp', 'tel', 'urn', 'ldap', 'file', 'fax', 'modem', 'git');

                if (!in_array($protocol, $scheme))
                {
                    $app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'warning');
                    $this->setRedirect(
                        JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false)
                    );

                    return false;
                }
            }
        }

        $data = $model->validate($form, $data);

        // Check for the special 'request' entry.
        if ($data['type'] == 'component' && isset($data['request']) && is_array($data['request']) && !empty($data['request']))
        {
            $removeArgs = array();

            // Preprocess request fields to ensure that we remove not set or empty request params
            $request = $form->getGroup('request');

            if (!empty($request))
            {
                foreach ($request as $field)
                {
                    $fieldName = $field->getAttribute('name');

                    if (!isset($data['request'][$fieldName]) || $data['request'][$fieldName] == '')
                    {
                        $removeArgs[$fieldName] = '';
                    }
                }
            }

            // Parse the submitted link arguments.
            $args = array();
            parse_str(parse_url($data['link'], PHP_URL_QUERY), $args);

            // Merge in the user supplied request arguments.
            $args = array_merge($args, $data['request']);

            // Remove the unused request params
            if (!empty($args) && !empty($removeArgs))
            {
                $args = array_diff_key($args, $removeArgs);
            }

            $data['link'] = 'index.php?' . urldecode(http_build_query($args, '', '&'));
            unset($data['request']);
        }

        // Check for validation errors.
        if ($data === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_heartcare.edit.device.data', $data);

            // Redirect back to the edit screen.
            $editUrl = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId);
            $this->setRedirect(JRoute::_($editUrl, false));

            return false;
        }

        // Attempt to save the data.
        if (!$model->save($data))
        {
            // Save the data in the session.
            $app->setUserState('com_heartcare.edit.device.data', $data);

            // Redirect back to the edit screen.
            $editUrl = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId);
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_($editUrl, false));

            return false;
        }

        // Save succeeded, check-in the row.
        if ($model->checkin($data['id']) === false)
        {
            // Check-in failed, go back to the row and display a notice.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
            $redirectUrl = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId);
            $this->setRedirect(JRoute::_($redirectUrl, false));

            return false;
        }

        $this->setMessage(JText::_('COM_HEARTCARE_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState('com_heartcare.edit.device.data', null);
                $app->setUserState('com_heartcare.edit.device.type', null);
                $app->setUserState('com_heartcare.edit.device.link', null);

                // Redirect back to the edit screen.
                $editUrl = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId);
                $this->setRedirect(JRoute::_($editUrl, false));
                break;
            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_heartcare.edit.device.data', null);
                $app->setUserState('com_heartcare.edit.device.type', null);
                $app->setUserState('com_heartcare.edit.device.link', null);

                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }

        return true;
    }

}