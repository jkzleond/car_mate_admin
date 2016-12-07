<?php

use \Phalcon\Db;

class DriverInfo extends ModelEx
{
	/**
	 * 获取驾驶员信息列表
	 * @param  array|null $criteria
	 * @param  null $page_num  
	 * @param  null $page_size 
	 * @return array                
	 */
	public static function getDriverInfoList(array $criteria=null, $page_num=null, $page_size=null)
	{
		$crt = new Criteria($criteria);
		$cte_condition_arr = array();
		$cte_condition_str = '';
		$page_condition_str = '';

		if($crt->user_id)
		{
			$cte_condition_arr[] = 'di.user_id = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->hphm)
		{
			$cte_condition_arr[] = 'di.hphm like :hphm';
			$bind['hphm'] = '%'.$crt->hphm.'%';
		}
		if($crt->phone)
		{
			$cte_condition_arr[] = 'u.phone like :phone';
			$bind['phone'] = '%'.$crt->phone.'%';
		}

		if($crt->user_name)
		{
			$cte_condition_arr[] = 'u.uname = :user_name';
			$bind['user_name'] = $crt->user_name;
		}

		if(!empty($cte_condition_arr))
		{
			$cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
		}

		if($page_num)
		{
			$page_condition_str = 'where rownum between :from and :to';
			$bind['from'] = $page_size * ($page_num - 1) + 1; // rownum从1开始
			$bind['to'] = $page_size * $page_num;
		}

		$sql = <<<SQL
		with DI_CTE as (
			select di.id, di.license_no, di.archieve_no as archive_no, di.user_id, di.hphm, isnull(c.engineNumber, di.fdjh) as engine_no, u.phone, u.uname as user_name, c.hpzl, c.frameNumber as frame_no,
			ROW_NUMBER() over( order by di.id asc) as rownum
			from DriverInfo di
			left join IAM_USER u on u.userId = di.user_id
			left join CarInfo c on c.userId = di.user_id and c.hphm = di.hphm
			$cte_condition_str
		)
		select * from DI_CTE
		$page_condition_str 
SQL;
		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取驾驶员信息总数
	 * @param   array|null $criteria 
	 * @return  mixed
	 */
	public static function getDriverInfoCount( array $criteria=null)
	{
		$crt = new Criteria($criteria);

		$condition_arr = array();
		$condition_str = '';
		$bind = array();

		if($crt->user_id)
		{
			$condition_arr[] = 'di.user_id = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->hphm)
		{
			$condition_arr[] = 'di.hphm like :hphm';
			$bind['hphm'] = '%'.$crt->hphm.'%';
		}
		if($crt->phone)
		{
			$condition_arr[] = 'u.phone like :phone';
			$bind['phone'] = '%'.$crt->phone.'%';
		}

		if($crt->user_name)
		{
			$condition_arr[] = 'u.uname = :user_name';
			$bind['user_name'] = $crt->user_name;
		}

		if(!empty($condition_arr))
		{
			$condition_str = 'where '.implode(' and ', $condition_arr);
		}

		$sql = "select count(di.id) from DriverInfo di left join IAM_USER u on u.userId = di.user_id $condition_str";

		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

		return $result[0];
	}

	/**
	 * 更新驾驶员信息
	 * @param  $info_id  
	 * @param  array  $criteria 
	 * @return bool
	 */
	public static function updateDriverInfo($info_id, array $criteria)
	{
		$crt = new Criteria($criteria);
		$field_str = '';
		$bind = array('info_id' => $info_id);

		if($crt->license_no)
		{
			$field_str .= 'license_no = :license_no, ';
			$bind['license_no'] = $crt->license_no;
		}

		if($crt->paper_id)
        {
            $field_str .= 'paper_id = :paper_id, ';
            $bind['paper_id'] = $crt->paper_id;
        }

		if($crt->archive_no)
		{
			$field_str .= 'archieve_no = :archive_no, ';
			$bind['archive_no'] = $crt->archive_no;
		}

		if($crt->engine_no)
		{
			$field_str .= 'fdjh = :engine_no, ';
			$bind['engine_no'] = $crt->engine_no;
		}

		$field_str = rtrim($field_str, ', ');

		$sql = "update DriverInfo set $field_str where id = :info_id";

		return self::nativeExecute($sql, $bind);
	}
}