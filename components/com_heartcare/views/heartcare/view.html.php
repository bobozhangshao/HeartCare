<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/2
 * Time: 15:04
 */
class HeartCareViewHeartCare extends JViewLegacy
{
    function display($tpl = null)
    {
        $this->msg = $this->get('Msg');

        parent::display($tpl);
    }
}