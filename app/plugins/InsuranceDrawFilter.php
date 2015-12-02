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
			select i.id as id, i.actulAmount as actul_amount, r.afterDiscountCompusoryInsurance as compusory, r.afterDiscountThird as third, r.afterDiscountDriver as driver, r.afterDiscountPassenger as passenger from Insurance_Info i
			left join Insurance_FinalResult r on r.id = i.finalResult_id
			where i.id in (
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
			$avg_compusory = 0;
			$avg_third = 0;
			$avg_seet = 0; //座位险平均值(包含司机和乘客)
			$total_amount = 0;
			$total_compusory = 0;
			$total_third = 0;
			$total_seet = 0;
			$total = 0;
			$info_ids = '';
			foreach($top20_insurance_info_list as $top20_insurance_info)
			{
				$total++;
				$total_amount += $top20_insurance_info['actul_amount'];
				$total_compusory += $top20_insurance_info['compusory'];
				$total_third += $top20_insurance_info['third'];
				$total_seet += $top20_insurance_info['driver'] + $top_insurance_info['passenger'];
				$info_ids += $top20_insurance_info['id'] + ',';
			}
			$info_ids = rtrim($info_ids, ',');
			$avg_amount = number_format($total_amount/$total,2);
			$avg_compusory = number_format($total_compusory/$total,2);
			$avg_third = number_format($total_third/$total, 2);
			$avg_seet = number_format($total_seet/$total, 2);
			
			//更新标记为已计算
			$update_mark_sql = <<<SQL
			update InsuranceInfoToMarkTmp set mark = 1 where id in ($info_ids)
SQL;
			$db->execute($update_mark_sql);

			//添加奖品
			
			$add_award_params = array();
			$add_award_bind = array(
				'num' => 1,
				'rate' => 1,
				'aid' => 228,
				'day_limit' => 1
			);
			$add_award_sql = '';
			
			if(!(float)$avg_compusory != 0)
			{
				$add_award_params[] = '(:compusory_name, :num, :rate, :aid, :day_limit, :compusory_value)';
				$add_award_bind['compusory_name'] = '交强险免' + $avg_compusory + '元';
				$add_award_bind['compusory_value'] = $avg_compusory;
			}
			
			if(!(float)$avg_third != 0)
			{
				$add_award_params[] = '(:third_name, :num, :rate, :aid, :day_limit, :third_value)';
				$add_award_bind['third_name'] = '三者险免' + $avg_third + '元';
				$add_award_bind['thrid_value'] = $avg_third;
			}
			
			if(!(float)$avg_seet != 0)
			{
				$add_award_params[] = '(:seet_name, :num, :rate, :aid, :day_limit, :seet_value)';
				$add_award_bind['seet_name'] = '座位险免' + $avg_seet + '元';
				$add_award_bind['seet_value'] = $avg_seet;
			}
			
			if(!empty($add_award_params))
			{
				$add_award_params_str = implode(', ', $add_award_params);
				$add_award_sql = 'insert into Award (name, num, rate, aid, pic, dayLimit, value) values '.$add_award_param_str;
				$db->execute($add_award_sql, $add_award_bind);
			}
			
		}
	}
}
