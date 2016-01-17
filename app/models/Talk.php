<?php

use \Phalcon\Db;

class Talk extends ModelEx
{
	/**
	 * 获取车友互动列表
	 * @param  array|null $criteria 
	 * @param  integer    $page_num 
	 * @param  integer    $page_size
	 * @param  string     $order_by 
	 * @return array              
	 */
	public static function getTalkList(array $criteria=null, $page_num=1, $page_size=10, $order_by = 'publishTime desc')
	{
		$crt = new Criteria($criteria);
		$cte_condition = array('boardId = 1');
		$cte_condition_str = '';
		$page_condition_str = '';
		$bind = array();

		if($crt->province_id)
		{
			$cte_condition[] = 'provinceId = :province_id';
			$bind['province_id'] = $crt->province_id;
		}

		if($crt->user_id)
		{
			$cte_condition[] = 'userId = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->contents)
		{
			$cte_condition[] = 'contents like :contents';
			$bind['contents'] = '%'.$crt->contents.'%';
		}

		if($crt->start_publish_time)
		{
			$cte_condition[] = 'datediff(d, :start_publish_time, publishTime) > 0';
			$bind['start_publish_time'] = $crt->start_publish_time;
		}

		if($crt->end_publish_time)
		{
			$cte_codnition[] = 'datediff(d, publishTime, :end_publish_time) > 0';
			$bind['start_publish_time'] = $crt->end_publish_time;
		}

		if(!empty($cte_condition))
		{
			$cte_condition_str = 'where '.implode(' and ', $cte_condition);
		}

		if($page_num)
		{
			$page_condition_str = 'where rownum between :from and :to';
			$bind['from'] = ($page_num - 1) * $page_size + 1;
			$bind['to'] = $page_num * $page_size;
		}

		$sql = <<<SQL
		with TALK_CTE as (
			select *, ROW_NUMBER() over ( order by $order_by ) as rownum from Talk
			$cte_condition_str
		)
		select t.id, t.userId as user_id, t.title, t.contents, t.pic, t.voice, convert(varchar(20), t.publishTime, 20) as publish_time, t.isState as state, t.noReply as no_reply, p.name as province_name, u.nickname as nick_name
		from TALK_CTE t
		left join IAM_USER u on u.userId = t.userId
		left join Province p on p.id = t.provinceId
		$page_condition_str
SQL;
		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取车友互动总数
	 * @param  array|null $criteria
	 * @return int
	 */
	public static function getTalkCount(array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$condition = array('boardId = 1');
		$condition_str = '';
		$bind = array();

		if($crt->province_id)
		{
			$condition[] = 'provinceId = :province_id';
			$bind['province_id'] = $crt->province_id;
		}

		if($crt->user_id)
		{
			$condition[] = 'userId = :user_id';
			$bind['user_id'] = $crt->user_id;
		}

		if($crt->contents)
		{
			$condition[] = 'contents like :contents';
			$bind['contents'] = '%'.$crt->contents.'%';
		}

		if($crt->start_publish_time)
		{
			$condition[] = 'datediff(d, :start_publish_time, publishTime) > 0';
			$bind['start_publish_time'] = $crt->start_publish_time;
		}

		if($crt->end_publish_time)
		{
			$codnition[] = 'datediff(d, publishTime, :end_publish_time) > 0';
			$bind['start_publish_time'] = $crt->end_publish_time;
		}

		if(!empty($condition))
		{
			$condition_str = 'where '.implode(' and ', $condition);
		}

		$sql = <<<SQL
		select count(1) from Talk
		$condition_str
SQL;
		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result[0];
	}

	/**
	 * 获取车友互动图片base64数据
	 * @param  int|string $id
	 * @return string
	 */
	public static function getTalkPicById($id)
	{
		$sql = 'select pic from Talk where id = :id';
		$bind = array('id' => $id);

		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result ? $result[0] : null;
	}

	/**
	 * 删除指定ID好友互动
	 * @param  int|string $id
	 * @return bool
	 */
	public static function deleteTalkById($id)
	{
		$sql = 'delete from Talk where id = :id';
		$bind['id'] = $id;
		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 更新好友互动
	 * @param  int|string $id
	 * @param  array|null $criteria
	 * @return bool
	 */
	public static function updateTalkById($id, array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$field_arr = array();
		$field_str = '';
		$bind = array('id' => $id);

		if($crt->contents)
		{
			$field_arr[] = 'contents = :contents';
			$bind['contents'] = $crt->contents;
		}

		if($crt->state or $crt->state === 0 or $crt->state === '0')
		{
			$field_arr[] = 'isState = :state';
			$bind['state'] = $crt->state;
		}

		if($crt->no_reply or $crt->no_reply === 0 or $crt->no_reply === '0')
		{
			$field_arr[] = 'noReply = :no_reply';
			$bind['no_reply'] = $crt->no_reply;
		}

		if(!empty($field_arr))
		{
			$field_str = implode(', ', $field_arr);
		}

		$sql = <<<SQL
		update Talk set $field_str where id = :id
SQL;

		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 获取指定ID互动的回复列表
	 * @param  int|string  $pid
	 * @param  int $page_num
	 * @param  int $page_size
	 * @return bool
	 */
	public static function getTalkReplyByPid($pid, $page_num=1, $page_size=10)
	{
		$page_condition_str = '';
		$bind = array('pid' => $pid);

		if($page_num)
		{
			$page_condition_str = 'where rownum between :from and :to';
			$bind['from'] = ($page_num - 1) * $page_size + 1;
			$bind['to'] = $page_num * $page_size;
		}

		$sql = <<<SQL
		with REPLY_CTE as (
			select *, row_number() over (order by commentTime desc) as rownum from TalkReply
			where pid = :pid
		)
		select r.id, r.userId as user_id, r.title, r.comment, r.voice, r.pic, convert(varchar(20), r.commentTime, 20) as comment_time, r.isState as state
		from REPLY_CTE r
		$page_condition_str
SQL;
		
		return self::nativeQuery($sql, $bind);
	}

	/**
	 * 获取指定ID车友互动回总数
	 * @param  int|string  $pid
	 * @param  integer $page_num
	 * @param  integer $page_size
	 * @return int
	 */
	public static function getTalkReplyCountByPid($pid)
	{
		$bind = array('pid' => $pid);
		$sql = 'select count(1) from TalkReply where pid = :pid';
		$result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
		return $result[0];
	}

	/**
	 * 添加回复
	 * @param int|string $talk_id
	 * @param string $contents
	 */
	public static function addReply($talk_id, array $criteria=null)
	{
		$crt = new Criteria($criteria);
		$sql = 'insert into TalkReply (pid, userId, title, comment, commentTime, isState, push) values (:pid, :user_id, :title, :comment, getdate(), 1, 1)';
		$bind = array(
			'pid' => $talk_id,
			'user_id' => $crt->user_id,
			'title' => $crt->title, 
			'comment' => $crt->comment
		);
		//echo $sql.PHP_EOL;var_dump($bind);exit;
		return self::nativeExecute($sql, $bind);
	}

	/**
	 * 删除指定ID回复
	 * @param  int|string $id
	 * @return bool
	 */
	public static function deleteTalkReplyById($id)
	{
		$sql = 'delete from TalkReply where id = :id';
		$bind['id'] = $id;
		return self::nativeExecute($sql, $bind);
	}
}