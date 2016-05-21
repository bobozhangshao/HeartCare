<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/5/16
 * Time: 21:47
 */

defined('_JEXEC') or die('Restricted Access');

class HeartCareViewWeixin extends JViewLegacy
{

    function display($tpl = null)
    {

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}