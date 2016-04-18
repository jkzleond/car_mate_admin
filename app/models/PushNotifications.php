<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-2
 * Time: 下午10:33
 */

class PushNotifications extends ModelEx {

	/**
	 * 添加推送信息
	 * @param int $type_id
	 * @param string $user_id
	 * @param string $client
	 * @param string $contents
	 * @param string $title
	 * @param int $count
	 */
    public static function addPushMessage($type_id=null, $user_id=null, $client=null, $contents=null, $title=null, $count=null)
    {
        $sql = <<<SQL
        insert into ReadyPush(
		typeId,
		userId,
		client,
		isRead,
		addTime,
		contents,
		title,
		[count]
		)values(
		:type_id,
		:user_id,
		:client,
		0,
		getdate(),
		:contents,
		:title,
		:count
		)
SQL;
        $bind = array(
            'type_id' => $type_id,
            'user_id' => $user_id,
            'client' => $client,
            'contents' => $contents,
            'title' => $title,
            'count' => $count
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加推送信息(按用户条件批量推送)
     * @param array      $criteria
     * @param array|null $user_criteria
     */
    public static function addPushMessageBatchWithCondition(array $criteria, array $user_criteria=null)
    {
    	$msg_crt = new Criteria($criteria);
    	$user_crt = new Criteria($user_criteria);

    	$user_condition_arr = array();
    	$user_condition_str = '';
    	$bind = array();

    	if($user_crt->user_id)
		{
			$user_condition_arr[] = 'userid = :user_id';
			$bind['user_id'] = $user_crt->user_id;
		}

		if($user_crt->user_name)
		{
			$user_condition_arr[] = 'uname = :user_name';
			$bind['user_name'] = $user_crt->user_name;
		}

		if($user_crt->phone)
		{
			$user_condition_arr[] = 'phone = :phone';
			$bind['phone'] = $user_crt->phone;
		}

		if($user_crt->client_type)
		{
			$user_condition_arr[] = 'clientType = :client_type';
			$bind['client_type'] = $crt->client_type;
		}

		if($user_crt->login_start_date)
		{
			$user_condition_arr[] = 'datediff(dd, :login_start_date, lastLoginTime) >= 0';
			$bind['login_start_date'] = $crt->login_start_date;
		}

		if($user_crt->login_end_date)
		{
			$user_condition_arr[] = 'datediff(dd, lastLoginTime, :login_end_date) >= 0';
			$bind['login_end_date'] = $crt->login_end_date;
		}

		if(!empty($user_condition_arr))
		{
			$user_condition_str = 'where '.implode(' and ', $user_condition_arr);
		}

    	$sql = <<<SQL
    	insert into ReadyPush(typeId, userId, client, isRead, addTime, title, contents, [count])
    	select :type_id as typeId, userId, clientType as client, :is_read as isRead, getdate() as addTime, 
    	:title as title, :content as contents, :count as [count] 
    	from IAM_USER
    	$user_condition_str
SQL;
		$bind['type_id'] = $msg_crt->type_id ? $msg_crt->type_id : 0;
		$bind['is_read'] = $msg_crt->is_read;
		$bind['title'] = empty($msg_crt->content) ? $msg_crt->title.'...' : $msg_crt->title;
		$bind['content'] = $msg_crt->content;
		$bind['count'] = 0;

		return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加系统消息(按用户条件批量添加)
     * @param array  $criteria
     * @param [type] $user_criteria
     */
    public static function addSysMessageBatchWithCondition(array $criteria, $user_criteria=null)
    {
    	$crt = new Criteria($criteria);
    	$user_crt = new Criteria($user_criteria);

    	$user_condition_arr = array();
    	$user_condition_str = '';
    	$add_push_user_bind = array();

		if($user_crt->user_id)
		{
			$user_condition_arr[] = 'userid = :user_id';
			$add_push_user_bind['user_id'] = $user_crt->user_id;
		}

		if($user_crt->user_name)
		{
			$user_condition_arr[] = 'uname = :user_name';
			$add_push_user_bind['user_name'] = $user_crt->user_name;
		}

		if($user_crt->phone)
		{
			$user_condition_arr[] = 'phone = :phone';
			$add_push_user_bind['phone'] = $user_crt->phone;
		}

		if($user_crt->client_type)
		{
			$user_condition_arr[] = 'clientType = :client_type';
			$add_push_user_bind['client_type'] = $crt->client_type;
		}

		if($user_crt->login_start_date)
		{
			$user_condition_arr[] = 'datediff(dd, :login_start_date, lastLoginTime) >= 0';
			$add_push_user_bind['login_start_date'] = $crt->login_start_date;
		}

		if($user_crt->login_end_date)
		{
			$user_condition_arr[] = 'datediff(dd, lastLoginTime, :login_end_date) >= 0';
			$add_push_user_bind['login_end_date'] = $crt->login_end_date;
		}

		if(!empty($user_condition_arr))
		{
			$user_condition_str = 'where '.implode(' and ', $user_condition_arr);
		}
    	
    	$connection = self::_getConnection();
    	$connection->begin();

    	$add_push_msg_sql = 'insert into PushList (title, contents, addTime, type) values (:title, :content, getdate(), :msg_type)';
    	$add_push_msg_bind = array(
    		'title' => $crt->title,
    		'content' => $crt->content,
    		'msg_type' =>  ($crt->msg_type or $crt->msg_type === 0 or $crt->msg_type === '0') ? $crt->msg_type : 3 //默认系统消息
    	);

    	$add_push_msg_success = self::nativeExecute($add_push_msg_sql, $add_push_msg_bind);

    	if(!$add_push_msg_success)
    	{
    		$connection->rollback();
    		return false;
    	}

    	$new_msg_id = $connection->lastInsertId();

    	$add_push_user_sql = <<<SQL
    	insert into PushUserList (pushId, userId, client, isRead, addTime)
    	select :push_id as pushId, userid, clientType as client, :is_read as isRead, getdate() as addTime
    	from IAM_USER
    	$user_condition_str
SQL;
		$add_push_user_bind['push_id'] = $new_msg_id;
		$add_push_user_bind['is_read'] = 0;

		$add_push_user_success = self::nativeExecute($add_push_user_sql, $add_push_user_bind);
    	
    	if(!$add_push_user_success)
    	{
    		$connection->rollback();
    		return false;
    	}

    	return $connection->commit();
    }

}