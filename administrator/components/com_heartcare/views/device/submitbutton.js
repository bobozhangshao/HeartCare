/**
 * Created by zhangshaobo on 15/12/22.
 *
 * 提交按钮 检查是否符合表单的标准
 */

Joomla.submitbutton = function(task)
{
    if (task == '')
    {
        return false;
    }
    else
    {
        var isValid = true;
        var action = task.split('.');
        if (action[1] != 'cancel' && action[1] != 'close')
        {
            var forms = $$('form.form-validate');
            for (var i=0; i<forms.length; i++)
            {
                if (!document.formvalidator.isValid(forms[i]))
                {
                    isValid = false;
                    break;
                }
            }
        }

        if (isValid)
        {
            Joomla.submitform(task);
            return true;
        }
        else
        {
            alert(Joomla.JText._('COM_HEARTCARE_DEVICE_ID_OR_TYPE_ERROR_UNACCPTABLE','some values are unacceptable'));
            return false;
        }
    }
}
