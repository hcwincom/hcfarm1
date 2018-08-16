//[Custom Javascript]

//Project:	Delicious Restaurant - Multipage Html Responsive Template
//Version:	1.1
//Primary use:	Delicious Restaurant - Multipage Html Responsive Template 

//add your script here

// 手机导航
$('.dropdown-toggle').on('click',function(e){
	e.prevent()
})

window.onload = function(){
	if(screen.width > 414 ){
		$('.container-nav ul.pagination ').addClass('pagination-lg')
	}else{
		$('.container-nav ul.pagination ').removeClass('pagination-lg')
	}
}

// 手机正则验证

function provingPhone(phone){
	var reg= /^(((13|14|15|18|17)\d{9}))$/;
	return reg.test(phone)
}
function provingName(name){
	var reg= /^[^\u4e00-\u9fa5]{2,}|[A-Za-z]{1}[A-Za-z0-9_.]{1,}$/;
	return reg.test(name)
}

function isEmail(str){ 
	var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+$/; 
	return reg.test(str)
} 

function proving(form){
	var InputName=$('#InputName').val();
	var InputPhone=$('#InputPhone').val();
	var InputEmail=$('#InputEmail').val();
	var InputMessage=$('#InputMessage').val();

	if(InputName=="" || InputName=="您的名字" || InputName.length>20){
		$('#InputName+b').show();
		
		return false;
	}else  if(InputPhone ==""  || InputPhone=="您的手机号" || provingPhone(InputPhone) == false){

		$('#InputPhone+b').show();
		return false;
	}else if(InputEmail =="" || InputEmail=="您的邮箱"  || isEmail(InputEmail) === false ){
		$('#InputEmail+b').show();
		return false;
	}else if(InputMessage == "" || InputMessage=="留言内容" || InputMessage.length>200){
		$('#InputMessage+b').show();
		return false;
	}
}

$('#InputName').focus(function(){
	$('#InputName+b').hide();
})
$('#InputPhone').focus(function(){
	$('#InputPhone+b').hide();
})
$('#InputEmail').focus(function(){
	$('#InputEmail+b').hide();
})
$('#InputMessage').focus(function(){
	$('#InputMessage+b').hide();
})

/**placeholder兼容性问题*/
 // if(navigator.appName == "Microsoft Internet Explorer"&&parseInt(navigator.appVersion.split(";")[1].replace(/[ ]/g, "").replace("MSIE",""))<9){
 	 $("[placeholder]")
    .focus(function() {
      var input = $(this);
      if (input.val() == input.attr("placeholder")) {
        input.val("");
        input.removeClass("placeholder");
      }
    })
    .blur(function() {
      var input = $(this);
      if (input.val() == "" || input.val() == input.attr("placeholder")) {
        input.addClass("placeholder");
        input.val(input.attr("placeholder"));
      }
    })
    .blur()
    .parents("form")
    .submit(function() {
      $(this).find("[placeholder]").each(function() {
        var input = $(this);
        if (input.val() == input.attr("placeholder")) {
          input.val("");
        }
      });
    });
 // }
