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

}