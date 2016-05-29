<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/19
 * Time: 15:33
 */
defined('_JEXEC') or die;
JHtml::_('jquery.framework');

//引入百度echarts库
JHtml::script('media/com_heartcare/js/echarts.common.min.js',true);
$data = json_decode($this->txtData);

$xAxisname = 'name : "时\n间\n(s)" ,';
$tooltip = '';
$xAxisLabel = "axisLabel: {
                           interval:'auto',
                           rotate:-40,
                           textStyle:{
                                fontSize:10
                               }
                            },";
$axisData = 'axisData = obj.data;';
$zoomLock = 'zoomLock:false,';
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

    $xAxisLabel = "axisLabel: {
                           interval:'auto',
                           rotate:-40,
                           textStyle:{
                                fontSize:10
                               }
                            },";
    $zoomLock = 'zoomLock:false,';
    $tooltip = "tooltip:{show:true,
                         trigger: 'axis'
                         },";
    $series = "series : [
                        {
                            name:yname,
                            type:'line',
                            smooth:false,
                            symbol:'triangle',
                            symbolSize : 4,
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

    $zoomLock = 'zoomLock:false,';

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

?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery(".navbar").remove();
            jQuery(".logo").remove();
            jQuery("header").remove();
            jQuery("link").remove();

            jQuery("#rt-copyright").remove();
            jQuery("footer").remove();
            jQuery("div#waveshowfront").attr("z-index",999);
            jQuery("#show").click(function(){
                jQuery("#showdoctorsay").toggle(500);
            });
        });
    </script>
<div id="all">
    <div id="waveshowfront" style="height: 350px;"></div>

    <div id="doctorsay" style="border: 1px solid aquamarine;  background-color:aliceblue; padding: 1px;">
        <input type="button" id="show" name="show" value="听听医生怎么讲" />
        <div id="showdoctorsay" style="display: none"><?php echo "<h3>Doctor Say:</h3><br>".$this->doctorSay[0]->diagnosis;?></div>
    </div>
</div>


    <script type="text/javascript">
        var zoomLock = false;
        var obj = eval(<?php echo $this->txtData;?>);
        var yname = obj.yname;
        var dataLen = obj.len;
        var dataZom =  obj.zom;
        var xarrr = obj.xa;
        <?php echo $axisData;?>

        if (yname == 'HR' || yname == 'RR' || yname == 'BP')
        {

        }
        else if (yname == 'ECG' || yname == 'ICG' || yname == 'deltaZ')
        {
            zoomLock = true;
        }
        else if (yname == 'ACC' || yname == 'GRRO')
        {
            zoomLock = true;
        }

        option = {
            //backgroundColor:'rgba(200,250,200,0.7)',
            animation:false,
            <?php echo $legend.$tooltip;?>
            title: {
                left: 'left',
                text: yname
            },
            toolbox: {
                show: true,
                right:'10%',
                feature: {
                    dataView: {show: false, readOnly: false},
                    magicType: {show: true, type: ['line', 'bar']},
                    restore: {show: true},
                    saveAsImage: {show: true}
                }
            },
            xAxis: {
                type: 'category',
                name : "时\n间\n(s)" ,
                nameGap:1,
                boundaryGap: false,
                <?php echo $xAxisLabel;?>
                nameLocation:'end',
                axisTick:{
                    show:true,
                    interval:250
                },
                splitLine:{
                    show:true,
                    lineStyle:{
                        width:1
                    }
                },
                data: xarrr
            },
            yAxis: {
                type: 'value',
                scale:true,
                boundaryGap: [0, 0]
            },
            dataZoom: [{
                type: 'inside',
                start: 0,
                end: dataZom
            }, {
                start: 0,
                end: dataZom,
                zoomLock:zoomLock

            }],

            grid:{
                width:'90%',

                left:40,
                top:50,
                right:'6%',
                bottom:80
            },
            <?php echo $series;?>
        };

        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('waveshowfront'));

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>

<script>


</script>
<?php echo JHtml::_('form.token'); ?>