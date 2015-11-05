<?php

use \Phalcon\Db;

class User extends ModelEx
{	
	/**
	 * 获取用户数据列表
	 * @param  array|null $criteria
	 * @param  int|null     $page_num
	 * @param  int|null     $page_size
	 * @return array
	 */
	public static function getUserList(array $criteria=null, $page_num=null, $page_size=null)
	{
		$crt = new Criteria($criteria);

		$cte_condition_arr = array();
		$cte_condition_str = '';
		$page_condition_str = '';
		$bind = array();

		if($crt->user_id)
		{
			$cte_condition_arr[] = 'userid = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->user_name)
		{
			$cte_condition_arr[] = 'uname = :user_name';
			$bind['user_name'] = $crt->user_name;
		}

		if($crt->phone)
		{
			$cte_condition_arr[] = 'phone = :phone';
			$bind['phone'] = $crt->phone;
		}

		if($crt->client_type)
		{
			$cte_condition_arr[] = 'clientType = :client_type';
			$bind['client_type'] = $crt->client_type;
		}

		if($crt->login_start_date)
		{
			$cte_condition_arr[] = 'datediff(dd, :login_start_date, lastLoginTime) >= 0';
			$bind['login_start_date'] = $crt->login_start_date;
		}

		if($crt->login_end_date)
		{
			$cte_condition_arr[] = 'datediff(dd, lastLoginTime, :login_end_date) >= 0';
			$bind['login_end_date'] = $crt->login_end_date;
		}

		if(!empty($cte_condition_arr))
		{
			$cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
		}

		if($page_num)
		{
			$page_condition_str = 'where rownum between :from and :to';
			$bind['from'] = $page_size * ($page_num - 1) + 1;
			$bind['to'] = $page_size * $page_num;
		}

		$sql = <<<SQL
		with USER_CTE as (
			select
			id,userId as user_id, pwd, uName as user_name, nickName as nick_name,
			clientType as client_type, phone, iosToken as ios_token, status, convert(varchar(20), createDate, 20) as create_date, 
			convert(varchar(20), lastLoginTime, 20) as last_login_time, androidToken as android_token,
			row_number() over (order by createDate desc) as rownum
			from IAM_USER
			$cte_condition_str
		)
		select * from USER_CTE
		$page_condition_str
SQL;

		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取用户数据总数
	 * @param  array|null $criteria
	 * @return [type]
	 */
	public static function getUserCount(array $criteria=null)
	{
		$crt = new Criteria($criteria);

		$condition_arr = array();
		$condition_str = '';
		$bind = array();

		if($crt->user_id)
		{
			$condition_arr[] = 'userid = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->user_name)
		{
			$condition_arr[] = 'uname = :user_name';
			$bind['user_name'] = $crt->user_name;
		}

		if($crt->phone)
		{
			$condition_arr[] = 'phone = :phone';
			$bind['phone'] = $crt->phone;
		}

		if($crt->client_type)
		{
			$condition_arr[] = 'clientType = :client_type';
			$bind['client_type'] = $crt->client_type;
		}

		if($crt->login_start_date)
		{
			$condition_arr[] = 'datediff(dd, :login_start_date, lastLoginTime) >= 0';
			$bind['login_start_date'] = $crt->login_start_date;
		}

		if($crt->login_end_date)
		{
			$condition_arr[] = 'datediff(dd, lastLoginTime, :login_end_date) >= 0';
			$bind['login_end_date'] = $crt->login_end_date;
		}

		if(!empty($condition_arr))
		{
			$condition_str = 'where '.implode(' and ', $condition_arr);
		}

		$sql = <<<SQL
		select count(1) from IAM_USER
		$condition_str
SQL;
		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result[0];
	}

	/**
	 * 获取用户客户端列表
	 * @return array
	 */
	public static function getUserClientTypeList()
	{
		$sql = 'select clientType as client_type from IAM_USER where clientType is not null group by clientType order by clientType';

		return self::nativeQuery($sql);
	}
}