<?php

use \Phalcon\Db;
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-8-2
 * Time: 上午10:07
 */
class FeedBack extends ModelEx
{
    /**
     * 获取意见反馈列表数据
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getFeedBackList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'u.userid = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->uname)
        {
            $cte_condition_arr[] = 'u.uname = :uname';
            $bind['uname'] = $crt->uname;
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' or ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        with FB_CTE as (
          select ROW_NUMBER() OVER(order by f.createdate desc) as rownum,
          f.id,
          f.userid as user_id,
          f.contents,
          convert(varchar(20), f.createdate, 20) as create_date,
          u.uname,
          p.name as province,
          fr.replyContents as reply_contents
          from IAM_FEEDBACK f
          left join FeedbackReply fr on fr.feedid = f.id
          left join IAM_USER u on u.userid = f.userid
          left join Province p on p.id = u.provinceid
          $cte_condition_str
        )
        select * from FB_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind, null, Db::FETCH_ASSOC);
    }

    /**
     * 获取意见反馈总数
     * @param array|null $criteria
     * @return int
     */
    public static function getFeedBackCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $condition_arr = array();
        $condition_str = '';

        if($crt->user_id)
        {
            $condition_arr[] = 'u.userid = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->uname)
        {
            $condition_arr[] = 'u.uname = :uname';
            $bind['uname'] = $crt->uname;
        }

        if($crt->phone)
        {
            $condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' or ', $condition_arr);
        }

        $sql = <<<SQL
          select count(f.id)
          from IAM_FEEDBACK f
          left join IAM_USER u on u.userid = f.userid
          left join Province p on p.id = u.provinceid
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return (int) $result[0];
    }

    /**
     * 删除指定ID意见反馈
     * @param $id
     * @return bool
     */
    public static function deleteFeedBack($id)
    {
        $sql = 'delete from IAM_FEEDBACK where id = :id';
        $bind = array('id' => $id);
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 回复指定ID意见反馈
     * @param $feed_id
     * @param $content
     * @param $user_id
     * @return bool
     */
    public static function reply($feed_id, $content, $user_id)
    {
        $sql = 'insert into FeedbackReply (feedid, replycontents, replyuserid) values (:feed_id, :content, :user_id)';
        $bind = array(
            'feed_id' => $feed_id,
            'content' => $content,
            'user_id' => $user_id
        );
        return self::nativeExecute($sql, $bind);
    }
}