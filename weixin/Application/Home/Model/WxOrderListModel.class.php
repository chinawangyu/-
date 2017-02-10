<?php 
	
	namespace Home\Model;

	class WxOrderListModel extends \Think\Model{

		//增加订单数据-返回应支付金额
		public function addOrderData( $data ){

			//获取当前商品型号单价
			$result = D('WxGoodsGoldDetails')->getCurrentGoodsDetailsPrice( $data['gold_details_id'] );

			$data['total_fee']   = $result['gold_price'];
			$data['gold_number'] = $result['gold_number'];
			$data['gold_show']   = $result['gold_show'];

			$data['pay_time']  = time();
			$this->add( $data );

			return $data['total_fee'];
		}

		//更改当前订单状态成功付款
		public function setOrderDataSuccess( $out_trade_no ){
			//修改当前订单的状态
			$map['out_trade_no'] = array('eq',$out_trade_no);
			$orderData = $this->field('pay_status,gold_details_id,out_trade_no,total_fee,account_id,openid')->where($map)->find();

			//如果没有标识支付成功的话,进行标记
			if( $orderData['pay_status']!=1 ){
				
				$data = array(
					'pay_status' => 1, 
					'pay_time'   => time(),
				);
				$this->where($map)->save($data);

				//对当前用户充值金币
				//1、金币数量 [account_id 用户ID,gold_number 增加金币量]
				$gold_number = D('WxGoodsGoldDetails')->getGoldCoin( $orderData['gold_details_id'] );
				//2、发送通知进行充值
				
				$result = array(
					'change' => array(
						'diamond' => 0,
						'chip'	  => (int)$gold_number,
					),
					'event'  => "wx",
					'account_id' => (int)$orderData['account_id'],
					'extra'  => array(
						'out_trade_no' => $orderData['out_trade_no'],
						'total_fee'	   => $orderData['total_fee']
					),
				);

				$result_json = json_encode($result);

				$res = requestNodeMessage( C('JSON_ADD_GOLD_ADDR') ,$result_json);				

				$res = json_decode($res,true);
				if($res['err_code']==0){
					$gold_data = ['gold_status'=>1];
					$flag = $this->where($map)->setField($gold_data);
					if($flag){

						//进行模板通知
						$this->sendTemplateNotice($result,$orderData['openid']);
					}
				}
			}
			
			return 'ok';
		}


		//保存用户留言
		public function saveGuestBook( $post ){
			$map['out_trade_no'] = array('eq',$post['out_trade_no']);
			$this->where($map)->setField('msg',$post['msg']);
		}		


		//进行发送模板通知
		public function sendTemplateNotice( $result,$openid ){
			
			Vendor('WxpayAPI.lib.WxPay#Api');

			//获取基础access_token
	        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.\WxPayConfig::APPID.'&secret='.\WxPayConfig::APPSECRET;
	        $access_token_data = curlGetRequest($url);
	        $access_token = json_decode($access_token_data,true)['access_token'];

	        //设置基础摸板
	        //$url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$access_token;
	        //$data = json_encode(['industry_id1'=>1,'industry_id2'=>4]);
	        //$res = requestNodeMessage($url,$data);
	        //file_put_contents('data.log',$res);
	        //var_dump($res);

	        //获得模板ID
	        //$url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$access_token;
	        //$data = json_encode(['template_id_short'=>TM00015]);
	        //$res = requestNodeMessage($url,$data);
	        //var_dump($res);

	        //获取模板详细内容
	        //$url = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$access_token;
	        //$res = curlGetRequest($url);
	        //var_dump($res);


	        //发送模板消息--3klMN3RLA0IXDMGLk3ANUlznJMb37zneIiymK6TPhYs
	        $data = array(
	        	"touser" => $openid,
	        	"template_id" => "3klMN3RLA0IXDMGLk3ANUlznJMb37zneIiymK6TPhYs",
	        	"url"  => "",
	        	"data" => [
	        		"first" => [
	        			"value" => "鲨鱼牌手金币充值",
	        			"color" => "#173177"
	        		],
	        		"orderMoneySum" => [
	        			"value" => sprintf('%.2f',$result['extra']['total_fee']/100).'元',
	        			"color" => "#173177"
	        		],
	        		"orderProductName" => [
	        			"value" => '金币'.$result['change']['chip'],
	        			"color" => "#173177"
	        		],
	        		"Remark" => [
	        			"value" => "已向账号ID".$result['account_id']."充值金币成功",
                       	"color" => "#006400"
	        		]
	        	]
	        );
 	
	        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
	        requestNodeMessage($url,json_encode($data));
		}



	}