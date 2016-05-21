<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/5/16
 * Time: 21:47
 */

defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.core');
JHtml::_('jquery.framework');

$app   = JFactory::getApplication();
$code  = $app->input->get('code','','string');
$state = $app->input->get('state','','string');
$flag  = $app->input->getInt('flag','3');

if(!$flag)
{
    $flag = $_SESSION['flag'];
}

$js = '';

switch ($flag){
    case 0:
        $js = 'jQuery("#label").text("网络错误请重试");';
        break;
    case 1:
        $js = 'jQuery("#table").hide();alert("绑定成功");jQuery("#success").show();';
        break;
    case 2:
        $js = "alert(\"用户名或密码错误,或者还没有注册HeartCare!\");";
        break;
    case 3:
        $js = "jQuery(\"#label\").text(\"填写HeartCare的用户名和密码,HeartCare账号和微信账号之间为一一对应关系,请慎重绑定.\");";
        break;
    case 4:
        $js = 'jQuery("#table").hide();jQuery("#already").show();';
        break;
    default:
        $js = "alert(\"未知错误,请重试!\");";
        break;
};
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        <?php echo $js;?>
    })
</script>
<form action="<?php echo JRoute::_('index.php?option=com_heartcare&task=weixin.bindWxToHeartCare');?>" enctype="multipart/form-data"  method="post" name="adminForm" id="adminForm">
    <div id="table">
        <p class="info" id="label">填写HeartCare的用户名和密码,HeartCare账号和微信账号之间为一一对应关系,请慎重绑定.</p>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="10%">name</th>
                <th width="90%">value</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>username</td>
                <td><input type="text" name="username" class="input-large" placeholder="username" /></td>
            </tr>
            <tr>
                <td>password</td>
                <td><input type="password" name="password" class="input-large" placeholder="password" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><button type="submit" class="btn btn-large btn-block btn-primary">绑定微信账号</button></td>
            </tr>

            <input type="hidden" name="code" value="<?php echo $code; ?>" />
            <input type="hidden" name="state" value="<?php echo $state; ?>" />
            </tbody>
        </table>
    </div>
    <p class="success" id="success" style="display:none">恭喜您绑定成功,您将可以从微信公众号平台获取更多服务</p>
    <p class="success" id="already" style="display:none">您已经绑定过此账号,请勿重复绑定,您可以从微信公众号平台获取更多服务</p>
    <?php echo JHtml::_('form.token'); ?>

</form>