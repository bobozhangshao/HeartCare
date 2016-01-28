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
    axisData = obj.data;
    var woption = {
                    backgroundColor:\'rgba(200,250,200,0.7)\',
                    legend: {
                        show:false,
                        data:[yname]
                    },
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
                        zoomLock:true,
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
                            name : "时\n间\n(s)" ,
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
                            max : 1300,
                            min : 800,
                            scale : true,
                            splitNumber : 10
                        }
                    ],
                    series : [
                        {
                            name:\'PAC\',
                            type:\'line\',
                            smooth:true,
                            symbol:\'none\',
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        color:\'rgba(255,60,50,1)\',
                                        width: 0.8
                                    }
                                }
                            },

                            data : axisData
                        }
                    ]
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