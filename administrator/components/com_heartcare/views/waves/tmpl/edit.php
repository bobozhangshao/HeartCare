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