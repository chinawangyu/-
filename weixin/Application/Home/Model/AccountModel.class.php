<?php 
	
	namespace Home\Model;

	class AccountModel extends \Think\Model{

		protected $dbName        = 'TexasPoker';
		protected $trueTableName = 'account';

		//检测当前输入的AccountId是否存在
		public function checkAccountIdIsExists( $account_id ){
			$map['account_id'] = $account_id;
			$num = $this->where($map)->count();
			
			if( $num>0 )
				return true;
			else
				return false;

		}

	}
