<?php
use \Phalcon\Db;

class PrivateMessage extends ModelEx
{
	/**
	 * 选择符合条件的用户发私信
	 * @param array $criteria
	 * @param array $user_criteria
	 */
	public static function addMessageBatchWithCondition(array $criteria=null, array $user_criteria=null)
	{
		$msg_crt = new Criteria($criteria);

		$user_list = User::getUserList($user_criteria);

		$get_pid_sql = <<<SQL
		select top 1 id from PrivateUserList where (userId = :user_id and reuserId = :reuser_id)
SQL;
		$get_pid_bind = array(
			'user_id' => $msg_crt->user_id
		);

		$add_pid_sql = <<<SQL
		insert into PrivateUserList (userId, reuserId) values (:user_id, :reuser_id)
SQL;
		$add_pid_bind = array(
			'user_id' => $msg_crt->user_id
		);

		$add_msg_sql = <<<SQL
		insert into PrivateMessage (pId,userId,reuserId,contents,voice,voicelen,pic,clientType) values (:pid,:user_id,:reuser_id,:contents,:voice,:voicelen,:pic,:client_type)
SQL;
		$add_msg_bind = array(
			'user_id' => $msg_crt->user_id,
			'contents' => $msg_crt->content,
			'voice' => $msg_crt->voice,
			'voicelen' => $msg_crt->voicelen,
			'pic' => $msg_crt->pic,
			'client_type' => 'Android'
		);

		$connection = self::_getConnection();
		$connection->begin();

		$pid = null;

		foreach($user_list as $user)
		{
				
			$get_pid_bind['reuser_id'] = $user['user_id'];
			
			$pid_result = self::fetchOne($get_pid_sql, $get_pid_bind, null, Db::FETCH_ASSOC);
			
			if(!empty($pid_result))
			{
				$pid = $pid_result['id'];
			}
			else
			{
				$add_pid_bind['reuser_id'] = $user['user_id']; 
				self::nativeExecute($add_pid_sql, $add_pid_bind);
				$pid = $connection->lastInsertId();
				if(!$pid)
				{
					$connection->rollback();
					return false;
				}
			}
			
			$add_msg_bind['pid'] = $pid;
			$add_msg_bind['reuser_id'] = $user['user_id'];
			$add_msg_success = self::nativeExecute($add_msg_sql, $add_msg_bind);
			
			if(!$add_msg_success)
			{
				$connection->rollback();
				return false;
			}
		}

		return $connection->commit();
	}
}