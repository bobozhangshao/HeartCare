<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/25
 * Time: 10:15
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareViewChoose extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $pagination;
    protected $description;
    protected $doctors;

    function display($tpl = null)
    {
        // Get some data from the models
        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->description  = $this->get('Description');
        $this->doctors      = $this->get('ChoosedDoctors');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}