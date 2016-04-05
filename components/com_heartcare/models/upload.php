<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/3/21
 * Time: 09:08
 */

defined('_JEXEC') or die('Restricted Access');


class HeartCareModelUpload extends JModelList
{
    /**
     * method to move the file to the folder
     * */
    public function to_folder(array $data)
    {
        try{
            // put the file where we'd like it
            $upfile = JPATH_BASE.'/media/com_heartcare/data/'.$data['file']['name'];

            if (is_uploaded_file($data['file']['tmp_name']))
            {
                if (!move_uploaded_file($data['file']['tmp_name'], $upfile))
                {
                    //echo 'Problem: Could not move file to destination directory';
                    return false;
                }
                else
                {
                    return true;
                }
            }
            else
            {
                //echo 'Problem: Possible file upload attack. Filename: ';
                //echo $data['file']['name'];

                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        //echo "This is Upload.to_folder";
    }


    /**
     * method to check if the user in this DB
     *
     * check email, username. if they all equils each other, then it is the same user;
     */
    public function check_user(array $data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id,name,username,email')->from($db->quoteName('#__users'));
        $query->where('username = '.$db->quote($data['user']['username']).' AND email = '.$db->quote($data['user']['email']));
        $db->setQuery($query);

        //echo $query;
        try
        {
            $result = $db->loadObjectList();

//            echo "<pre>";
//            print_r($result);
//            echo "</pre>";

            if($result){
                return true;
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
     * method to insert a new record
     *
     * 如果有此设备,则直接插入设备;如果没有此设备,那么先更新设备后再插入数据.
     * */
    public function insert_measure(array $data)
    {
        //print_r($data);
        //echo "This is Upload.insert_measure";
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $columns = array('device_id', 'user_id','data_type', 'measure_time', 'data_route');
        $values = array($db->quote($data['data']['deviceid']),$db->quote($data['user']['id']), $db->quote($data['data']['datatype']), $db->quote($data['data']['time']),  $db->quote($data['file']['name']));
        $query
            ->insert($db->quoteName('#__health_data'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);
        
        try
        {
            if($db->execute())
            {
                //查找设备表里是否有这个设备.有则返回true没有则新建设备
                $query->clear()
                    ->select($db->quoteName(array('device_id', 'user_id', 'device_type')))
                    ->from($db->quoteName('#__health_device'))
                    ->where($db->quoteName('device_id') . ' = ' . $db->quote($data['data']['deviceid']));

                $db->setQuery($query);

                try
                {
                    $device = $db->loadObjectList();
                    if($device)
                    {
                        return true;
                    }
                    else
                    {
                        $query->clear()
                            ->insert($db->quoteName('#__health_device'))
                            ->columns($db->quoteName(array('device_id','user_id','device_type','register_time')))
                            ->values(implode(',',array($db->quote($data['data']['deviceid']),$db->quote($data['user']['id']),$db->quote($data['data']['devicetype']),$db->quote(date('Y-m-d H:i:s')))));
                        $db->setQuery($query);

                        try
                        {
                            if($db->execute())
                            {
                                return true;
                            }
                        }
                        catch(RuntimeException $e)
                        {
                            $this->setError($e->getMessage());

                            return false;
                        }

                    }
                }
                catch (RuntimeException $e)
                {
                    $this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

                    return false;
                }
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
     * 启用,激活用户
     * */
    public function start_user(array $data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('block').' = \'0\'',
            $db->quoteName('activation').' = \'\''
        );
        $conditions = array($db->quoteName('username').' = '.$db->quote($data['user']['username']));

        $query->update($db->quoteName('#__users'))->set($fields)->where($conditions);
        $db->setQuery($query);

        try
        {
            $result = $db->execute();

            if($result){
                return true;
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

}