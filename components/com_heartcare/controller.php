<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/10/8
 * Time: 下午2:36
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe url parameters and their variable types,
     *                               for valid values see {@link JFilterInput::clean()}.
     *
     * @return  WeblinksController  This object to support chaining.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        $cachable	= true;	// Huh? Why not just put that in the constructor?
        $user		= JFactory::getUser();

        // Set the default view name and format from the Request.
        // Note we are using user_id to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $id    = $this->input->getInt('user_id');
//
//        echo "<pre>";
//        var_dump($user->get('id'));
//        echo "hh";
//        echo "</pre>";

        $vName = $this->input->get('view', 'heartcare');
        $this->input->set('view', $vName);

        if ($user->get('id'))
        {
            $cachable = false;
        }

        $safeurlparams = array(
            'id'				=> 'INT',
            'limit'				=> 'UINT',
            'limitstart'		=> 'UINT',
            'filter_order'		=> 'CMD',
            'filter_order_Dir'	=> 'CMD',
            'lang'				=> 'CMD'
        );

        // Check for edit form.
        if ($vName == 'form' && !$this->checkEditId('com_heartcare.edit.user', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
        }

        return parent::display($cachable, $safeurlparams);
    }

}