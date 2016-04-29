<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/21
 * Time: 11:12
 */
defined('_JEXEC') or die('Restricted Acceess');

class HeartCareControllerHeartCare extends JControllerForm
{
    /**
     * 内部删除
     * 按id查找
     * */
    public function remove_measure()
    {
        $app = JFactory::getApplication();
        $measure['id']         = $app->input->post->get('data_id','','string');
        $measure['data_route'] = $app->input->post->get('data_route','','string');

        $model = $this->getModel('HeartCare','HeartCareModel');

        if($model->remove_measure($measure))
        {
            $this->setRedirect('index.php/usercenter/health/health-record');
        }
        else
        {
            $this->setRedirect('index.php/usercenter/health/health-record');
        }
    }

    /**
     * Android 删除接口
     * 按文件名查找
     * */
    public function remove_interface()
    {
        $app = JFactory::getApplication();
        $filename = $app->input->post->get('filename','','string');
        //$filename = $app->input->getString('filename');

        $model = $this->getModel('HeartCare','HeartCareModel');
        if($filename != '')
        {
            $response['filename'] = $filename;

            if($model->remove_interface($filename,$response))
            {
                $response['remove_record'] = 'OK';
                if($this->remove_file($filename))
                {
                    $response['remove_file'] = 'OK';
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
                else
                {
                    $response['remove_file'] = 'FALSE';
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
            }
            else
            {
                $response['remove_record'] = 'FALSE';
                echo json_encode($response);
                JFactory::getApplication()->close();
            }


        }
        else
        {
            $response['filename'] = 'NULL';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
    }

    /**
     * 文件夹中的测量数据文件
     *
     * */
    public function remove_file(&$filename)
    {
        $measure_file = JPATH_BASE.'/media/com_heartcare/data/'.$filename;

        try
        {
            if(unlink($measure_file))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(RuntimeException $e)
        {
            $this->setError($e->getMessage());
        }
    }


    /**
     * 返回一个用户已经上传过的文件
     * */
    public function user_files()
    {
        $app = JFactory::getApplication();
        $user['username'] = $app->input->post->get('username','','string');
        //$user['useremail']         = $app->input->get('user_email','','string');
        $filelist = array();
        $filelist['have_user'] = 'EXIST';

        $model = $this->getModel('HeartCare','HeartCareModel');

        if(!$model->check_username($user))
        {
            $filelist['have_user'] = 'NOT EXIST';
            echo json_encode($filelist);
            JFactory::getApplication()->close();
        }

        if($user['username'] != '')
        {
            $user['id'] = $model->get_user_id($user);
            if($user['id'] != '')
            {
                $user['id'] = $user['id'][0]->id;
                $filelist['data'] = $model->get_user_files($user);
                if($filelist['data'])
                {
                    echo json_encode($filelist);
                    JFactory::getApplication()->close();
                }
                else
                {
                    echo json_encode($filelist);
                    JFactory::getApplication()->close();
                }
            }
            else
            {
                echo json_encode($filelist);
                JFactory::getApplication()->close();
            }
        }
        else
        {
            echo json_encode($filelist);
            JFactory::getApplication()->close();
        }

    }

    /**
     * 获取用户上传总数,最早和最晚时间
     * return count last first
     * */
    public function get_measure_info()
    {
        $app = JFactory::getApplication();
        $user['username'] = $app->input->post->get('username','','string');
        $model = $this->getModel('HeartCare','HeartCareModel');

        $response = array();
        if($model->check_username($user))
        {
            $response['have_user'] = 'EXIST';
            $user['id'] = $model->get_user_id($user);
            $user['id'] = $user['id'][0]->id;

            $response['info'] = $model->get_measure_info($user);

            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        else
        {
            $response['have_user'] = 'NOT EXIST';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }

        JFactory::getApplication()->close();
    }

    /**
     * 下载测量数据文件的接口
     * return file
     * */
    public function download_file()
    {
        $app = JFactory::getApplication();
        $user['username'] = $app->input->post->get('username','','string');
        $user['filename'] = $app->input->post->get('filename','','string');

        $file_route = JPATH_BASE.'/media/com_heartcare/data/';
        $model = $this->getModel('HeartCare','HeartCareModel');
        $response = array();
        $files    = array();

        if($model->check_username($user))
        {
            $response['have_user'] = 'EXIST';
            $user['id'] = $model->get_user_id($user);
            $user['id'] = $user['id'][0]->id;

            if($model->user_state($user))
            {
                $response['online'] = 'OK';
                $files = $model->get_user_files($user);
            }
            else
            {
                $response['online'] = 'NO';
                echo json_encode($response);
                JFactory::getApplication()->close();
            }
        }
        else
        {
            $response['have_user'] = 'NOT EXIST';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }

        if(empty($files))
        {
            $response['files'] = 'NOT EXIST';
        }
        else
        {
            if(in_array($user['filename'],$files))
            {
                $response['this_record'] = 'EXIST';
                $file = $file_route.$user['filename'];

                if(file_exists($file))
                {
                    $response['this_file'] = 'EXIST';

                    $fp = fopen($file,"r");
                    $file_size = filesize($file);

                    //下载文件需要用到的头
                    header("Content-type: application/octet-stream");
                    Header("Accept-Ranges: bytes");
                    Header("Accept-Length:".$file_size);
                    Header("Content-Disposition: attachment; filename=".$user['filename']);
                    $buffer = 4096;
                    $file_count=0;
                    //向浏览器返回数据
                    while(!feof($fp) && $file_count<$file_size){
                        $file_con=fread($fp,$buffer);
                        $file_count+=$buffer;
                        echo $file_con;
                    }
                    fclose($fp);
                }
                else
                {
                    $response['this_file'] = 'NOT EXIST';
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
            }
            else
            {
                $response['this_record'] = 'NOT EXIST';
                $response['this_file'] = 'NOT EXIST';
                echo json_encode($response);
                JFactory::getApplication()->close();
            }
        }

        JFactory::getApplication()->close();
    }

}