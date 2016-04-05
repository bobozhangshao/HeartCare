<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/3/28
 * Time: 11:00
 */

defined('_JEXEC') or die('Restricted Access');
?>


<form action="<?php echo JRoute::_('index.php?option=com_heartcare&task=upload.upload');?>" enctype="multipart/form-data"  method="post" name="adminForm" id="adminForm">
    <div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="10%">form</th>
            <th width="90%">value</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>id</td>
            <td><input type="text" name="user_id" id="user_id" class="input-large" placeholder="UserId" /></td>
        </tr>
        <tr>
            <td>email</td>
            <td><input type="email" name="user_email" class="input-large" placeholder="Email"  /></td>
        </tr>
        <tr>
            <td>username</td>
            <td><input type="text" name="username" class="input-large" placeholder="UserName" /></td>
        </tr>
        <tr>
            <td>date</td>
            <td><input type="datetime-local" name="datetime" /></td>
        </tr>
        <tr>
            <td>deviceid</td>
            <td><input type="text" name="device_id" /></td>
        </tr>
        <tr>
            <td>devicetype</td>
            <td><input type="text" name="device_type" /></td>
        </tr>
        <tr>
            <td>file</td>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                <input type="file" name="file" />
            </td>
        </tr>
        <tr>
            <td>filetype</td>
            <td>
                <select name="datatype">
                    <option value="ECG">ECG</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><button type="submit" class="btn btn-primary">submit</button></td>
        </tr>

        </tbody>
    </table>
    </div>
    <?php echo JHtml::_('form.token'); ?>

</form>