<?php

use \Phalcon\Db;

class SimpleMod extends ModelEx
{
	/**
	 * 获取简单模块列表
	 * @param  array|null $criteria
	 * @param  int     	  $page_num
	 * @param  int     	  $page_size
	 * @return array
	 */
	public static function getSimpleModList(array $criteria=null, $page_num=null, $page_size=null)
	{
		$crt = new Criteria($criteria);
		$cte_condition_str = '';
		$cte_condition_arr = array();
		$page_condition_str = '';
		$bind = array();

		if($crt->type)
		{
			$cte_condition_arr[] = 'type = :type';
			$bind['type'] = $crt->type;
		}

		if($crt->is_use)
		{
			$cte_condition_arr[] = 'is_use = :is_use';
			$bind['is_use'] = $crt->is_use;
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
		with SM_CTE as (
			select m.id, m.name, m.url, m.type, m.link_type, m.rel_id, m.is_use, m.display_order, m.content_id, c.content, row_number() over(order by m.display_order asc, m.id desc) as rownum 
			from SimpleMod m
			left join Content c on c.id = m.content_id
			$cte_condition_str
		)
		select * from SM_CTE
		$page_condition_str
SQL;
		//echo $sql; exit;
		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取简单模块总数
	 * @param  array|null $criteria
	 * @return int
	 */
	public static function getSimpleModCount(array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$condition_arr = array();
		$condition_str = '';
		$bind = array();

		if($crt->type)
		{
			$condition_arr[] = 'type = :type';
			$bind['type'] = $crt->type;
		}

		if($crt->is_use)
		{
			$condition_arr[] = 'is_use = :is_use';
			$bind['is_use'] = $crt->is_use;
		}

		if(!empty($condition_arr))
		{
			$condition_str = 'where '.implode(' and ', $condition_arr);
		}

		$sql = <<<SQL
		select count(1) from SimpleMod $condition_str
SQL;
		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result[0];
	}

	/**
	 * 添加简单模块
	 * @param array $data
	 * @return bool
	 */
	public static function addSimpleMod(array $data)
	{
		$crt = new Criteria($data);
		$bind = array(
			'name' => $crt->name,
			'link_type' => $crt->link_type,
			'rel_id' => $crt->rel_id,
			'url' => $crt->url,
			'type' => $crt->type,
			'img' => $crt->img,
			'content_id' => $crt->content_id
		);

		if($crt->content)
		{
			$content_id = Content::addContent($crt->content);
			if(!$content_id) return false;
			$bind['content_id'] = $content_id;
		}

		$sql = <<<SQL
		insert into SimpleMod (name, link_type, rel_id, url, type, img, content_id)
		values (:name, :link_type, :rel_id, :url, :type, :img, :content_id)
SQL;
		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 删除简单模块
	 * @param  int|string $id
	 * @return bool
	 */
	public static function delSimpleMod($id)
	{
		$sql = 'delete from SimpleMod where id = :id';
		$bind = array('id' => $id);

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 更新简单模块
	 * @param  [type] $id
	 * @param  array  $criteria
	 * @return [type]
	 */
	public static function updateSimpleMod($id, array $criteria)
	{
		$crt = new Criteria($criteria);
		$field_str = '';
		$bind = array('id' => $id);

		if($crt->name)
		{
			$field_str .= 'name = :name, ';
			$bind['name'] = $crt->name;
		}

		if($crt->link_type)
		{
			$field_str .= 'link_type = :link_type, ';
			$bind['link_type'] = $crt->link_type;
		}

		if($crt->rel_id)
		{
			$field_str .= 'rel_id = :rel_id, ';
			$bind['rel_id'] = $crt->rel_id;
		}

		if($crt->url)
		{
			$field_str .= 'url = :url, ';
			$bind['url'] = $crt->url;
		}

		if($crt->type)
		{
			$field_str .= 'type = :type, ';
			$bind['type'] = $crt->type;
		}

		if($crt->img)
		{
			$field_str .= 'img = :img, ';
			$bind['img'] = $crt->img;
		}

		if($crt->is_use or $crt->is_use === false or $crt->is_use === 0 or $crt->is_use === '0')
		{
			$field_str .= 'is_use = :is_use ,';
			$bind['is_use'] = $crt->is_use;
		}

		if($crt->display_order or $crt->display_order === 0 or $crt->display_order === '0')
		{
			$field_str .= 'display_order = :display_order ,';
			$bind['display_order'] = $crt->display_order;
		}

		if($crt->content)
		{
			if($crt->content_id)
			{
				$update_content_success = Content::updateContent($crt->content_id, $crt->content);
				if(!$update_content_success)
				{
					return false;
				}
			}
			else
			{
				$new_content_id = Content::addContent($crt->content);
				if(!$new_content_id)
				{
					return false;
				}

				$field_str .= 'content_id = :content_id, ';
				$bind['content_id'] = $new_content_id;
			}
		}

		$field_str = rtrim($field_str, ', ');

		$sql = "update SimpleMod set $field_str where id = :id";

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 获取指定ID简单模块的img数据(base64)
	 * @param  int|string $id
	 * @return string
	 */
	public static function getImageDataById($id)
	{
		$sql = 'select top 1 img from SimpleMod where id = :id';
		$bind = array('id' => $id);
		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result[0];
	}

}