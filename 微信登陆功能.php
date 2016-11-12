<?php 
	
	//静默登陆： https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3ce9bb75b2666423&redirect_uri=http://web1611091713389.bj.bdysite.com/test.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect
 
   //提示登陆 https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3ce9bb75b2666423&redirect_uri=http://web1611091713389.bj.bdysite.com/test.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect



	$code = $_GET['code'];

	function  curl_get($url){
 		$ch=curl_init();
 		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_HEADER, 0);
 		$data = curl_exec($ch);
 		curl_close($ch);
 		return  $data;
 	}

 	$access_token = json_decode(curl_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3ce9bb75b2666423&secret=fca9e207b8fc48c42a9016b8cfcdfcd7&code='.$code.'&grant_type=authorization_code'),true);


 	$data = json_decode(curl_get('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN'),true);

  
 	echo "你好,{$data['nickname']},你在{$data['province']}, {$data['city']}吗？";