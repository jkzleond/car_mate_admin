<?php
use \Phalcon\Mvc\User\Plugin;

class InsuranceDrawFilter extends Plugin
{
	public function afterIssuing($insurance_controller, $results)
	{
		if($results['success'])
		{
			$user_id = $results['user_id'];
			$db = $this->db;

			//获取保险20免一活动参与用户的上家
			$get_involve_sql = <<<SQL
			select top 1 p_user_id from Hui_UserToPuser where user_id = :user_id and aid = :aid
SQL;
			$get_involve_bind = array(
				'user_id' => $user_id,
				'aid' => 228
			);

			$get_involve_result = $this->db->query($get_involve_sql, $get_involve_bind);
			$involve_info = $get_involve_result->fetch();

			//没有参与
			if(empty($involve_info))
			{
				return;
			}

			$p_user_id = $involve_info['p_user_id'];
			//增加用户上家的抽奖次数
			$update_chance_sql = <<<SQL
			update AwardChance set chance = chance + 1 where userid = :user_id and aid = :aid
SQL;
			$update_chance_bind = array(
				'user_id' => $p_user_id,
				'aid' => 228
			);

			$update_success = $db->execute($update_chance_sql, $update_chance_sql);

			//计算奖金(每20个参与了活动的用户出单就计算平均值)
			
		}
	}	
}