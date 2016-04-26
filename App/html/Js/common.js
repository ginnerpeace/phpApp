$(function(){
    $.ajax({
        url:'http://www.onmpw.com/lt/phpPro/index.php?c=Channel&a=find',
        dataType:'json',
        type:'post',
        data:{id:1},
        success:function(data){
            var html = '';
            for(var i=0;i<data.length;i++){
                html += '<tr id="myid_'+data[i].id+'" data-dep='+data[i].dep+' class="parent_'+data[i].parId+' tr" data-id='+data[i].id+'>';
                html += '<td><input type="checkbox" name="checkbox" /> </td>';
                html += '<td>'+data[i].id+'</td>';
                var dep = '';
                for(var j=1;j<data[i].dep;j++){
                    dep +='&nbsp;&nbsp;';
                }
                if(data[i].dep > 1){
                    dep += '|--&nbsp;';
                }
                html += '<td>'+dep+'<i>'+data[i].name+'</i></td>';
                html += '<td><span class="addChild" data-id="'+data[i].id+'" data-dep="'+data[i].dep+'">添加子类</span><span class="del" data-id="'+data[i].id+'">删除</span><span class="modify" data-id="'+data[i].id+'">修改</span><span class="move" data-id="'+data[i].id+'">移动</span></td>';
                html += '</tr>';
            }
            $(".table").append(html);
        },
		error:function(res){
			console.log('err');
		}
    });
    /**
     * 添加子栏目
     */
    $(document).on('click','.addChild',function(){
        //var html = '<tr class="addNode"><td></td><td></td><td><input type="text" name="typename"/></td><td><span class="toadd">添加</span><span class="cancel">取消</span></td></tr>';
        var html = '<tr class="addNode"><td></td><td></td><td></td><td></td></tr>';
        var parId = $(this).data('id');
        var dep = parseInt($(this).data('dep'));
        if($(this).data('have') != 20) {
            $(this).data('have', 20);
            $(this).parents("tr").after(html);
            $(".addNode").animate({
                height:30,
            },100,function(){
               $(this).find('td').eq(2).html('<input type="text" name="typename" />');
               $(this).find('td').eq(3).html('<span class="toadd" data-dep='+(dep+1)+' data-parid='+parId+'>添加</span><span class="cancel">取消</span>')
            });
        }
    });
    /**
     * 取消添加子栏目
     */
    $(document).on('click',".cancel",function(){
        if($(this).prev().data('dep') == 1){
            $(".addtop").data('have',21);
        }else{
            $(this).parents("tr").prev().find(".addChild").data('have',21);
        }
        $(".addNode").animate({
            height:0,
        },100,function(){
            $(this).remove();
        }).find("td").html('');
    });
    /**
     * 确认添加子栏目
     */
    $(document).on('click','.toadd',function(){
        var input = $(this).parents("td").prev().find('input[type="text"]');
        if($(input).val() == ''){
            $(input).css("border-color","#cf001b");
        }else{
            if($(this).data('dep') == 1){
                $(".addtop").data('have',21);
            }else{
                $(this).parents("tr").prev().find(".addChild").data('have',21);
            }
            var parId = $(this).data('parid');
            var dep = $(this).data('dep');
            $.ajax({
                url:'http://www.onmpw.com/lt/phpPro/index.php?c=Channel&a=addChild',
                dataType:'json',
                type:'post',
                data:{name:$(input).val(),parId:parId,dep:dep},
                success:function(data){
                   if(data.res == 1){
                       /*
                        * 首先去掉添加子类的输入框
                        */
                       $(".addNode").animate({
                           height:0,
                       },100,function(){
                           $(this).remove();
                       }).find("td").html('');
                       var cl = $(".parent_"+parId);
                       var html = '<tr id="myid_'+data.id+'" data-dep='+data.dep+' class="parent_'+parId+' tr" data-id='+data.id+'>';
                       html += '<td><input type="checkbox" name="checkbox" /> </td>';
                       html += '<td>'+data.id+'</td>';
                       var dep = '';
                       for(var j=1;j<data.dep;j++){
                           dep +='&nbsp;&nbsp;';
                       }
                       if(data.dep > 1){
                           dep += '|--&nbsp;';
                       }
                       html += '<td>'+dep+$(input).val()+'</td>';
                       html += '<td><span class="addChild" data-id="'+data.id+'" data-dep="'+data.dep+'">添加子类</span><span class="del" data-id="'+data.id+'">删除</span><span class="modify" data-id="'+data.id+'">修改</span><span class="move" data-id="'+data.id+'">移动</span></td>';
                       html += '</tr>';
                       var obj = $(cl).length <= 0?$("#myid_"+parId):getObj(cl);
					   if($(obj).length == 0) obj = $(".channel_title");
                       $(obj).after(html);
                   }else{
                       alert('err');
                   }
                },
				error:function(res){
					console.log('err');
				}
            })
        }
    });
    /**
     * 输入框获得焦点时恢复原来的边框颜色
     */
    $(document).on('focus','td input[type="text"]',function(){
        $(this).css({'border-color':'#eaeaea'});
    });
    /**
     * 删除栏目
     */
    $(document).on('click','.del',function(){
        var that = this;
        onmpw_confirm("确定删除该栏目吗？",function(res){
            if(res){
                var id = $(this).data('id');
                $.ajax({
                    url:'http://www.onmpw.com/lt/phpPro/index.php?c=Channel&a=del',
                    dataType:'json',
                    type:'post',
                    data:{id:id},
                    success:function(data){
                        if(data == 0){
                            console.log('删除失败')
                        }else{
                            for(var i=0;i<data.length;i++){
                                $("#myid_"+data[i]).remove();
                            }
                        }

                    }.bind(this),
                })
            }else{
                console.log('不删除');
            }
        }.bind(this));
    });

    /**
     * 修改栏目
     */
    $(document).on('click','.modify',function(){
        if($(this).data('have') != 20) {
            $(this).data('have', 20);
            var text = $(this).parents("tr").find("td").eq(2).find('i').html();
            $(this).parents("tr").find("td").eq(2).find('i').after('<input type="text" value="'+text+'" data-id='+$(this).data('id')+' /><span class="tomodify">确定</span><span class="cancelmodify">取消</span>');
        }
    });
    /**
     * 确认修改栏目名称
     */
    $(document).on('click','.tomodify',function(){
        var input = $(this).siblings('input[type="text"]');
        if($(input).val() == ''){
            $(input).css('border-color','#cf001b');
        }else{
            var typename = $(input).val();
            var id = $(input).data('id');
            $.ajax({
                url:'http://www.onmpw.com/lt/phpPro/index.php?c=Channel&a=update',
                dataType:'json',
                type:'post',
                data:{id:id,typename:typename},
                success:function(data){
                    if(data.res == 0){
                        console.log('修改失败')
                    }else{
                        $(input).prev().html(typename);
                        $(this).siblings("span,input[type='text']").remove();
                        $(this).remove();
                    }
                }.bind(this),
            })
        }
    });
    /**
     * 取消修改栏目名称
     */
    $(document).on('click','.cancelmodify',function(){
        $(this).parent().next().find("span").eq(2).data('have',21);
        $(this).siblings("span,input[type='text']").remove();
        $(this).remove();
    });
    $(document).on('click','.move',function(){
        if($(this).data('have') != 20) {
            $(this).data('have', 20);
            var id = $(this).data('id');
            getAllId(id);
            $(this).html('取消');
            $(".tr").each(function () {
                if(allids.indexOf($(this).find('span.move').data('id')) == -1){
                    $(this).find("td i").after('<span class="movetohere" data-cid=' + id + '>移动至此</span>');
                }
            });
            allids = [];
        }else{
            $(this).data('have',21);
            $(this).html('移动');
            $(".movetohere").remove();
        }
    });
    $(document).on('click','.movetohere',function(){
         onmpw_confirm("确定移动为该栏目的子栏目吗",function(res){
             if(res){
                 var parId = $(this).parents("tr").data('id');
                 var dp = $("#myid_"+parId).data('dep');
                 $.ajax({
                     url:'http://www.onmpw.com/lt/phpPro/index.php?c=Channel&a=move',
                     dataType:'json',
                     type:'post',
                     data:{parId:parId,id:$(this).data('cid')},
                     success:function(data){
                         if(data.res == 0){
                             console.log("移动失败")
                         }else{
                             $(this).data('have',21);
                             $(this).html('移动');
                             $(".movetohere").remove();
                             var curr = $("#myid_"+$(this).data('cid'));
                             $(curr).attr("class",'parent_'+parId);
                             getAll(curr,dp);
                             var cl = $(".parent_"+parId);
                             var obj = $(cl).length <= 0?$("#myid_"+parId):getObj(cl);
                             var html = '';
                             for(var i=0;i<objects.length;i++){
                                 html += '<tr id="'+$(objects[i].obj).attr('id')+'" data-dep='+objects[i].dep+' class="'+$(objects[i].obj).attr('class')+' tr" data-id='+$(objects[i].obj).data('id')+'>';
                                 html += '<td><input type="checkbox" name="checkbox" /> </td>';
                                 html += '<td>'+$(objects[i].obj).data('id')+'</td>';
                                 var dep = '';
                                 for(var j=1;j<objects[i].dep;j++){
                                     dep +='&nbsp;&nbsp;';
                                 }
                                 if(objects[i].dep > 1){
                                     dep += '|--&nbsp;';
                                 }
                                 html += '<td>'+dep+'<i>'+$(objects[i].obj).find('td i').html()+'</i></td>';
                                 html += '<td><span class="addChild" data-id="'+$(objects[i].obj).data('id')+'" data-dep="'+objects[i].dep+'">添加子类</span><span class="del" data-id="'+$(objects[i].obj).data('id')+'">删除</span><span class="modify" data-id="'+$(objects[i].obj).data('id')+'">修改</span><span class="move" data-id="'+$(objects[i].obj).data('id')+'">移动</span></td>';
                                 html += '</tr>';
                             }
                             $(obj).after(html);
                             objects = [];
                         }
                     }.bind(this),
                 })
             }
         }.bind(this));
    });
	$(document).on('click','.addtop',function(){
        //var html = '<tr class="addNode"><td></td><td></td><td><input type="text" name="typename"/></td><td><span class="toadd">添加</span><span class="cancel">取消</span></td></tr>';
        var html = '<tr class="addNode"><td></td><td></td><td></td><td></td></tr>';
        var parId = $(this).data('id');
        var dep = parseInt($(this).data('dep'));
        if($(this).data('have') != 20) {
            $(this).data('have', 20);
            $(".channel_title").after(html);
            $(".addNode").animate({
                height:30,
            },100,function(){
                $(this).find('td').eq(2).html('<input type="text" name="typename" />');
                $(this).find('td').eq(3).html('<span class="toadd" data-dep=1 data-parid=0>添加</span><span class="cancel">取消</span>')
            });
        }
    });
});
var allids = [];
var objects = [];
/**
 * 得到所有的子栏目以及子栏目的子栏目
 * 将所有的栏目按照原先的顺序保存在objects数组中
 * @param obj
 * @param dep
 */
