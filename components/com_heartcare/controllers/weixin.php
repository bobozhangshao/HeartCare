<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/5/9
 * Time: 13:28
 */
defined('_JEXEC') or die('Restricted Acceess');

class HeartCareControllerWeixin extends JControllerAdmin
{
    public function test()
    {
        $app = JFactory::getApplication();
        $timestamp = $app->input->get('timestamp','','string');
        $nonce     = $app->input->get('nonce','','string');
        $signature = $app->input->get('signature','','string');
        $echostr   = $app->input->get('echostr','','string');
        $token     = 'heartcare123';
        $array = array($timestamp,$nonce,$token);
        sort($array);
        //2.将排序后的三个参数拼接之后用sha1加密
        $tmpstr = implode('',$array);
        $tmpstr = sha1($tmpstr);
        //3.将加密后的字符串与signature进行对比,判断该请求是否来自微信
        if($tmpstr == $signature && $echostr){
            echo $echostr;
            JFactory::getApplication()->close();
        }
        else
        {
            $this->responseMsg();
            JFactory::getApplication()->close();
        }
    }

    /**
     * 接收事件推送并回复
     * */
    public function responseMsg()
    {
        //1.获取微信推送过来的post数据(xml格式)
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];

        //2.处理消息类型,并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //文本消息,模板
        $template = '<xml>
                          <ToUserName><![CDATA[%s]]></ToUserName>
                          <FromUserName><![CDATA[%s]]></FromUserName>
                          <CreateTime>%s</CreateTime>
                          <MsgType><![CDATA[%s]]></MsgType>
                          <Content><![CDATA[%s]]></Content>
                     </xml>';
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  = 'text';
        //$content  = '';
        //判断该数据包是订阅的事件推送,下面接收数据包xml
        /*
        ToUserName
        FromUserName
        CreateTime
        MsgType
        Event
        EventKey
        Ticket
        */

        if(strtolower($postObj->MsgType) == 'event')
        {
            //订阅事件
            if(strtolower($postObj->Event) == 'subscribe'){
                $msgType = 'text';
                $content = '小蝌蚪!你好,欢迎关注我们.在这里,你将获得心脏健康监护方面的知识,帮助您保持健康的身体.您可以点击<a href="http://www.heartcare.site">我们的网站</a>来了解更多信息.';
                $content = $content."\n回复数字:\n1:查看主页\n2:关于我们\n3:个人中心";
                $content = $content."\n公众账号:".$fromUser;
                $info = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
            }
            elseif(strtolower($postObj->Event) == 'unsubscribe'){
                //TODO 取消关注
            }
        }

        //纯文本回复
        if(strtolower($postObj->MsgType) == 'text')
        {
            $input = strtolower(trim($postObj->Content));
            if($input == 'hello'|| $input == 'hi' || $input == '你好')
            {
                $content  = "Hello,\n欢迎!欢迎您关注我们,在这里,您将获得心脏健康监护方面的知识,帮助您\"心心向荣\".您可以点击<a href=\"http://www.heartcare.site\">我们的网站</a>来了解更多信息";
                $content = $content."\n回复数字:\n1:查看主页\n2:关于我们\n3:个人中心";
                $info = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
            }
            elseif($input>=1 && $input <10)
            {
                switch ($input){
                    case 1:
                        $content = "<a href=\"http://www.heartcare.site\">查看主页</a>";
                        break;
                    case 2:
                        $content = "<a href=\"http://www.heartcare.site/index.php/about-us\">关于我们</a>";
                        break;
                    case 3:
                        $content = "<a href=\"http://www.heartcare.site/index.php/usercenter/health/health-record\">个人中心</a>";
                        break;
                    default:
                        $content = "回复'hello'试一下";
                        $content = $content."\n数字:\n1:查看主页\n2:关于我们\n3:个人中心";
                        break;
                }

                $info = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
            }
            //用户发送图文
            elseif($input = '介绍')
            {


            }
        }
    }

}