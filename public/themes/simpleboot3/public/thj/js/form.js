
// 提货编码验证
	function isNumber(number){
		var pattern = /^\d{8}$/;
		return pattern.test(number);
	}
	// 验证6位密码
	function isPassword(password){
	    var pattern=/^\d{6}$/;
	    return pattern.test(password)
	}
	// 验证4位验证码
	function isCode(password){
	    var pattern=/^\d{4}$/;
	    return pattern.test(password)
	}
	// 验证中文名称
	function isChinaName(name) {
	    var pattern = /^[\u4E00-\u9FA5]{1,6}$/;
	    return pattern.test(name);
	}
		
	// 验证手机号码
	function isPhoneNo(phone) { 
	    var pattern = /^1[34578]\d{9}$/; 
	    return pattern.test(phone); 
	}
	
		
	//  提货验证
	function pickUp(){
		var num = $.trim($("input[name=number]").val());
		var pwd = $.trim($("input[name=password]").val());
		if(num == "" || isNumber(num) == false){
			$("input[name=code]").focus();
			$(".indyz").html("请正确填写提货编码");
			return false;
		}else if(pwd == "" || isPassword(pwd) == false){
			$("input[name=password]").focus();
			$(".indyz1").html("请输入正确密码");
			return false;
		}
		return true;
	}


