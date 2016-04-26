<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/19
 * Time: 15:33
 */
defined('_JEXEC') or die;

JHtml::_('behavior.core');
//引入百度echarts库
JHtml::script('http://echarts.baidu.com/build/dist/echarts.js',true);
$data = json_decode($this->txtData);
$xAxisname = 'name : "时\n间\n(s)" ,';
$tooltip = '';
$axisData = 'axisData = obj.data;';
$zoomLock = 'zoomLock:true,';
$legend = "legend: {
                    show:true,
                    data:[yname]
                    },";
$series = "series : [
                        {
                            name:yname,
                            type:'line',
                            smooth:true,
                            symbol:'none',
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        color:'rgba(255,60,50,1)',
                                        width: 0.8
                                    }
                                }
                            },

                            data : axisData
                        }
                    ]";

if(($data->yname == 'HR') || ($data->yname == 'RR')||($data->yname == 'BP'))
{
    if ($data->yname == 'BP')
    {
        $xAxisname = 'name : "时\n间" ,';
    }
    $zoomLock = 'zoomLock:false,';
    $tooltip = "tooltip:{show:true,
                         trigger: 'axis'
                         },";
    $series = "series : [
                        {
                            name:yname,
                            type:'line',
                            smooth:false,
                            symbol:'heart',
                            symbolSize : 3,
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        color:'rgba(255,60,50,1)',
                                        width: 1
                                    }
                                }
                            },

                            data : axisData

                        }
                    ]";
}
elseif(($data->yname == 'ACC')||($data->yname == 'GRRO'))
{
    $data_type = 'ACC';
    if ($data->yname == 'GRRO')
    {
        $data_type = 'GRRO';
    }

    $axisData = "axisData_x = obj.data_x;
                 axisData_y = obj.data_y;
                 axisData_z = obj.data_z;";
    $legend = "legend:{
                       show:true,
                       data:['".$data_type."_X','".$data_type."_Y','".$data_type."_Z']
    },";
    $series = "series:[
                        {
                            name:'".$data_type."_X',
                            type:'line',
                            smooth:true,
                            symbol:'none',
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        width: 0.8
                                    }
                                }
                            },
                            data : axisData_x
                        },
                        {
                            name:'".$data_type."_Y',
                            type:'line',
                            smooth:true,
                            symbol:'none',
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        width: 0.8
                                    }
                                }
                            },

                            data : axisData_y
                        },
                        {
                            name:'".$data_type."_Z',
                            type:'line',
                            smooth:true,
                            symbol:'none',
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        width: 0.8
                                    }
                                }
                            },

                            data : axisData_z
                        }

                    ]";

}

JFactory::getDocument()->addScriptDeclaration('

    var yname;
    var dataLen;
    var dataZom;
    var xarrr = [];
    var axisData = [];
    var obj = eval(' .$this->txtData. ');
    yname = obj.yname;
    dataLen = obj.len;
    dataZom =  obj.zom;
    xarrr = obj.xa;
    '.$axisData.'
    var woption = {
                    backgroundColor:\'rgba(200,250,200,0.7)\',
                    '.$legend.$tooltip.'
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: false, readOnly: false},
                            magicType : {show: true, type: [\'line\']},
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    dataZoom:{
                        show:true,
                        realtime:true,
                        '.$zoomLock.'
                        handleSize:20,
                        showDetail:true,
                        y:320,
                        height:20,
                        start:0,
                        end:dataZom
                    },

                    grid:{
                        x:40,
                        y:50,
                        x2:18,
                        y2:80
                    },

                    xAxis : [

                        {
                            '.$xAxisname.'
                            type : \'category\',
                            boundaryGap : false,
                            axisTick:{
                                show:true,
                                interval:71
                            },

                            axisLabel: {
                                interval:0,
                                rotate:-30,
                                textStyle:{
                                    fontSize:10
                                }
                            },

                            splitLine:{
                              show:true,
                                onGap:true,
                                lineStyle:{
                                    width:0.2
                                }
                            },
                            data : xarrr
                        }
                    ],
                    yAxis : [
                        {
                            type : \'value\',
                            name : yname,
                            boundaryGap: [0, 0],
                            scale : true,
                        }
                    ],
                    '.$series.'
                };
    require.config({
                    paths: {
                        echarts: \'http://echarts.baidu.com/build/dist\'
                    }
                });
    require(
                        [
                            \'echarts\',
                            \'echarts/chart/line\',
                            \'echarts/chart/bar\'
                        ],

                        function (ec) {
                            var waveChart = ec.init(document.getElementById(\'waveshowfront\'));
                            waveChart.setOption(woption);
                        }
                );
');
?>

    <div id="waveshowfront" style="height: 350px; border: 1px solid #ccc; padding: 1px;"></div>
    <div id="doctorsay" style="border: 5px solid aquamarine; background-color:aliceblue; padding: 1px;"><?php echo "<h1>Doctor Say:</h1><br>".$this->doctorSay[0]->diagnosis;?></div>

<?php echo JHtml::_('form.token'); ?>