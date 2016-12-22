<?php
$max_code = 0;
$response = array();
$dir = dirname(__DIR__) . '/response';
if(is_dir($dir)){
	if($dh = opendir($dir)){
		include_once dirname(__DIR__) . '/library/Response.php';
		while(($file = readdir($dh)) !== false){
			$file_path = $dir . '/' . $file;
			$path_parts = pathinfo($file_path);
			if($path_parts['extension'] == 'php'){
				include_once $file_path;
				$className = '\\Response\\' . $path_parts['filename'];
				$class = new $className;
				$name = $path_parts['filename'];
				$value = array(array('class_code', 'int', $class->class_code, null));
				$response[$name] = array_merge($value, $class->getReq());
				if($max_code < $class->class_code){
					$max_code = $class->class_code;
				}
			}
		}
		ksort($response);
		closedir($dh);
	}
}

$request = array(
	'auth => [Auth] (계정인증)' => array( 'acc_id-계정 고유 아이디' => 'string' ),
	'auth_withdraw => [Auth] (계정탈퇴)' => array( 'acc_id-계정 고유 아이디' => 'string', '[msg]-메시지' => 'string' ),
);
ksort($request);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>api document page</title>
<style>
html, body {margin:0px; padding:0px; height:100%; overflow:hidden;}
.contents {margin-top:2px;width:100%;height:100%;overflow:auto; border:1px solid #000;}
.title {height:100%; height:26px; border:1px solid #000;text-align:center;font-weight:bold;}
</style>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
var focus_num = -1;
var request = <?php echo (!empty($request) ? json_encode($request) : '{}'); ?>;
var response = <?php echo (!empty($response) ? json_encode($response) : '{}'); ?>;
$(document).ready(function(){
    $(window).resize(function(){
        var height = window.innerHeight ||
                 html.clientHeight  ||
                 body.clientHeight  ||
                 screen.availHeight;
        console.log(height);
        $('.contents').css('height', (height-54)+'px');
    }).resize();

    $(window).resize();
    
	var num = 0;
	var request_view = $("#request_view");
	for(var param in request){
		var sp = param.indexOf("[");
		var ep = param.indexOf("]");
		var reqs = null;
		if(sp > -1 && ep > -1){
			reqs = param.substring(sp+1, ep);
		}

		var param_div = $("<div name='params' style='margin:4px; padding:4px; border:1px solid #DDD; cursor:pointer;' />");
		param_div.html("<b>" + param + "</b>");
		param_div.click(function(){
			$("#response_view").children().hide();
			var attr_reqs = $(this).attr("reqs");
			var attr_num = $(this).attr("num");
			$("div[name='params']").css({"background-color": "none"});

			if(focus_num == attr_num){
				$("#response_view").children().show();
				focus_num = -1;
			}else{
				if(typeof attr_reqs != "undefined"){
					var reqs = attr_reqs.split(",");
					for(var i in reqs){
						$("#res_" + reqs[i]).show();
					}
				}
				$(this).css({"background-color": "#F3F3F3"});
				focus_num = attr_num;
			}
		}).attr("reqs", reqs).attr("num", num);
		num = num + 1;

		//param_div.mouseenter(function(){
		//	$("#response_view").children().hide();
		//	var reqs = $(this).attr("reqs").split(",");
		//	for(var i in reqs){
		//		$("#res_" + reqs[i]).show();
		//	}
		//}).mouseleave(function(){
		//	$("#response_view").children().show();
		//}).attr("reqs", reqs);

		if(typeof request[param] == "object"){
			for(var key in request[param]){
				var key_div = $("<div style='margin:4px 0 0 40px;' />");
				var val = key.split("-");
				var value = val[0];
				if(typeof val[1] != "undefined"){
					value = value + " - <u>" + val[1] + "</u>";
				}
				key_div.html("(" + request[param][key] + ") " + value);
				param_div.append(key_div);
			}
		}
		request_view.append(param_div);
	}

	var response_view = $("#response_view");
	for(var param in response){
		var param_div = $("<div style='margin:4px; padding:4px; border:1px solid #DDD;' />");
		param_div.attr("id", "res_" + param);
		param_div.html("<b>" + param + "</b>");

		if(typeof response[param] == "object"){
			for(var i in response[param]){
				var key_div = $("<div style='margin:4px 0 0 40px;' />");
				var val = "(" + response[param][i][1] + ") " + response[param][i][0];
				switch(response[param][i][1]){
					case "int":
						val = val + " = " + response[param][i][2];
						break;
					case "string":
						val = val + " = '" + response[param][i][2] + "'";
						break;
					case "array":
						val = val + " = array()";
						break;
				}
				if(response[param][i][3]){
					val = val + " = (object)" + response[param][i][3];
				}
				if(response[param][i][4]){
					val = val + " - <u>" + response[param][i][4] + "</u>";
				}
				key_div.html(val);
				param_div.append(key_div);
			}
		}
		response_view.append(param_div);
	}
});
</script>
</head>
<body>
    <div style="width:100%; height:100%;padding:6px;">
        <div style="float:left; border:1px solid #000; padding:2px; width:49%;">
        	<div class="title">REQUEST</div>
        	<div id="request_view" class="contents"></div>
        </div>
        <div style="float:left;border:1px solid #000; padding:2px; width:49%; margin-left:4px;">
        	<div class="title">RESPONSE[<?php echo $max_code; ?>]</div>
        	<div id="response_view" class="contents"></div>
        </div>
    </div>
</body>
</html>
