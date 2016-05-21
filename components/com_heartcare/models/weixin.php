<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/5/13
 * Time: 15:03
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareModelWeixin extends JModelList
{
    //access_token有效时间
    private $expireTime = 7000;

    /**
     * 存储access_token
     * string $accessToken
     *
     * SQL字段说明:accessToken, expire_time:过期时间, store_time:存储时间
     * */
    public function setAccessToken($accessToken)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $columns = array('access_token', 'expire_time','store_time');
        $values = array($db->quote($accessToken),$db->quote(time()+$this->expireTime), $db->quote(time()));
        $query
            ->insert($db->quoteName('#__health_weixin_access_token'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        try
        {
            if($db->execute())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            echo $e->getMessage();
            $this->setError($e->getMessage());

            return false;
        }

    }

    /**
     * 取出access_token
     * return obj $accessToken->access_token
     *                        ->expire_time
     *                        ->store_time
     * */
    public function getAccessToken()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__health_weixin_access_token'));
        $query->where(' store_time IN (select MAX(store_time) from ie_health_weixin_access_token)');
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return $result[0];
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

    }

    /**
     * 存储微信用户的相关信息
     *
     * user_id :就是用户joomla系统的id,   如果用户绑定了微信,就填写,如果不绑定那么就为空
     *
     * subscribe:关注
     * openid:用户的标识，对当前公众号唯一
     * nickname:用户的昵称
     * sex:1
     * language:zh_CN
     * city:海淀
     * province:北京
     * country:中国
     * headimgurl:头像链接
     * subscribe_time:用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
     * unionid:只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
     * remark:公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
     * groupid:用户所在的分组ID（兼容旧的用户分组接口）
     * tagid_list:[]用户被打上的标签ID列表
     * */
    public function setWxUserInfo(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $columns = array('user_id','subscribe', 'openid','nickname','sex','language','city','province','country','headimgurl','subscribe_time','unionid','remark','groupid','tagid_list');
        $values = array($db->quote($user['user_id']),$db->quote($user['subscribe']),$db->quote($user['openid']),$db->quote($user['nickname']),$db->quote($user['sex']),$db->quote($user['language']),$db->quote($user['city']),$db->quote($user['province']),$db->quote($user['country']),$db->quote($user['headimgurl']),$db->quote($user['subscribe_time']),$db->quote($user['unionid']),$db->quote($user['remark']),$db->quote($user['groupid']),$db->quote($user['tagid_list']));
        $query
            ->insert($db->quoteName('#__health_weixin_users'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        try
        {
            if($db->execute())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            echo $e->getMessage();
            $this->setError($e->getMessage());

            return false;
        }
    }


    /**
     * 更新相关用户信息
     * */
    public function updateWxUserInfo(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            'user_id     = '.$db->quote($user['user_id']) ,
            'subscribe   = '.$db->quote($user['subscribe']),
            'openid      = '.$db->quote($user['openid']),
            'nickname    = '.$db->quote($user['nickname']),
            'sex         = '.$db->quote($user['sex']),
            'language    = '.$db->quote($user['language']),
            'city        = '.$db->quote($user['city']),
            'province    = '.$db->quote($user['province']),
            'country     = '.$db->quote($user['country']),
            'headimgurl  = '.$db->quote($user['headimgurl']),
            'privilege  =  '.$db->quote($user['privilege']),
            'subscribe_time = '.$db->quote($user['subscribe_time']),
            'unionid     = '.$db->quote($user['unionid']),
            'remark      = '.$db->quote($user['remark']),
            'groupid     = '.$db->quote($user['groupid']),
            'tagid_list  = '.$db->quote($user['tagid_list'])
        );
        $conditions = array('openid = '.$db->quote($user['openid']),);
        $query->update($db->quoteName('#__health_weixin_users'))->set($fields)->where($conditions);

        $db->setQuery($query);

        try
        {
            if($db->execute())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            echo $e->getMessage();
            $this->setError($e->getMessage());

            return false;
        }

    }

    /**
     * 获取微信用户列表
     * return array $result
     * */
    public function getWxUsers()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__health_weixin_users'));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return $result;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * 获取单个微信用户信息
     * input $openId 微信用户的标识
     * return array
     * */
    public function getWxUserInfo($openId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__health_weixin_users'));
        $query->where( 'openid = '.$db->quote($openId));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return $result[0];
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * 获取用户所选择的医生
     * */
    public function getDoctorChoosedByUser($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cb_doctors')->from($db->quoteName('#__comprofiler'));
        $query->where('id = '.(int) $userId);

        $db->setQuery($query);
        try
        {
            $temp = $db->loadObjectList();
            $result = json_decode($temp[0]->cb_doctors)->doctors;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;
    }

    /**
     * 根据一堆id查询用户name,username,email
     * */
    public function userIdFindName(array $userId)
    {
        $idString = implode(',',$userId);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('name,email')->from($db->quoteName('#__users'));
        $query->where('id in ('.$idString.')');

        $db->setQuery($query);
        try
        {
            $result = $db->loadObjectList();
            return $result;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }


    /**
     * 根据用户id查出他的测量记录
     * return array
     * */
    public function getUserMeasureData($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id,device_id,data_type,measure_time,data_route')->from($db->quoteName('#__health_data'))->where($db->quoteName('user_id') . ' = ' . $userId. ' ORDER BY ' .$db->quoteName('measure_time').' DESC');
        $db->setQuery($query);

        try {
            $result = $db->loadObjectList();

            return $result;
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }
    }
}