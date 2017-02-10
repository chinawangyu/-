<?php 
	
	namespace Home\Model;

	class WxGoodsModel extends \Think\Model{

		public function getGoodsDetails( $goods_id ){
			$map['id'] = $goods_id;
			$data = $this->field('id,goods_img,goods_details')->where($map)->find();
			return $data;
		}

	}