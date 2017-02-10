<?php 

	namespace Home\Model;

	class WxGoodsGoldDetailsModel extends \Think\Model{

		public function getGoodsDetailsModel( $goods_id ){

			$map['goods_id'] = $goods_id;
			return $this->where($map)->select();
		}

		//获取当前商品型号单价
		public function getCurrentGoodsDetailsPrice( $details_id ){

			$map['id'] = $details_id;
			return $this->field('gold_price,gold_number,gold_show')->where($map)->find();
		}

		//获取充值的金币数量
		public function getGoldCoin( $details_id ){
			$map['id'] = $details_id;
			return $this->where($map)->getField('gold_number');
		}


	}