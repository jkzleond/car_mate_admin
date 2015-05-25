<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-11
 * Time: 上午10:19
 */

class AppException extends ModelEx
{
    /**
     * 获取app异常列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getExceptionList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crit = new Criteria($criteria);

        $cte_condition = array();
        $cte_condition_str = '';
        $page_condition = '';

        $bind = array();

        if($crit->province_id)
        {
            $cte_condition[] = 'u.provinceId = :province_id';
            $bind['province_id'] = $crit->province_id;
        }

        if($crit->user_id)
        {
            $cte_condition[] = 'u.USERID like :user_id';
            $bind['user_id'] = '%'.$crit->user_id.'%';
        }

        if($crit->uname)
        {
            $cte_condition[] = 'u.UNAME like :uname';
            $bind['uname'] = '%'.$crit->uname.'%';
        }

        if($crit->phone)
        {
            $cte_condition[] = 'u.PHONE like :phone';
            $bind['phone'] = '%'.$crit->phone.'%';
        }

        if(!empty($cte_condition))
        {
            $cte_condition_str = 'where '.implode(' or ', $cte_condition);
        }

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH FEEDBACK_CTE AS (
            SELECT ROW_NUMBER() OVER (ORDER BY e.id desc) AS rownum,
            e.id id,
            e.userId userId,
            u.UNAME uName,
            e.content,
            e.occurTime,
            u.provinceId,
            p.name provinceName
            FROM dbo.AppException e
            left join IAM_USER u on e.userId = u.USERID
            left join Province p on p.id = u.provinceId
            %s
		)
        select id,userId,uName,content,occurTime,provinceId,provinceName from FEEDBACK_CTE
        %s
        order by id desc
SQL;
        $sql = sprintf($sql, $cte_condition_str, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取app异常总数
     * @param array|null $criteria
     * @return mixed
     */
    public static function getExceptionCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition = array();
        $condition_str = '';

        $bind = array();

        if($crt->province_id)
        {
            $condition[] = 'u.provinceId = :province_id';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->user_id)
        {
            $condition[] = 'u.USERID like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->uname)
        {
            $condition[] = 'u.UNAME like :uname';
            $bind['uname'] = '%'.$crt->uname.'%';
        }

        if($crt->phone)
        {
            $condition[] = 'u.PHONE like :phone';
            $bind['phone'] = '%'.$crt->phone.'%';
        }

        if(!empty($condition))
        {
            $condition_str = 'where '.implode(' or ', $condition);
        }

        $sql = 'select count(*) from dbo.AppException e left join dbo.IAM_USER u on u.USERID = e.userId %s';

        $sql = sprintf($sql, $condition_str);

        $result = self::nativeQuery($sql, $bind, null, \Phalcon\Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 删除异常信息
     * @param $id
     * @return bool
     */
    public static function delException($id)
    {
        $sql = 'delete from dbo.AppException where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }
}