$(function(){

    //判断男女，并设置radio选中状态

    if($('#the_customer_sex').val() == '0'){
        $("input:radio[name='customer_sex']").eq(0).attr("checked",true);

        $('#leave_date').hide();

    }else if ($('#the_customer_sex').val() == '1'){
        $("input:radio[name='customer_sex']").eq(1).attr("checked",true);
        $('#leave_date').show();
    }

    /**
     * 自动验证
     */
    if($('#updateForm').length > 0) {
        //文本框失去焦点后
        $('form :input').blur(function(){
            var $parent = $(this).parent();
            $parent.find(".formtips").remove();

            //车主验证
            if( $(this).is('#customer_name') ){
                var idname = 'customer_name';
                if( (this.value.length > 15) || (this.value.length < 2) || (this.value == '') ){
                    var errorMsg = '姓名不能为空，并且位数只能是2到15位';
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
function delCustomerInfo(id){

    //弹出确认框
    if (!confirm('确定要删除吗？')){
        return false;
    }

    $.ajax({
    url:ROOT+"/Admin/Customer/doAction/action/del"//改为你的动态页
    ,type:"POST"
    ,data:{
            'id':id
        }
    ,dataType: "json"
    ,success:function(json){
        if(json.success == 1){
            alert('删除成功');
            location = ROOT+"/Admin/Customer/doAction/action/all";

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