<?php
namespace Home\Controller;
use Think\Controller;
class GoldController extends Controller {

    public function index(){

        D('WxOrderList')->sendTemplateNotice([]);exit;

        $goods_id = 1;
        $goods = D('Wx_goods')->getGoodsDetails( $goods_id );

        //获取型号
        $goods_details_model = D('WxGoodsGoldDetails')->getGoodsDetailsModel( $goods_id );

     	Vendor('WxpayAPI.lib.WxPay#Api');
     	Vendor('WxpayAPI.example.WxPay#JsApiPay');

     	//获取用户openID
     	$tools = new \JsApiPay();
	    $result = $tools->GetOpenid();

        if(empty($result['openid'])){
            redirect(U('Gold/index'));
        }


        $this->assign('goods_details_model',$goods_details_model);
        $this->assign('goods',$goods);
	    $this->assign('openId',$result['openid']);
        $this->assign('nickname',$result['nickname']);
    	$this->display();
    }


    //ajax请求---统一下单接口
    public function UnifiedOrder(){

        //如果用户ID或openid为空,返回失败
        if(empty($_POST['account_id'])||empty($_GET['openid'])){
            echo json_encode(['code'=>400,'msg'=>'参数错误,可关闭页面重新进入']);
            exit;
        }


	    Vendor('WxpayAPI.lib.WxPay#Api');
        Vendor('WxpayAPI.example.WxPay#JsApiPay');

	    $openId   = $_GET['openid'];
        //进行订单保存--
        $data['wx_nickname'] = empty($_POST['nickname'])?'':$_POST['nickname'];
        $data['gold_details_id'] = $_POST['goods_details_id'];
        $data['account_id'] = $_POST['account_id'];

        $data['out_trade_no'] = \WxPayConfig::MCHID.date("YmdHis").mt_rand(0,100);
        $data['openid']  = $openId;

        //返回应支付金额
        $total_fee = D('WxOrderList')->addOrderData($data); 
        //--do code



	    $tools = new \JsApiPay();	
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("鲨鱼牌手充值");
        $input->SetAttach("鲨鱼牌手充值");
        $input->SetOut_trade_no( $data['out_trade_no'] );
        $input->SetTotal_fee( $total_fee );
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url( U('Gold/notify','',true,true) );
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

	    echo json_encode( ['jsapi'=>$jsApiParameters,'out_trade_no'=>$data['out_trade_no']] );
    }



   //支付信息回调地址
   public function notify(){

  
	    $msg = $GLOBALS['HTTP_RAW_POST_DATA'];
	    $msgXml = simplexml_load_string($msg);

	   //支付成功
        if(  strtoupper($msgXml->return_code)=='SUCCESS' && strtoupper($msgXml->result_code)=='SUCCESS' ) {
            //传入订单号
            D('WxOrderList')->setOrderDataSuccess( (String)$msgXml->out_trade_no );


            //回复消息
            $templete = '<xml>
                            <return_code><![CDATA[SUCCESS]]></return_code>
                            <return_msg><![CDATA[OK]]></return_msg>
                        </xml>';
            echo $templete; 
        }

   }


   //检测当前充值用户是否存在
   public function checkAccountId(){
        $account_id = I('get.account_id');

        $isExists = D('Account')->checkAccountIdIsExists( $account_id );
        //如果存在        
        if( $isExists ){
            $res = array('code'=>200,'msg'=>'存在');
        }else{
            $res = array('code'=>400,'msg'=>'不存在');
        }

        $this->ajaxReturn($res);
   }

   //用户留言
   public function guestBook(){
        D('WxOrderList')->saveGuestBook($_POST);
   }




}
