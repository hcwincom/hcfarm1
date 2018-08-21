$(function () {

    // 左侧点击展开
    $('.menudiv p').click(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active').next('ul').hide().end();
        } else {
            $(this).addClass('active').next('ul').show();
        }
        $(this).parent().siblings().children('p').removeClass('active').next('ul').hide();
    });	
    //商品排序选项卡切换
    $('.protabul li').click(function () {
        $(this).addClass('active_tab').siblings('li').removeClass('active_tab');
    });
    
// 筛选点击展开
    $('.screenem').click(function () {
        $(this).toggleClass('addbg').parent().next('.screendiv').slideToggle();
        $('.blackbg').toggle();

    });

//  点击展开左侧导航
    $('#navClick').click(function () {
        $('.leftnav').toggleClass('left10');
        $('.indbox').toggleClass('indbox_s');
        $('.navblack').show();
        $('.navmain').addClass('addhide');
        //$('body,html').css('overflow','hidden');
    });
    $('.ckh').click(function () {
        $('.leftnav').toggleClass('left10');
        $('.indbox').toggleClass('indbox_s');
        $('.navblack').hide();
        $('.navmain').removeClass('addhide');
        //$('body,html').css('overflow','auto');
    });

	// 网点点击切换
	$(".netWorkpoin li").click(function(){
		var index = $(this).index();
		$(this).addClass("activeli").siblings().removeClass("activeli");
		$(".dotdiv div").eq(index).css("display","block").siblings().css("display","none");
	});

   // 点击查看全部资讯
   $(".infoul li").click(function(){
   		$(this).find(".infoCont").toggle();
   });

	//更多导航 点击一级出现二级
	$('.fistli').click(function(){
		$(this).children('i').toggleClass('rodeg90s').parent().nextAll('.twodl').toggle().end().parent().parent().siblings().find('dl').hide();
	});
	$('.scrolltopa').click(function(){		
		$('body,html').stop().animate({scrollTop:420},300);
	});
	
	// 意见建议导航
	$(".showdr").click(function(){
		$(".showtopdl").stop().toggle('animatenav');
	});
    
    //  点击查看商品显示商品信息
    $("#checkGoods p").click(function () {
        $(".showdiv").toggle();
    });
    
    // 点击选择发货形式
    $("#racheck").click(function () {
        $(".onlineGoods").show();
        $(".storeGoods").hide();
    })

    $("#radiocheck").click(function () {
        $(".onlineGoods").hide();
        $(".storeGoods").show();
    });	









});