<?php
class CarInfo extends ModelEx
{
	/**
	 * 更新指定ID车辆信息
	 * @param             $info_id
	 * @param  array|null $criteria
	 * @return 
	 */
	public static function updateCarInfoById($info_id, array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$field_str = '';
		$bind = array('info_id' => $info_id);

		if($crt->engine_no)
		{
			$field_str .= 'engineNumber = :engine_no, ';
			$bind['engine_no'] = $crt->engine_no;
		}

		if($crt->frame_no)
		{
			$field_str .= 'frameNumber = :frame_no, ';
			$bind['frame_no'] = $crt->frame_no;
		}

		$field_str = rtrim($field_str, ', ');

		$sql = "update CarInfo set $field_str where id = :info_id";

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 更新符合指定条件的车辆信息
	 * @param  array  $condition
	 * @param  array  $data
	 * @return bool
	 */
	public static function updateCarInfoWithCondition(array $condition, array $data)
	{
		$condition_crt = new Criteria($condition);
		$condition_arr = array();
		$condition_str = '';

		$data_crt = new Criteria($data);
		$field_str = '';

		$bind = array();

		if($condition_crt->user_id)
		{
			$condition_arr[] = 'userId = :user_id_cond';
			$bind['user_id_cond'] = $condition_crt->user_id;
		}

		if($condition_crt->hphm)
		{
			$condition_arr[] = 'hphm = :hphm_cond';
			$bind['hphm_cond'] = $condition_crt->hphm;
		}

		if($condition_crt->engine_no)
		{
			$condition_arr[] = '(engineNumber = :engine_no_cond or engineNumber is null)';
			$bind['engine_no_cond'] = $condition_crt->engine_no;
		}

		if(!empty($condition_arr))
		{
			$condition_str = 'where '.implode(' and ', $condition_arr);
		}

		if($data_crt->engine_no)
		{
			$field_str .= 'engineNumber = :engine_no, ';
			$bind['engine_no'] = $data_crt->engine_no;
		}

		if($data_crt->frame_no)
		{
			$field_str .= 'frameNumber = :frame_no, ';
			$bind['frame_no'] = $data_crt->frame_no;
		}

		$field_str = rtrim($field_str, ', ');

		$sql = "update CarInfo set $field_str $condition_str";
		
		return self::nativeExecute($sql, $bind);
	}
}