//  礼品券验证
	function pickGoods(){
		var num = $.trim($("input[name=number]").val());
		var pwd = $.trim($("input[name=password]").val());
		var code = $.trim($("input[name=codeNum]").val());
		if(num == "" || isNumber(num) == false){
			$("input[name=code]").focus();
			$(".indyz").html("请正确填写提货编码");
			return false;
		}else if(pwd == "" || isPassword(pwd) == false){
			$("input[name=password]").focus();
			$(".indyz1").html("请输入正确密码");
			return false;
		}else if(code = "" || code.length != 4){
			$("input[name=codeNum]").focus();
			$(".indyz2").html("请输入正确验证码");
			return false;
		}else{
			$(".indyz").html("");
			$(".indyz1").html("");
			$(".indyz2").html("");
		}
		return true;
	}


	function verification(){
		var num = $.trim($("input[name=number]").val());

		var code = $.trim($("input[name=codeNum]").val());
		if(num == "" || isNumber(num) == false){
			$("input[name=code]").focus();
			$(".indyz").html("请正确填写提货编码");
			return false;
		}else if(code = "" || code.length != 4){
			$("input[name=codeNum]").focus();
			$(".indyz2").html("请输入正确验证码");
			return false;
		}	
		return true;
	}



	//  提货提交地址验证

	
	
	//  意见建议验证
	function validateForm(){
		var title = $.trim($("#title").val());
		var content = $.trim($("#content").val());
		var contactName = $.trim($("#contactName").val());
		var phNumber = $.trim($("#phoneNumber").val());
		var yanCode = $.trim($("#yancode").val());
		if(title == ""){
			$("#title").focus();
			$(".msg").html("请输入标题");
			return false;
		}else if(content == ""){
			$("#content").focus();
			$(".msg").html("请输入内容");
			return false;
		}else if(contactName == "" || isChinaName(contactName) == false){
			$("#contactName").focus();
			$(".msg").html("请输入联系人姓名");
			return false;
		}else if(phNumber == "" || isPhoneNo(phNumber) == false){
			$("#phoneNumber").focus();
			$(".msg").html("请输入手机号码");
			return false;
		}else if(yanCode.length != 4){
			$("#yancode").focus();
			$(".msg").html("请输入正确的验证码");
			return false;
		}
		return true;	
	}
	
	// 申请理赔
	function validateFormA(){
		var picurl1 = $("#picUrl1").val();
		var picurl2 = $("#picUrl2").val();
		var picurl3 = $("#picUrl3").val();
		var picurl4 = $("#picUrl4").val();
		
		var img1 = $("#picUrl1")[0].files[0];
		var img2 = $("#picUrl2")[0].files[0];
		var img3 = $("#picUrl3")[0].files[0];
		var img4 = $("#picUrl4")[0].files[0];
		
		if($.trim($("#orderNum").val()) == ""){
			$("#orderNum").focus();
			$(".msg").html("请输入货物订单号");
			return false;
		}else if($.trim($("#expNum").val()) == ""){
			$("#expNum").focus();
			$(".msg").html("请输入物流订单号");
			return false;
		}else if($.trim($("#description").val()) == ""){
			$("#description").focus();
			$(".msg").html("请输入理赔原因");
			return false;
		}else if($.trim($("#picUrl1").val()) == ""){
			$("#picUrl1").focus();
			$(".msg").html("请上传理赔凭证");
			return false;
		}else if($.trim($("#applyName").val()) == "" || isChinaName($.trim($("#applyName").val())) == false){
			$("#applyName").focus();
			$(".msg").html("请填写申请人姓名");
			return false;
		}else if($.trim($("#applyPnone").val()) == "" || isPhoneNo($.trim($("#applyPnone").val())) == false){
			$("#applyPnone").focus();
			$(".msg").html("请填写申请人手机号码");
			return false;
		}else if($.trim($("#yancode").val()).length != 4){
			$("#yancode").focus();
			$(".msg").html("请输入正确的验证码");
			return false;
		}
		
		//上传图片
		if(picurl1 == "" && picurl2 == "" && picurl3 == "" && picurl4 == ""){
		    alert('请至少上传一张图片');
			return false;
		}
		
		var ruleimg = /^(.*)(\.)(JPEG|jpeg|JPG|jpg|GIF|gif|bmp|BMP|png|PNG)$/;
		if($("#picUrl1").val() !=''){
			var size1 = img1.size / 1024;
			if(!ruleimg.test(picurl1) || size1 > 6144){
				alert("理赔凭证图1,请上传jpg、png等常用格式的图片,最大为6M");
				return false;
			}
		}
		
		if($("#picUrl2").val() !=''){
			var size2 = img2.size / 1024;
			if(!ruleimg.test(picurl2) || size2 > 6144){
				alert("理赔凭证图2,请上传jpg、png等常用格式的图片,最大为6M");
				return false;
			}
		}
		
		if($("#picUrl3").val() !=''){
			var size3 = img3.size / 1024;
			if(!ruleimg.test(picurl3) || size3 > 6144){
				alert("理赔凭证图3,请上传jpg、png等常用格式的图片,最大为6M");
				return false;
			}
		}
		
		if($("#picUrl4").val() !=''){
			var size4 = img4.size / 1024;
			if(!ruleimg.test(picurl4) || size4 > 6144){
				alert("理赔凭证图4,请上传jpg、png等常用格式的图片,最大为6M");
				return false;
			}
		}
		return true;			
	}
	
	// 理赔验证
	function validateFormB(){
		var picurl1 = $("#picurl1").val();
		var picurl2 = $("#picurl2").val();
		var picurl3 = $("#picurl3").val();
		var picurl4 = $("#picurl4").val();
		
		var img1 = $("#picurl1")[0].files[0];
		var img2 = $("#picurl2")[0].files[0];
		var img3 = $("#picurl3")[0].files[0];
		var img4 = $("#picurl4")[0].files[0];
		
		if($.trim($("#orderno").val()) == ""){
			$("#orderno").focus();
			$("#showorderno").show();
			return false;
		}else if($.trim($("#description").val()) == ""){
			$("#description").focus();
			$("#showdescription").show();
			return false;
		}else if($.trim($("#picurl1").val()) == ""){
			$("#picurl1").focus();
			return false;
		}else if($.trim($("#contacts").val()) == "" || isChinaName($.trim($("#contacts").val())) == false){
			$("#contacts").focus();
			$("#showcontacts").show();
			return false;
		}else if($.trim($("#contactstel").val()) == "" || isPhoneNo($.trim($("#contactstel").val())) == false){
			$("#contactstel").focus();
			$("#showcontactstel").show();
			return false;
		}	
		
		
		if(picurl1 == "" && picurl2 == "" && picurl3 == "" && picurl4 == ""){
		    alert('请至少上传一张图片');
			return false;
		}
		
		var ruleimg = /^(.*)(\.)(JPEG|jpeg|JPG|jpg|GIF|gif|bmp|BMP|png|PNG)$/;
		if($("#picurl1").val() !=''){
			var size1 = img1.size / 1024;
			if(!ruleimg.test(picurl1) || size1 > 2144){
				alert("理赔凭证图1,请上传jpg、png等常用格式的图片,最大为2M");
				return false;
			}
		}
		
		if($("#picurl2").val() !=''){
			var size2 = img2.size / 1024;
			if(!ruleimg.test(picurl2) || size2 > 2144){
				alert("理赔凭证图2,请上传jpg、png等常用格式的图片,最大为2M");
				return false;
			}
		}
		
		if($("#picurl3").val() !=''){
			var size3 = img3.size / 1024;
			if(!ruleimg.test(picurl3) || size3 > 2144){
				alert("理赔凭证图3,请上传jpg、png等常用格式的图片,最大为2M");
				return false;
			}
		}
		
		if($("#picurl4").val() !=''){
			var size4 = img4.size / 1024;
			if(!ruleimg.test(picurl4) || size4 > 2144){
				alert("理赔凭证图4,请上传jpg、png等常用格式的图片,最大为2M");
				return false;
			}
		}	
		return true;
		
	}
	//  意见建议
	function validateFormC(){
		if($.trim($("#title").val()) == ""){
			$("#title").focus();
			$("#showtitle").show();
			return false;
		}else if($.trim($("#content").val()) == ""){
			$("#content").focus();
			$("#showcontent").show();
			return false;
		}else if($.trim($("#contact").val()) == ""){
			$("#contact").focus();
			$("#showcontact").show();
			return false;
		}else if($.trim($("#mphone").val()) == "" || isPhoneNo($.trim($("#mphone").val())) == false){
			$("#mphone").focus();
			$("#showmphone").show();
			return false;
		}else if($.trim($("#yancode").val()) == "" || isCode($.trim($("#yancode").val())) == false){
			$("#yancode").focus();
			$("#showyancode").show();
			return false;
		}
		return true;
	}
	
	
	
















