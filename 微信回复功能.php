<?php 
	
 class weixin{

 	 
 	private $token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx3ce9bb75b2666423&secret=fca9e207b8fc48c42a9016b8cfcdfcd7';
 	 

 	


 	/*在配置时只运行一次*/
 	public function checkMsg(){

		$signature = $_GET["signature"];
	    $timestamp = $_GET["timestamp"];
	    $nonce = $_GET["nonce"];	
	      
	    $echoStr = $_GET['echostr'];

		$token = 'wangyu123';
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode('',$tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr !== $signature ){
			return false;
		}else{
			 echo $echoStr;
		}

 	}


 	/*接收到用户消息回复关注事件*/
 	public function sendMsg(){
 
 		$msg = $GLOBALS['HTTP_RAW_POST_DATA'];

 		//将xml字符串转换成对象
 		$msgXml = simplexml_load_string($msg); 

 		//file_put_contents('test.php',$msgXml->Content);

 		
		/*单文本回复消息*/
		/*$toUser = $msgXml->FromUserName; //用户账号(OpenId)
		$FromUserName = $msgXml->ToUserName;
		$time = time();
		$MsgType = 'text';
		$Content = '欢迎关注王宇的第一个例子';

		$template = '<xml>
	 				<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>';
		$msg = sprintf($template,$toUser,$FromUserName,$time,$MsgType,$Content);*/

			/*如果发来的是文本回复张图片*/
		/*	if( $msgXml->MsgType=='text' ){
				$toUser = $msgXml->FromUserName;
				$fromUserName = $msgXml->ToUserName;
				$time = time();
				$MsgType = 'image';
				$MediaId = "FWDO2p4ubeHcceRUqQXiZ_DPld_pSe9xt3EvLSrniqFjuPYLgBOg9wXukILiFq18";

				$template = '<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Image>
							<MediaId><![CDATA[%s]]></MediaId>
							</Image>
							</xml>';

							
				$msg = sprintf($template,$toUser,$fromUserName,$time,$MsgType,$MediaId);			
				echo $msg;
			}*/


		/*	
		//回复图文消息	
		if( $msgXml->MsgType == 'text' ){

				$toUser = $msgXml->FromUserName;
				$fromUserName = $msgXml->ToUserName;
				$time = time();

				$title = '小雨一日游';
				$des = '在天安门游玩';
				$pic = 'http://web1611091713389.bj.bdysite.com/teacher3.jpg';
				$url = 'http://www.baidu.com';
				$template = '<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>1</ArticleCount>
						<Articles>
						<item>
						<Title><![CDATA[%s]]></Title> 
						<Description><![CDATA[%s]]></Description>
						<PicUrl><![CDATA[%s]]></PicUrl>
						<Url><![CDATA[%s]]></Url>
						</item>					 
						</Articles>
						</xml>';

				echo sprintf($template,$toUser,$fromUserName,$time,$title,$des,$pic,$url);	
			}*/


			if( strtolower($msgXml->MsgType) == 'event' ){
				if( strtolower($msgXml->Event) === 'location'){
						$toUser = $msgXml->FromUserName;
						$fromUserName = $msgXml->ToUserName;
						$time = time();

						$MsgType = 'text';
						$Content = '您的位置是精度'.$msgXml->Latitude.'纬度是'.$msgXml->Longitude;

						$template = '<xml>
					 				<ToUserName><![CDATA[%s]]></ToUserName>
									<FromUserName><![CDATA[%s]]></FromUserName>
									<CreateTime>%s</CreateTime>
									<MsgType><![CDATA[%s]]></MsgType>
									<Content><![CDATA[%s]]></Content>
									</xml>';
						$msg = sprintf($template,$toUser,$FromUserName,$time,$MsgType,$Content);

						echo $msg;
				}
			}



			/*获取用户信息,回复用户【也可以进行其他处理】*/
			if( $msgXml->MsgType == 'text' ){

				/*获取用户信息*/
				$access_token = $this->curl_get( $this->token_url );
				$access_token = json_decode($access_token,true)['access_token'];
				$user_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$msgXml->FromUserName.'&lang=zh_CN';

				$userInfo = $this->curl_get( $user_url );
				$user = json_decode($userInfo,true);


				$toUser = $msgXml->FromUserName;
				$fromUserName = $msgXml->ToUserName;
				$time = time();

				$MsgType = 'text';
				$Content = '你好,'.$user['nickname'].',来自'.$user['city'].'-'.$user['province'];

				$template = '<xml>
			 				<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>';
				$msg = sprintf($template,$toUser,$FromUserName,$time,$MsgType,$Content);
				echo $msg;
			}

 	}


 	public function  curl_get($url){
 		$ch=curl_init();
 		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_HEADER, 0);
 		$data = curl_exec($ch);
 		curl_close($ch);
 		return  $data;
 	}

 } 




 (new weixin())->sendMsg();