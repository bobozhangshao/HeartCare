<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/16
 * Time: 17:10
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');
//引入百度echarts库
JHtml::script('http://echarts.baidu.com/build/dist/echarts.js',true);
$data = json_decode($this->txtData);
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

jQuery(document).ready(function() {
		jQuery(".navbar").remove();
	    jQuery(".logo").remove();
	    jQuery("header").remove();
	    jQuery("#container-collapse").remove();
	    jQuery("a").remove();

	    jQuery("div.container-fluid").css({"padding-left" : "0px", "padding-right" : "0px"});
        jQuery("div.subhead-collapse").css({ "margin-bottom" : "0px","height":"0px"});
	    jQuery("div.subhead").css({"background" : "none", "border-bottom" : "none","margin-bottom":"0px","min-height":"0px"});
        jQuery("div.control-group").css({"padding-left" : "40px", "padding-right" : "40px"});

	    if ("'.$this->item->data_route.'"==""){
            jQuery(".span12").hide();
          	window.parent.jQuery("#measureData' . $this->item->id . 'Modal").modal(\'hide\');
          	alert("No data!");

	    }

	});

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
                            var waveChart = ec.init(document.getElementById(\'waveshow\'));
                            waveChart.setOption(woption);
                        }
                );
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_heartcare&view=waves&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm">
    <div id="waveshow" style="height: 350px; border: 1px solid #ccc; padding: 1px;"></div>
    <br>
    <div>
        <?php foreach ($this->form->getFieldset() as $field): ?>
            <div class="control-group">
                <div class="control-label"><label><?php echo $field->label; ?></label></div>
                <div class="controls"><?php echo $field->input; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <input type="hidden" name="task" value="wave.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>