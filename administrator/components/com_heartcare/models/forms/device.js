/**
 * Created by zhangshaobo on 15/12/22.
 *
 * 检查device中两个表单是否合适
 */
jQuery(function(){
    document.formvalidator.setHandler('deviceid',
        function (value)
        {
            regex = /^[\S]+$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('devicetype',
        function (value)
        {
            regex = /^[\S]+$/;
            return regex.test(value);
        });
});