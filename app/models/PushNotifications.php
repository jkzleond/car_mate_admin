<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-2
 * Time: 下午10:33
 */

class PushNotifications extends ModelEx {

    public static function addFailurePushUser($type_id=null, $user_id=null, $client=null, $contents=null, $title=null, $count=null)
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

}