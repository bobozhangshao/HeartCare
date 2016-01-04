<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/25
 * Time: 16:02
 */

defined('_JEXEC') or die('Restricted Access');

class HeartCareModelWaves extends JModelAdmin
{
//    protected $measureDataId;

    public function getTable($type = 'Waves', $prefix = 'HeartCareTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            'com_heartcare.waves',
            'waves',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState(
            'com_heartcare.waves.edit.data',
            array()
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    //获取txt数据
    public function getTxtData()
    {
        $file = '../media/com_heartcare/data/'.$this->getItem()->data_route;
        $content = file_get_contents($file);

        $yname = $this->getItem()->data_type;
        $arr = explode("\r\n", $content);
        $len = sizeof($arr);
        $zoom = floatval(288000/$len);

        $xarr = array();

        for ($i=0 ; $i<$len ; $i++ ){
            //$xarr[$i] = number_format($i*(1/360),1).'s';

            if ($i%72 == 0){
                $k = $i/72;
                $xarr[$i] = $k*(0.2);
            }else{
                $xarr[$i] = '';
            }
        }

        $result = array(
            'yname' => $yname,
            'len' => $len,
            'zom' => $zoom,
            'xa' => $xarr,
            'data' => $arr
        );

        $json_result = json_encode($result);

        return $json_result;
    }
}