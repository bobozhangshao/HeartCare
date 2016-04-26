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
        $result = array();

        $file = '../media/com_heartcare/data/'.$this->getItem()->data_route;
        $content = file_get_contents($file);
        $content = preg_replace('/(\r*)\n/',"\n",$content);//将content字符串中的\n\r替换成\n
        $yname = $this->getItem()->data_type;

        $arr = explode("\n", $content);

        if(end($arr) == '')
        {
            array_pop($arr);
        }

        $len = sizeof($arr);
        //缩放的倍数150000是频率2500*时间(6秒)*100,得到百分比
        $zoom = floatval(150000/$len);
        if($zoom > 100)
        {
            $zoom = 100;
        }

        $xarr = array();

        if(($yname == 'ECG')||($yname == 'ICG')||($yname == 'deltaZ')||($yname == 'Z0'))
        {
            //xarr 是x轴坐标
            for ($i=0 ; $i<$len ; $i++ ){
                //$xarr[$i] = number_format($i*(1/360),1).'s';

                //除以50是指0.2秒的数据量,频率为250
                if ($i%50 == 0){
                    $k = $i/50;
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

        }
        //ACC数据
        elseif(($yname == 'ACC')||($yname == 'GRRO'))
        {
            $arr_x = array();
            $arr_y = array();
            $arr_z = array();
            foreach($arr as $key=>$value)
            {
                $arr_xyz = explode(' ', $value);
                $arr_x[$key] = (float)$arr_xyz[0];
                $arr_y[$key] = (float)$arr_xyz[1];
                $arr_z[$key] = (float)$arr_xyz[2];

                $arr_x[$key] = round($arr_x[$key],3);
                $arr_y[$key] = round($arr_y[$key],3);
                $arr_z[$key] = round($arr_z[$key],3);

            }

            for ($i=0 ; $i<$len ; $i++ ){
                //$xarr[$i] = number_format($i*(1/360),1).'s';

                //除以20是指1秒的数据量,频率为20hz
                if ($i%20 == 0){
                    $k = $i/20;
                    $xarr[$i] = $k;
                }else{
                    $xarr[$i] = '';
                }
            }


            $result = array(
                'yname' => $yname,
                'len' => $len,
                'zom' => $zoom,
                'xa' => $xarr,
                'data_x' => $arr_x,
                'data_y' => $arr_y,
                'data_z' => $arr_z
            );

        }
        //HR,RR,BP数据
        elseif(($yname == 'HR')||($yname == 'RR')||($yname='BP'))
        {
            $zoom = 2000/$len;
            //unset($xarr);
            for ($i=0 ; $i<$len ; $i++ ){
                //$xarr[$i] = number_format($i*(1/360),1).'s';
                $xarr[$i] = $i;
            }

            $result = array(
                'yname' => $yname,
                'len' => $len,
                'zom' => $zoom,
                'xa' => $xarr,
                'data' => $arr
            );

        }

        $json_result = json_encode($result);

        return $json_result;
    }
}