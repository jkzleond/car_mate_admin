<?php

use \Phalcon\Db;

class Field extends ModelEx
{
	/**
	 * 获取字段数据列表
	 * @param  array|null $criteria
	 * @param  int     	  $page_num
	 * @param  int     	  $page_size
	 * @return array
	 */
	public static function getFieldList(array $criteria=null, $page_num=null, $page_size=null)
	{
		$crt = new Criteria($criteria);
		$cte_condition_arr = array();
		$cte_condition_str = '';
		$page_condition_str = '';

		$bind = array();

		if($crt->name)
		{
			$cte_condition_arr[] = 'name like :name';
			$bind['name'] = '%'.$crt->name.'%';
		}

		if($crt->ename)
		{
			$cte_condition_arr[] = 'ename like :ename';
			$bind['ename'] = '%'.$crt->ename.'%';
		}

		if($crt->type)
		{
			$cte_condition_arr[] = 'type = :type';
			$bind['type'] = $crt->type;
		}

		if(!empty($cte_condition_arr))
		{	
			$cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
		}

		if($page_num)
		{
			$page_condition_str = 'where rownum between :from and :to';
			$bind['from'] = ($page_num - 1) * $page_size + 1;
			$bind['to'] = $page_num * $page_size;
		}

		$sql = <<<SQL
		with FIELD_CTE as (
			select id, name, ename, type, [values], [default], des, row_number() over( order by id) as rownum
			from Field
			$cte_condition_str
		)

		select * from FIELD_CTE
		$page_condition_str
SQL;
		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取字段数据总数
	 * @param  array|null $criteria
	 * @return mixed
	 */
	public static function getFieldCount(array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$condition_arr = array();
		$condition_str = '';

		$bind = array();

		if($crt->name)
		{
			$condition_arr[] = 'name like :name';
			$bind['name'] = '%'.$crt->name.'%';
		}

		if($crt->ename)
		{
			$condition_arr[] = 'ename like :ename';
			$bind['ename'] = '%'.$crt->ename.'%';
		}

		if($crt->type)
		{
			$condition_arr[] = 'type = :type';
			$bind['type'] = $crt->type;
		}

		if(!empty($condition_arr))
		{	
			$condition_str = 'where '.implode(' and ', $condition_arr);
		}

		$sql = "select count(1) from Field $condition_str";

		$result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);

		return $result[0];
	}

	/**
	 * 添加字段
	 * @param array|null $criteira
	 * @return bool
	 */
	public static function addField(array $criteria=null)
	{
		$crt = new Criteria($criteria);

		$sql = 'insert into Field (name, ename, type, [values], [default], des) values (:name, :ename, :type, :values, :default, :des)';

		$bind = array(
			'name' => $crt->name,
			'ename' => $crt->ename,
			'type' => $crt->type,
			'values' => $crt->values,
			'default' => $crt->default,
			'des' => $crt->des
		);

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 更新字段
	 * @param  int|string $id
	 * @param  array|null $criteira
	 * @return bool
	 */
	public static function updateField($id, array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$field_str = '';
		$bind = array('id' => $id);

		if($crt->name)
		{
			$field_str .= 'name = :name, ';
			$bind['name'] = $crt->name;
		}

		if($crt->ename)
		{	
			$field_str .= 'ename = :ename, ';
			$bind['ename'] = $crt->ename;
		}

		if($crt->type)
		{
			$field_str .= '[type] = :type, ';
			$bind['type'] = $crt->type;
		}

		if($crt->values)
		{
			$field_str .= '[values] = :values, ';
			$bind['values'] = $crt->values;
		}

		if($crt->default)
		{
			$field_str .= '[default] = :default, ';
			$bind['default'] = $crt->default;
		}

		if($crt->des)
		{
			$field_str .= 'des = :des, ';
			$bind['des'] = $crt->des;
		}

		if($field_str)
		{
			$field_str = rtrim($field_str, ', ');
		}
		else
		{
			return false;
		}

		$sql = "update Field set $field_str where id = :id";

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 删除字段
	 * @param  int|string $id
	 * @return bool
	 */
	public static function delField($id)
	{
		$sql = 'delete from Field where id = :id';
		$bind = array('id' => $id);

		return self::nativeExecute($sql, $bind);
	}
}