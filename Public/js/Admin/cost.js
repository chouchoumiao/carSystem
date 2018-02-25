$(function(){

    /**
     *  编辑是时候如果是固定内容的话则显示为下拉框，并且显示正确的值
     */
    if( $('#cost_fixed_name') ){
        var fixName = $("#fixName").val();
        $("#cost_fixed_name").val(fixName);
    }

    /**
     * 自动验证
     */
    if($('#updateForm').length > 0) {
        //文本框失去焦点后
        $('form :input').blur(function(){
            var $parent = $(this).parent();
            $parent.find(".formtips").remove();

            //日期不能为空
            if( $(this).is('#cost_date') ){
                var idname = 'cost_date';
                if( this.value == '' ){
                    var errorMsg = '日期不能为空';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //验证车牌
            if( $(this).is('#car_no') ){
                var idname = 'car_no';
                if( (this.value.length > 10) || (this.value.length < 7) || (this.value == '') ){
                    var errorMsg = '车牌位数不能为空并且只能在7到10位之间';
                    doError($parent,errorMsg,idname);
                }else{

                    //getDriverInfo(this.value);  去除自动显示驾驶员姓名，因为车与驾驶员不绑定
                    doOK($parent,idname);
                }
            }

            //驾驶员验证
            if( $(this).is('#car_driver') ){
                var idname = 'car_driver';
                if( (this.value.length > 5) || (this.value.length < 2) || (this.value == '') ){
                    var errorMsg = '驾驶员姓名不能为空，并且位数只能是2到5位';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //报销内容验证
            if( $(this).is('#cost_name') ){
                var idname = 'cost_name';
                if( (this.value.length > 100) || (this.value == '') ){
                    var errorMsg = '报销内容不能为空，并且位数不能超过100位';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //金额验证
            if( $(this).is('#cost_amount') ){
                var idname = 'cost_amount';
                if( (!isDecimal(this.value)) || (this.value == '') ){
                    var errorMsg = '金额不能空，并且必须是数字';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //备注内容验证
            if( $(this).is('#cost_note') ){
                var idname = 'cost_note';
                if( this.value.length > 200 ){
                    var errorMsg = '备注内容长度不能超过200个字符';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }


        }).keyup(function(){
            $(this).triggerHandler("blur");
        }).focus(function(){
            $(this).triggerHandler("blur");
        });//end blur

    }
});

//通过Ajax获取车牌对应的驾驶员信息并填入  去除自动显示驾驶员姓名，因为车与驾驶员不绑定
// function getDriverInfo(carNo){
//
//     $.ajax({
//         url:ROOT+"/Admin/Car/doAction/action/getDriver"//改为你的动态页
//         ,type:"POST"
//         ,data:{
//             'carNo':carNo
//         }
//         ,dataType: "json"
//         ,success:function(json){
//             if(json.success == 1){
//                 $('#car_driver').val(json.msg);
//
//             }
//         }
//         ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
//     });
// }

//删除指定费用信息
function delCostInfo(id){

    //弹出确认框
    if (!confirm('确定要删除吗？')){
        return false;
    }

    $.ajax({
    url:ROOT+"/Admin/Cost/doAction/action/del"//改为你的动态页
    ,type:"POST"
    ,data:{
            'id':id
        }
    ,dataType: "json"
    ,success:function(json){
        if(json.success == 1){
            alert('删除成功');
            location = ROOT+"/Admin/Cost/doAction/action/all";

        }else if(json.success == 0){
            alert('删除失败');
            return false;
        }else {
            alert('未取到对应ID，或者其他错误');
            return false;
        }
    }
    ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}

/**
 * 显示验证失败时候应该要显示的样式
 * @param $parent
 * @param msg
 */
function doError($parent,msg,idname) {

    //判断原来是否已经有校验成功过,有的话则删除原来的样式
    if($('#showOK'+idname).length > 0 ){
        $('#showOK'+idname).remove();
        $parent.removeClass('has-success has-feedback');
    }

    //追加失败的样式
    $parent.addClass('has-error has-feedback');

    //如果已经增加过失败样式,则不追加,避免多次追加
    if($('#showError'+idname).length <= 0 ){
        $parent.append('<span id="showError'+idname+'" class="glyphicon glyphicon-remove form-control-feedback"></span>');
        $parent.append('<p id="msg'+idname+'"  class="showMsg text-danger">'+msg+'</p>');
    }

    $('#add').attr('disabled',"true");

}

/**
 * 显示验证成功时候应该要显示的样式
 * @param $parent
 */
function doOK($parent,idname) {
    //判断原来是否已经有校验失败过,有的话则删除原来的样式
    if($('#showError'+idname).length > 0){

        $('#showError'+idname).remove();
        $('#msg'+idname).remove();
        $parent.removeClass('has-error has-feedback');
    }

    //追加成功的样式
    $parent.addClass('has-success has-feedback');

    //如果已经增加过成功样式,则不追加,避免多次追加
    if($('#showOK'+idname).length <= 0){
        $parent.append('<span id="showOK'+idname+'" class="glyphicon glyphicon-ok form-control-feedback""></span>');
    }

    $('#add').removeAttr("disabled");
}

function showItem(item) {

    $('#line').show();
    $('#'+item).show();

}

//清空输入框
function delItem(divName) {
    $('#'+divName).val("");
    $('#div_'+divName).css('display','none');

}