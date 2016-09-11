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
    private $appId     = 'wx415579659490a864';
    private $appSecret = '3d6a80a4a4f9f1e927767938c34f5b3e';
    private $token     = 'heartcare123';

    /**
     * 获取weixin的model
     * */
    public function getModel($name = 'Weixin', $prefix = 'HeartCareModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * 微信入口
     * */
    public function test()
    {
        $app = JFactory::getApplication();
        $timestamp = $app->input->get('timestamp','','string');
        $nonce     = $app->input->get('nonce','','string');
        $signature = $app->input->get('signature','','string');
        $echostr   = $app->input->get('echostr','','string');
        $token = $this->token;
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
            $this->definedItems();
            JFactory::getApplication()->close();
        }
    }

    /**
     * 接收事件推送消息
     * */
    public function responseMsg()
    {
        header('content-type:text/html;charset=utf-8');
        //1.获取微信推送过来的post数据(xml格式)
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];

        if(!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            echo $result;
        }else {
            echo "";
        }
    }

    /**
     * 接收事件消息
     * input Object
     * */
    private function receiveEvent($object)
    {
        $content = '';
        $result = '';
        switch ($object->Event)
        {
            case "subscribe":
                $content = '小蝌蚪!你好,欢迎关注我们.在这里,你将获得心脏健康监护方面的知识,帮助您保持健康的身体.您可以点击<a href="http://www.heartcare.site">我们的网站</a>来了解更多信息.';
                $content = $content."\n回复数字:\n1:查看主页\n2:关于我们\n3:个人中心\n4:绑定账号";
                $content = $content."\n您也可以绑定到<a href=\"http://www.heartcare.site/index.php?option=com_heartcare&task=weixin.getCode\">HeartCare账号</a>获取更多信息.";
                $content = $content."\n公众账号:".$object->ToUserName;

                //将新注册的用户微信信息记录到weixin_user表中
                $this->getUserInfo($object);
                break;
            case "CLICK":
                $clickInfo = $this->clickCheckArrange($object);

                switch ($object->EventKey)
                {
                    case "measurementTimes":
                        //绑定了HeartCare
                        if($clickInfo['user'])
                        {
                            $content = "恭喜您已经在我们的系统有[";
                            $content = $content.$clickInfo['count'];
                            $content = $content."]次记录了~\\(≧▽≦)/~啦啦啦";
                        } else {//没有绑定,
                            $content = $clickInfo['count'];
                        }
                        break;
                    case "lastTime":
                        if($clickInfo['user'])
                        {
                            $content = "您最近一次测量时间为:";
                            $content = $content.$clickInfo['last'];
                            $content = $content."\n您最早一次测量时间为:";
                            $content = $content.$clickInfo['first'];
                        } else {
                            $content = $clickInfo['first'];
                        }
                        break;
                    case "checkedDoctors":
                        if ($clickInfo['doctors'])
                        {
                            $i = 1;
                            $content = "您已经选择的医生为:\n";
                            foreach($clickInfo['doctors'] as $key => $value)
                            {
                                $content = $content."第".$i."位:\n";
                                foreach($value as $k => $v)
                                {
                                    $content = $content."$k:$v\n";
                                }
                                $i++;
                            }
                        } else {
                            $content = $clickInfo['doc_info'];
                        }
                        break;
                    case "readRecords":
                        $content = $this->assembleMeasureData($object);
                        break;
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "unsubscribe":
                $this->responseUnsubscribe($object);
                break;
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    /**
     * 接收文本消息
     * input Object
     */
    private function receiveText($object)
    {
        $content = '';
        $result = '';
        switch ( strtolower(trim($object->Content)))
        {
            case "hello":
            case "hi":
            case "你好":
                $content  = "Hello,\n欢迎!欢迎您关注我们,在这里,您将获得心脏健康监护方面的知识,帮助您\"心心向荣\".您可以点击<a href=\"http://www.heartcare.site\">我们的网站</a>来了解更多信息";
                $content = $content."\n回复数字:\n1:查看主页\n2:关于我们\n3:个人中心\n4:绑定账号";
                break;
            case 1:
                $content = array();
                $content[0]['Title']="查看主页,可迅速了解我们的相关服务";
                $content[0]['Description']="期待为您服务,用心关注内心O(∩_∩)O";
                $content[0]['PicUrl'] = "http://www.heartcare.site/images/healthcare/weareddevice.png";
                $content[0]['Url'] = "http://www.heartcare.site/";
                break;
            case 2:
                $content = array();
                $content[0]['Title']="关于我们,你可能想知道更多信息!";
                $content[0]['Description']="期待为您服务,用心关注内心O(∩_∩)O";
                $content[0]['PicUrl'] = "http://www.heartcare.site/images/healthcare/about_us_about_us.jpg";
                $content[0]['Url'] = "http://www.heartcare.site/index.php/about-us";
                break;
            case 3:
                $content = array();
                $content[0]['Title']="这里是您的个人中心,登录可查看往期记录!";
                $content[0]['Description']="期待为您服务,用心关注内心O(∩_∩)O";
                $content[0]['PicUrl'] = "http://www.heartcare.site/images/healthcare/user_center_user_center.jpg";
                $content[0]['Url'] = "http://www.heartcare.site/index.php/usercenter/health/health-record";
                break;
            case 4:
            case "绑定":
                $content = array();
                $content[0]['Title']="绑定微信公众号到HeartCare账号获取更多服务!";
                $content[0]['Description']="期待为您服务,用心关注内心O(∩_∩)O";
                $content[0]['PicUrl'] = "http://www.heartcare.site/images/healthcare/weixin/for_heart.jpeg";
                $content[0]['Url'] = "http://www.heartcare.site/index.php?option=com_heartcare&task=weixin.getCode";
             break;
            default:
                $content = "回复'hello'试一下";
                $content = $content."\n数字:\n1:查看主页\n2:关于我们\n3:个人中心\n4:绑定账号";
                break;
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;

    }

    /**
     * 接收图片信息
     * */
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    /**
     * 接收位置信息
     * */
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /**
     * 接收语音消息
     */
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    /**
     * 接收视频消息
     * */
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    /**
     * 接收链接消息
     * */
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /**
     * 处理取消关注,将subscribe字段设置为0
     *
     * return bool
     * */
    private function responseUnsubscribe($object)
    {
        $user = $this->getUserInfo($object);
        $user['subscribe'] = 0;//取消关注

        $model = $this->getModel();
        $result = $model->updateWxUserInfo($user);

        return $result;
    }

    /**
     * 回复事件消息中查询测量次数,测量时间
     * */
    private function clickCheckArrange($object)
    {
        //根据微信用户的openid获取heartcare用户的user_id
        $model = $this->getModel();
        $user = $model->getWxUserInfo($object->FromUserName);
        $user = $this->objToArray($user);//对象转化为数组

        if( $user['user_id'] > 0 )
        {
            //已绑定用户,查询测量次数,最早测量,最晚测量
            $user['id'] = $user['user_id'];
            $model_heart = $this->getModel('HeartCare','HeartCareModel');
            $measureInfo = $model_heart->get_measure_info($user);
            $measureInfo['user'] = 1;
            $doctorIds = $model->getDoctorChoosedByUser($user['id']);
            if($doctorIds)
            {
                //获取医生的基本信息
                $doctors = $model->userIdFindName($doctorIds);
                $measureInfo['doctors'] = $doctors;
            }else{
                $measureInfo['doctors'] = 0;//没有医生
                $measureInfo['doc_info'] = '您还没有选中医生,请登录系统进行选择.';
            }

            return $measureInfo;
        } else {
            //未绑定用户
            $str = '您还未绑定HeartCare账号,请绑定后再进行查询,回复绑定可进行账号绑定.';
            $measureInfo['count'] = $str;
            $measureInfo['last']  = $str;
            $measureInfo['first']  = $str;
            $measureInfo['doctors'] = 0;//没有医生
            $measureInfo['doc_info'] =$str;
            $measureInfo['user'] = 0;//没有绑定HeartCare

            return $measureInfo;
        }
    }

    /**
     * 对象转化为数组
     *
     * return array
     * */
    private function objToArray($object)
    {
        $arr = array();
        foreach($object as $key => $value)
        {
            $arr[$key] = $value;
        }
        return $arr;
    }

    /**
     * 数组中的数组转化为json格式字符串
     *
     * return array
     * */
    private function stepArrayToJson(array $arr)
    {
        foreach($arr as $key => $value)
        {
            if(is_array($value))
            {
                $arr[$key] = json_encode($value);
            }
        }
        return $arr;
    }

    /**
     * 回复文本消息
     * */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    /**
     * 回复图片消息
     * */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
            <MediaId><![CDATA[%s]]></MediaId>
        </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[image]]></MsgType>
            $item_str
            </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /**
     * 回复语音消息
     * */
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
            <MediaId><![CDATA[%s]]></MediaId>
        </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[voice]]></MsgType>
                $item_str
                </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /**
     * 回复视频消息
     * */
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
            <MediaId><![CDATA[%s]]></MediaId>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
        </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        $item_str
        </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /**
     * 回复音乐消息
     * */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        $item_str
        </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /**
     * 回复图文消息
     * */
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return '';
        }
        $itemTpl = "<item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <Content><![CDATA[]]></Content>
            <ArticleCount>%s</ArticleCount>
            <Articles>
            $item_str</Articles>
            </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }


    /**
     * 组装最近7次的测量结果
     *
     * return array
     * */
    public function assembleMeasureData($object)
    {
        $content = array();
        $count = 7;//设置显示的信息数
        //微信用户信息
        $wXser = $this->getUserInfo($object);

        if ( $wXser['user_id'] > 0 )
        {
            $model = $this->getModel();
            $measureData = $model->getUserMeasureData($wXser['user_id']);

            if(sizeof($measureData) < $count)
            {
                $count = sizeof($measureData);
            }

            for($i = 0 ; $i < $count ; $i++)
            {
                $mData = $this->objToArray($measureData[$i]);
                $content[$i]['Title'] = "您于".$mData['measure_time']."使用".$mData['device_id']."测量的".$mData['data_type'];
                $content[$i]['Description'] = "我们会用心呵护!";
                $content[$i]['PicUrl'] = 'http://www.heartcare.site/images/healthcare/weixin/measure.png';
                $content[$i]['Url'] = "http://www.heartcare.site/index.php?option=com_heartcare&view=heartcare&layout=modal_weixin&wave_id=".$mData['id']."&wave_type=".$mData['data_type'];
            }

        } else {
            $content[0]['Title']="小蝌蚪,您还没有进行绑定,绑定微信公众号到HeartCare账号获取更多服务!";
            $content[0]['Description']="期待为您服务,用心关注内心O(∩_∩)O";
            $content[0]['PicUrl'] = "http://www.heartcare.site/images/healthcare/weixin/for_heart.jpeg";
            $content[0]['Url'] = "http://www.heartcare.site/index.php?option=com_heartcare&task=weixin.getCode";
        }

        return $content;
    }

    /**
     * 获取微信用户基本信息,并插入到数据库中
     * return array $res
     * */
    public function getUserInfo($object)
    {
        $openid = $object->FromUserName;
        //$openid = 'oU5ClxNLeSQY7Z8C_eEDX1ykqo7A';
        //从DB中获取存储的info
        $model = $this->getModel();
        $userFromDB = $model->getWxUserInfo($openid);

        if($userFromDB)
        {
            //返回从DB中取回的userinfo
            $userFromDB = $this->objToArray($userFromDB);

            if($object->Event == 'subscribe')
            {
                //取消又关注的将subscribe字段设置为1
                $userFromDB['subscribe'] = 1;
                $userFromDB['subscribe_time'] = time();

                $model->updateWxUserInfo($userFromDB);
            }

            return $userFromDB;
        }
        else
        {
            //获取微信用户最新info
            $accessToken = $this->getWxAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid."&lang=zh_CN";
            $user = $this->httpCurl($url);
            //数组中元素有数组的,转化为json格式
            foreach($user as $key => $value)
            {
                if(is_array($value))
                {
                    $user[$key] = json_encode($value);
                }
            }

            $user['user_id'] = 0;
            //插入到数据库中,这里不带user_id
            $model->setWxUserInfo($user);
            //返回取回的user information
            return $user;
        }
    }

    /**
     * 第一次登陆绑定页面,进行账号绑定
     * */
    public function bindWxToHeartCare()
    {
        $app = JFactory::getApplication();
        $code                    = $app->input->post->get('code', '', 'string');
        $userInfoOne['username'] = $app->input->post->get('username', '', 'string');
        $userInfoOne['password'] = $app->input->post->get('password', '', 'string');
        $loginUrl = 'http://www.heartcare.site/index.php?option=com_heartcare&task=user.login';
        //检查用户名和密码是不是准确的,并登陆
        $loginResult = $this->httpCurl($loginUrl, 'post', 'json', $userInfoOne);

        //检查登陆的结果,如果登陆成功,那么确认这个用户在HeartCare拥有账号
        if ($loginResult['login'] == 'OK')
        {
            //查找用户在HeartCare上的id,并组装带userInfoOne中
            $model_heart =  $this->getModel('HeartCare','HeartCareModel');
            $userId = $model_heart->get_user_id($userInfoOne);
            $userInfoOne['user_id'] = $userId[0]->id;

            //获取微信用户的openid,网页授权access_token
            $param = $this->getWxWebAccessToken($code);

            //获取网页授权的用户信息
            $userInfoTwo = $this->getWxWebUserInfo($param);
            //数组中元素有数组的,转化为json格式
            foreach($userInfoTwo as $key => $value)
            {
                if(is_array($value))
                {
                    $userInfoTwo[$key] = json_encode($value);
                }
            }
            //合并用户的基本信息(微信信息和HeartCare信息)
            $user = array_merge($userInfoOne, $userInfoTwo);

            //将数据表中的信息更新,也就是进行绑定
            $model = $this->getModel();
            if($user['user_id'] && $user['openid'])
            {
                //获取weixin_user表中数据
                $wxUser = $model->getWxUserInfo($user['openid']);
                if($wxUser->user_id > 0)
                {
                    //已经绑定了
                    $this->setRedirect('index.php?option=com_heartcare&view=weixin&flag=4');
                }else{
                    //没有绑定,则更新数据库中对应关系
                    $flag = $model->updateWxUserInfo($user);
                    if($flag)
                    {
                        $this->setRedirect('index.php?option=com_heartcare&view=weixin&flag=1');
                    }else{
                        $this->setRedirect('index.php?option=com_heartcare&view=weixin&flag=0');
                    }
                }
            }else{
                $this->setRedirect('index.php?option=com_heartcare&view=weixin&flag=0');
            }
        }else{
            $this->setRedirect('index.php?option=com_heartcare&task=weixin.getCode&flag=2');
        }
    }

    /**
     * 获取 网页授权access_token 和 基础支持中的access_token 不同
     * return array
     *   "access_token":"ACCESS_TOKEN",
     *   "expires_in":7200,
     *   "refresh_token":"REFRESH_TOKEN",
     *   "openid":"OPENID",
     *   "scope":"SCOPE"
     *
     * */
    public function getWxWebAccessToken($code)
    {
        $appId  = $this->appId;
        $secret = $this->appSecret;
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$secret&code=$code&grant_type=authorization_code";
        $res = $this->httpCurl($url);

        return $res;
    }

    /**
     * 通过网页授权获取用户信息
     * */
    public function getWxWebUserInfo( array $param )
    {
        $accessToken = $param['access_token'];
        $openId      = $param['openid'];
        $lang        ='zh_CN';

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$openId&lang=$lang";
        $userInfo = $this->httpCurl($url);

        return $userInfo;
    }

    /**
     * 获取Code
     * (传递state参数)
     */
    public function getCode($state='1')
    {

        $app   = JFactory::getApplication();
        $flag  = $app->input->getInt('flag','2');

        $APPID=$this->appId;
        $redirect_uri = "http://www.heartcare.site/index.php?option=com_heartcare&view=weixin&flag=".$flag;

        //应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid)
        //snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
        $scope = 'snsapi_userinfo';

        $url_get_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APPID&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
        header("Location: $url_get_code");//重定向请求微信用户信息,code
    }

    /**
     * 获取微信服务器IP
     * return array ip_list
     * */
    public function getWxServerIp()
    {
        $accessToken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken;
        $res = $this->httpCurl($url,'get','json');

        return $res;
    }

    /**
     * 将access_token存session中
     * */
    public function getWxAccessToken()
    {
        $model = $this->getModel();
        $accessTmp = $model->getAccessToken();
        if($accessTmp->access_token && $accessTmp->expire_time > time())
        {
            $accessToken = $accessTmp->access_token;
            return $accessToken;
        }
        else
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
            $res = $this->httpCurl($url,'get','json');
            unset($accessToken);
            $accessToken = $res['access_token'];
            if($model->setAccessToken($accessToken))
            {
                echo $accessToken;
            }

            return $accessToken;
        }
    }

    /**
     * url  接口url     string
     * type 请求类型     string
     * res  返回数据类型  array
     * arr  post请求参数  string
     * */
    public function httpCurl($url,$type='get',$res='json',$arr='')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if($type == 'post')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }

        $output = curl_exec($ch);
        curl_close($ch);
        if($res == 'json')
        {
            if (curl_errno($ch))
            {
                return curl_error($ch);
            }
            else
            {
                return json_decode($output,true);
            }
        }
    }

    /**
     * 创建微信菜单
     * */
    public function definedItems()
    {
        header('content-type:text/html;charset=utf-8');
        $accessToken = $this->getWxAccessToken();
        $url =  "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
        $postArr = array(
            'button' => array(
                array(
                    'name' => urlencode('官网'),
                    'sub_button' => array(
                        array(
                            'name' => urlencode('主页'),
                            'type' => 'view',
                            'url' => 'http://www.heartcare.site/'
                        ),//第一个二级菜单
                        array(
                            'name' => urlencode('设备'),
                            'type' => 'view',
                            'url' => 'http://www.heartcare.site/index.php/devices-and-systems'
                        ),//第二个二级菜单
                        array(
                            'name' => urlencode('新闻'),
                            'type' => 'view',
                            'url' => 'http://www.heartcare.site/index.php/news'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>urlencode('关于'),
                            'url'=>'http://www.heartcare.site/index.php/about-us'
                        )
                    )
                ),//第一个一级菜单
                array(
                    'name' => urlencode('我的信息'),
                    'sub_button' =>array(
                        array(
                            'name' => urlencode('测量次数'),
                            'type' => 'click',
                            'key' => 'measurementTimes'
                        ),
                        array(
                            'name' => urlencode('上次时间'),
                            'type' => 'click',
                            'key' => 'lastTime'
                        ),
                        array(
                            'name' => urlencode('已选医生'),
                            'type' => 'click',
                            'key' => 'checkedDoctors'
                        )
                    )
                ),
                array(
                    "type"=>"click",
                    "name"=>urlencode("测量记录"),
                    "key"=>"readRecords"
                )//第三个一级菜单
            )
        );
        $postJson = urldecode(json_encode($postArr));
        $res = $this->httpCurl($url,'post','json',$postJson);
        var_dump($res);
    }

}