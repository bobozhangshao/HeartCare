<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/3/28
 * Time: 10:59
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareViewUpload extends JViewLegacy
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