function getAll(obj,dep){
    dep = parseInt(dep);
    dep += 1;
    var id = $(obj).data('id');
    objects.push({obj:obj,dep:dep});
    $(obj).remove();
    var os = $(".parent_"+id);
    if(os.length>0){
        $(os).each(function(){
            getAll(this,dep);
        })
    }else{
        return ;
    }
}
/**
 * 根据当前id，得到所有的子栏目以及子栏目的id
 * 将这些结果保存在 allids数组中
 * @param parId
 */
function getAllId(parId){
    allids.push(parId);
    var cd = $(".parent_"+parId)
    if(cd.length > 0){
        $(cd).each(function(){
            getAllId($(this).data('id'));
        })
    }else{
        return ;
    }
}
/**
 * 添加子栏目的时候，找到同级栏目的最后一个子栏目
 * @param allobj
 * @returns {*}
 */
function getObj(allobj){
    var parId = $(allobj).eq($(allobj).length-1).data('id');
    var cl = $(".parent_"+parId);
    if($(cl).length <= 0) return $(allobj).eq($(allobj).length-1);
    else{
        cl = getObj(cl);
    }
    return cl;
}
/**
 * 自定义confirm确认框
 * @param info
 * @param callback  回调函数
 */
function onmpw_confirm(info,callback){
    var identify = "whitebak";
    var bak = createBakDiv(identify);
    var coninf = document.createElement("div");
    coninf.className = "confirm-info";
    var infop = document.createElement("p");
    infop.innerHTML = info;
    coninf.appendChild(infop);
    var a1 = document.createElement("a");
    a1.innerHTML = "确定";
    a1.id = "confirm-a";
    a1.onclick = function(){
        removeBak(identify);
        callback(true);

    }
    var a2 = document.createElement("a");
    a2.innerHTML = "取消";
    a2.id = "cancel-a";
    a2.onclick = function(){
        removeBak(identify);
        callback(false);
    }
    coninf.appendChild(a1);
    coninf.appendChild(a2);
    bak.appendChild(coninf);

    showGrayBak(bak,identify);
}
/*
 *显示灰色背景
 *
 */
function showGrayBak(bak,id){
    var y;
    if(window.scrollMaxY){
        y = window.scrollMaxY + window.innerHeight;
    }else{
        y = window.innerHeight;
    }
    bak.style.height = y + "px";
    document.getElementsByTagName("body").item(0).appendChild(bak);
    $("#"+id).show().addClass("show");
}
/**
 * 创建灰色背景div
 * @returns
 */
function createBakDiv(classname){
    var bakDiv = document.createElement("div");
    bakDiv.className = classname;
    bakDiv.id = classname;
    return bakDiv;
}
/**
 * 取消灰色背景
 * @param id
 */
function removeBak(id){
    if(window.parent == window){
        $("#"+id).remove();
    }else{
        $(window.parent.document.getElementById(id)).remove();
    }
}