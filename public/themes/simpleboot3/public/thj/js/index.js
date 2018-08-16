$(function(){
	// 加载公共的
	$("#header").load("header.html");
	$("#footer").load("footer.html");
	$("#list_head").load("list_head.html");
	$("#leftNav").load("leftNav.html");
		
	//轮播图
	var t;
	var index = 0;
	var imgLen = $(".slides>a").length;
	var ulLi = "";
	for(var i = 0;i<imgLen; i++){
		ulLi += "<li></li>"
	}
	ulLi = "<ul>"+ ulLi + "</ul>";
	$(".slidesNav").append(ulLi);
	$(".slidesNav>ul").attr("class","slidesNum");
	$(".slidesNav ul li:first-child").addClass("current");
	// 自动播放
	t = setInterval(autoplay,2000);
	function autoplay(){
		index++;
		if(index>imgLen){
			index=0;
		}
		$(".slidesNum>li").eq(index).addClass("current").siblings().removeClass("current");
		$(".slides>a").eq(index).fadeIn().siblings().fadeOut();
	}
	
	// 点击鼠标图片切换
	$(".slidesNum>li").click(function(){
		$(this).addClass("current").siblings().removeClass("current");
		// 获取索引值
		var index = $(this).index();
		$(".slides>a").eq(index).fadeIn().siblings().fadeOut();
	});
	// 鼠标移进移出
	$(".slidesNav>ul>li,.slidesNav>.slides>a>img").hover(
		function(){
			 clearInterval(t);
		},function(){
			t = setInterval(autoplay,2000);
			
			autoplay();
		}
	)
	
	// 点击切换显示
	$(".middles_grider>ul>li").click(function(){
		var index = $(this).index();
		$(this).addClass("active2").siblings().removeClass("active2");
		$(".middles_detail>div").eq(index).addClass("show2").siblings().removeClass("show2");
	});
	

	// 点击弹出券卡使用流程
//	$(".modal_detail").click(function(){
//		$("#showDiv").fadeIn(1000);	
//	});
//	$(".closeBox").click(function(){
//		$("#showDiv").fadeOut(1000);
//	});
	
	//点击查看详情
	$("#reDetail").click(function(){
		$("#showDetail").fadeIn();
	});
	$(".closeBox").click(function(){
		$("#showDetail").fadeOut();
	});
	
	// 商品中心点击更多切换
	var $category = $('#gselect2 dd:gt(8):not(:last)');
	$category.hide();
	var $toggleBtn = $('#showMore a');
	$toggleBtn.click(function(){
		if($(this).find("span").text() == "+ 更多"){
			$category.show();
			$(this).find("span").text("- 收起")
		}else{
			$category.hide();
			$(this).find("span").text("+ 更多")
		}
	});
	//  
	$("#gselect2 dd").click(function () {
		var _this = $(this);//当前
		var copyThisB = _this.clone();//当前点击复制
		console.log(copyThisB);
		var _class = "selectB cate cate-" + _this.index();//添加到已选条件中的项添加class并且class是”cate-[这里是点击dd的索引数值]“
		var _sigleclass = "cate-" + _this.index();//”cate-[这里是点击dd的索引数值]“
		_this.parent().find("dd:eq(0)").removeClass("selected");//点击产品分类，将全部	第一个dd:eq(0)的样式移除selected	
		_this.addClass("selected");//当前点击的dd添加样式selected		
		//如果点击的是class为select-all全部的
		if ($(this).hasClass("select-all")) {
			$(".cate").remove();//已选条件中所有的cate移除
			_this.removeAttr("data-bind").siblings().removeAttr("data-bind");//移除data-bind
			_this.addClass("selected").siblings().removeClass("selected");//当前【全部】添加样式其他所有的移除样式
		}
		//如果当前点击的dd有属性data-bind
		else if (_this.attr("data-bind")) {
			_this.removeAttr("data-bind");//当前移除参数data-bind
			$(".select-result dl").find("."+_sigleclass).remove();//已选条件的dl发现有当前点击的cass值（因为每个class的索引不一样是唯一的）就移除
			_this.removeClass("selected");//当前移除样式selected			
			//判断，如果已选条件中的cate数组为0，表示没有，那就调用全部的点击事件
			if ($(".cate").length == 0) {
				_this.parent().find(".select-all").click();				
			}			
			
		}else{
			var _id  = "sdd-" + _this.index();//当前点击的dd添加id 为sdd+【索引值】
			_this.attr("data-bind","1").attr("id",_id);//当前的data-bind设置为1，id为_id
			//已选条件叠加当前点击的复制进去，并且添加class以及for属性
			$(".select-result dl").append(copyThisB.addClass(_class).attr("for",_id));
		}
		
	});	
				
				//移除选中的商品分类
				$(".selectB").on("click", function () {
					$(this).remove();
					if($('.selectB').index()<0){//当点击的是最后一个的时候 将全部添加选中样式 移除其他样式			
						$("#select2 .select-all").addClass("selected").siblings().removeClass("selected");
					}		
					$("#"+$(this).attr("for")).removeAttr('data-bind').removeClass('selected')
				});
						
				$(".gselect dd").on("click", function () {
					if ($(".select-result dd").length > 1) {
						$(".select-no").hide();
					} else {
						$(".select-no").show();
					}
				});
				
	
	//检测货物订单号
		$("#orderno").blur(function(){
			var orderno = $("#orderno").val();
			if( orderno==""){
				$("#showorderno").show();
			}
		});
		
		$("#orderno").focus(function(){
			$("#showorderno").hide();
		});
		//检测物流单号
		$("#expno").blur(function(){
			var expno = $("#expno").val();
			if( expno==""){
				$("#showexpno").show();
			}
		});
		
		$("#expno").focus(function(){
			$("#showexpno").hide();
		});
		//检测理赔原因
		$("#description").blur(function(){
			var description = $("#description").val();
			if( description==""){
				$("#showdescription").show();
			}
		});
		
		$("#description").focus(function(){
			$("#showdescription").hide();
		});
		
		
		
		//检测联系人
		$("#contacts").blur(function(){
			var contacts = $("#contacts").val();
			if( contacts==""){
				$("#showcontacts").show();
			}
		});
		
		$("#contacts").focus(function(){
			$("#showcontacts").hide();
		});
		//检测手机号码
		$("#contactstel").blur(function(){
			var contactstel = $("#contactstel").val();
			var rule1 = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/;
			var rule = /^1\d{10}$/;
				if(!(rule.test(contactstel)||rule1.test(contactstel))){
					$("#showcontactstel").show(); 
				}
		});
		
		$("#contactstel").focus(function(){
			$("#showcontactstel").hide();
		});
		//检测验证码
		$("#yancode").blur(function(){
			var yancode = $("#yancode").val();
				var rule = /^\d{4}$/;
				if(!rule.test(yancode)){
					$("#showyancode em").html("请填写4位验证码");
					$("#showyancode").show(); 
				}
		});
		
		$("#yancode").focus(function(){
			$("#showyancode").hide();
		});
		
	//  点击查看详情显示地图
	$(".see .addr").click(function(){
		$(this).parent(".see").next(".mapdisplay").toggle();
	});
	
	//   意见建议下的验证
	//检测标题
		$("#title").blur(function(){
			var title = $("#title").val();
			if( title==""){
				$("#showtitle").show();
			}
		});
		
		$("#title").focus(function(){
			$("#showtitle").hide();
		});
		//检测内容
		$("#content").blur(function(){
			var content = $("#content").val();
			if( content==""){
				$("#showcontent").show();
			}
		});
		
		$("#content").focus(function(){
			$("#showcontent").hide();
		});
		//检测联系人
		$("#contact").blur(function(){
			var contact = $("#contact").val();
			if( contact==""){
				$("#showcontact").show();
			}
		});
		
		$("#contact").focus(function(){
			$("#showcontact").hide();
		});
		//检测手机号码
		$("#mphone").blur(function(){
			var mphone = $("#mphone").val();
			var rule = /^1\d{10}$/;
				if(!rule.test(mphone)){
					$("#showmphone").show(); 
				}
		});
		
		$("#mphone").focus(function(){
			$("#showmphone").hide();
		});
		//检测固定电话
		$("#tphone").blur(function(){
			var tphone = $("#tphone").val();
			if(!tphone==''){
				var rule = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/;
				if(!rule.test(tphone)){
					$("#showtphone").show(); 
				}
			}
		});
		
		$("#tphone").focus(function(){
			$("#showtphone").hide();
		});

		//检测验证码
		$("#yancode").blur(function(){
			var yancode = $("#yancode").val();
				var rule = /^\d{4}$/;
				if(!rule.test(yancode)){
					$("#showyancode em").html("请填写4位验证码");
					$("#showyancode").show(); 
				}
		});
		
		$("#yancode").focus(function(){
			$("#showyancode").hide();
		});

	
});

function searchAgain(){
	    $("#code").val("");
	    $("#checkcode").val("");
		$(".cardr2").hide();
		$(".pick_top").show();
		$(".codeImg").click();
}



















