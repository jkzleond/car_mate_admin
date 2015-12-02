<?php
use \Phalcon\Mvc\User\Plugin;
use \Phalcon\Db;

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
			select top 1 p_user_id from ActivityUser where user_id = :user_id and aid = :aid
SQL;
			$get_involve_bind = array(
				'user_id' => $user_id,
				'aid' => 228
			);

			$get_involve_result = $this->db->query($get_involve_sql, $get_involve_bind);
			$get_involve_result->setFetchMode(Db::FETCH_ASSOC);
			$involve_info = $get_involve_result->fetch();

			//没有参与活动
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

			$get_insurance_info_mark_sql = <<<SQL
			select top 1 from InsuranceInfoToMarkTmp where insurance_info_id = :insurance_info_id and mark = 1
SQL;
			$get_insurance_info_mark_bind = array(
				'insurance_info_id' => $results['id']
			);

			$insurance_info_mark_result = $db->query($get_insurance_info_mark_sql, $get_insurance_info_mark_bind);
			$insurance_info_mark_result->setFetchMode(Db::FETCH_ASSOC);
			$insurance_info_mark = $insurance_info_mark_result->fetch();

			//计算过的保险信息则直接返回
			if(!empty($insurance_info_mark))
			{
				return;
			}

			//记录到临时表
			$insert_insurance_info_mark_sql = <<<SQL
			insert into InsuranceInfoToMarkTmp (insurance_info_id) values (:insurance_info_id)
SQL;
			$insert_insurance_info_mark_bind = array(
				'insurance_info_id' => $result['id']
			);

			$insert_insurance_info_mark_success = $db->execute($insert_insurance_info_mark_sql, $insert_insurance_info_mark_bind);

			/*
				计算奖金(每20个参与了活动的用户出单就计算平均值)
				取出没参与过计算的20条保险信息的实际出单价
			*/
			$get_top20_insurance_info_sql = <<<SQL
			select id, actulAmount as actul_amount from Insurance_Info
			where id in (
				select top 20 insurance_info_id from InsuranceInfoToMarkTmp where mark = 0
			)
SQL;
			$get_top20_insurance_info_result = $db->query($get_top20_insurance_info_sql);
			$get_top20_insurance_info_result->setFetchMode(Db::FETCH_ASSOC);
			$top20_insurance_info_list = $get_top20_insurance_info_result->fetchAll();

			//没有20条则直接返回
			if(count($top20_insurance_info_list) != 20)
			{
				return;
			}

			$avg_amount = 0;
			$total_amount = 0;
			$total = 0;
			$info_ids = '';
			foreach($top20_insurance_info_list as $top20_insurance_info)
			{
				$total++;
				$total_amount += $top20_insurance_info['actul_amount'];
				$info_ids += $top20_insurance_info['id'] + ',';
			}
			$info_ids = rtrim($info_ids, ',');
			$avg_amount = number_format($total_amount/$total,2);

			//更新标记为已计算
			$update_mark_sql = <<<SQL
			update InsuranceInfoToMarkTmp set mark = 1 where id in ($info_ids)
SQL;
			$db->execute($update_mark_sql);

			//添加奖品
			$add_award_sql = <<<SQL
			insert Award 
SQL;


		}
	}
}
