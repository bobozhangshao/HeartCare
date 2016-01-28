<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/25
 * Time: 15:15
 */
defined('_JEXEC') or die('Restricted Access');
?>
<div id="doctor_description" style="border: 5px solid aquamarine; background-color:aliceblue; padding: 1px;"><?php if($this->description[0]->cb_description != '') {echo $this->description[0]->cb_description;} else{echo "<h1>Nothing Here!</h1>";}?></div>
