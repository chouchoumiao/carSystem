
//删除指定车辆信息
function delCarInfo(id){

    //弹出确认框
    if (!confirm('确定要删除吗？')){
        return false;
    }

    $.ajax({
    url:ROOT+"/Admin/Car/doAction/action/del"//改为你的动态页
    ,type:"POST"
    ,data:{
            'id':id
        }
    ,dataType: "json"
    ,success:function(json){
        if(json.success == 1){
            alert('删除成功');
            location = ROOT+"/Admin/Car/doAction/action/all";

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