<?php
class Content extends ModelEx
{
	/**
	 * 添加内容
	 * @param string $content
	 * @return int|bool last_insert_id
	 */
	public static function addContent($content)
	{
		$sql = 'insert into Content (content) values (:content)';
		$bind = array('content' => $content);

		$success = self::nativeExecute($sql, $bind);
		$connection = self::_getConnection();

		return $success ? $connection->lastInsertId() : false;
	}

	/**
	 * 更新内容
	 * @param  int|string $id
	 * @param  string $content
	 * @return bool
	 */
	public static function updateContent($id, $content)
	{
		$sql = 'update Content set content = :content where id = :id';
		$bind = array(
			'id' => $id,
			'content' => $content
		);

		return self::nativeExecute($sql, $bind);
	}
}