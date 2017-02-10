<?php 
	
	//通知node端-发送请求
 	function requestNodeMessage($url="",$post_data=""){

	        $postUrl = $url;
	        $curlPost = $post_data;
	        $ch = curl_init(); 
	        curl_setopt($ch, CURLOPT_URL,$postUrl); 
	        curl_setopt($ch, CURLOPT_HEADER, 0); 
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	        curl_setopt($ch, CURLOPT_POST, 1); 
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	        curl_setopt($ch, CURLOPT_TIMEOUT,10);
	        $data = curl_exec($ch); 
	        curl_close($ch);
	        
	        return $data;
	}   

	//curl  get请求
	function curlGetRequest( $url ){
		$ch=curl_init();
 		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		curl_setopt($ch, CURLOPT_HEADER, 0);
 		$data = curl_exec($ch);
 		curl_close($ch);
 		return  $data;
	}