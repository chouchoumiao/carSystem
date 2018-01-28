$(function(){

    /**
     * 自动验证
     */
    if($('#updateForm').length > 0) {
        //文本框失去焦点后
        $('form :input').blur(function(){
            var $parent = $(this).parent();
            $parent.find(".formtips").remove();

            //日期不能为空
            if( $(this).is('#car_date') ){
                var idname = 'car_date';
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

                    getDriverInfo(this.value);
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

            //货物名称验证
            if( $(this).is('#goods_name') ){
                var idname = 'goods_name';
                if( (this.value.length > 100) || (this.value == '') ){
                    var errorMsg = '货物名称不能为空，并且位数不能超过100位';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //装货地名验证
            if( $(this).is('#loading_place') ){
                var idname = 'loading_place';
                if( (this.value.length > 100) || (this.value == '') ){
                    var errorMsg = '装货地名称不能为空，并且位数不能超过100位';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //卸货地名称验证
            if( $(this).is('#unloading_place') ){
                var idname = 'unloading_place';
                if( (this.value.length > 100) || (this.value == '') ){
                    var errorMsg = '卸货地名称不能为空，并且位数不能超过100位';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //发货吨位验证
            if( $(this).is('#loading_tonnage') ){
                var idname = 'loading_tonnage';
                if( (!isDecimal(this.value)) || (this.value == '') ){
                    var errorMsg = '发货吨位不能空，并且必须是数字';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //收货吨位验证
            if( $(this).is('#unloading_tonnage') ){
                var idname = 'unloading_tonnage';
                if( (!isDecimal(this.value)) || (this.value == '') ){
                    var errorMsg = '收货吨位不能空，并且必须是数字';
                    doError($parent,errorMsg,idname);
                }else{
                    doOK($parent,idname);
                }
            }

            //票号验证
            if( $(this).is('#ticket_number') ){
                var idname = 'ticket_number';
                if( this.value.length != 0){
                    //不为空再验证
                    if( (this.value.length > 8) || (!isNumber(this.value))){
                        var errorMsg = '票号必须是纯数字，并且位数小于8位';
                        doError($parent,errorMsg,idname);
                    }else{
                        doOK($parent,idname);
                    }
                }else {
                    doOK($parent,idname);
                }
            }

            //金额验证
            if( $(this).is('#amount') ){
                var idname = 'amount';
                if( (!isDecimal(this.value)) || (this.value == '') ){
                    var errorMsg = '金额不能空，并且必须是数字';
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

//删除指定车辆信息
function getDriverInfo(carNo){

    $.ajax({
        url:ROOT+"/Admin/Car/doAction/action/getDriver"//改为你的动态页
        ,type:"POST"
        ,data:{
            'carNo':carNo
        }
        ,dataType: "json"
        ,success:function(json){
            if(json.success == 1){
                $('#car_driver').val(json.msg);

            }
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}

//删除指定车辆信息
function delFreightInfo(id){

    //弹出确认框
    if (!confirm('确定要删除吗？')){
        return false;
    }

    $.ajax({
    url:ROOT+"/Admin/Freight/doAction/action/del"//改为你的动态页
    ,type:"POST"
    ,data:{
            'id':id
        }
    ,dataType: "json"
    ,success:function(json){
        if(json.success == 1){
            alert('删除成功');
            location = ROOT+"/Admin/Freight/doAction/action/all";

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

    $('#edit').attr('disabled',"true");

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

    $('#edit').removeAttr("disabled");
}

function showItem(item) {

    $('#line').show();
    $('#'+item).show();

}