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

        $('form :input').keyup(function(){

            if( $(this).is('#car_no')){
                if(this.value.length > 3 ){
                    getCarNoInfo(this.value);
                }
            }


            if( $(this).is('#car_driver')){
                if(this.value.length > 0 ){
                    getDriverNoInfo(this.value);
                }
            }

        });

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

                    //getCarNoInfo(this.value);  自动现实匹配的车牌

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


        $('.addCost').click(function () {
            $('.firstCost').append('<div class="form-group"><label class="col-lg-2 control-label">费用内容</label><div class="col-lg-5"><input type="text" placeholder="请输入费用名称" class="form-control"  name="cost_name[]" value=""> </div><!-- /.col --> <div class="col-lg-2"> <input type="text" placeholder="费用金额" class="form-control"  name="cost_amount[]" value=""> </div> <div class="col-lg-2"> <input type="button" class="delCost btn btn-danger m-left-xs btn-block" value="删除当前行 - "> </div> </div>');

            //删除当前行数据
            $('.delCost').click(function () {
                this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
            });
        });



    }
});

function getCarNoInfo(carNo){

    $.ajax({
        url:ROOT+"/Admin/Car/doAction/action/getCarNo"//改为你的动态页
        ,type:"POST"
        ,data:{
            'carNo':carNo
        }
        ,dataType: "json"
        ,success:function(json){
            if(json.success == 1){

                if(json.msg != null){
                    $('#wlmslist').empty();

                    for (var i=0;i<json.msg.length;i++){
                        $('#wlmslist').append("<option>"+json.msg[i]+"</option>");            //添加下拉列表
                    }
                }else {
                    return false;
                }

            }
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}

function getDriverNoInfo(driverName){

    $.ajax({
        url:ROOT+"/Admin/Driver/doAction/action/getDriverName"//改为你的动态页
        ,type:"POST"
        ,data:{
            'driverName':driverName
        }
        ,dataType: "json"
        ,success:function(json){
            if(json.success == 1){

                if(json.msg != null){
                    $('#namelist').empty();

                    for (var i=0;i<json.msg.length;i++){
                        $('#namelist').append("<option>"+json.msg[i]+"</option>");            //添加下拉列表
                    }
                }else {
                    return false;
                }


            }
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}

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