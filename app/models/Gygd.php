<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-9-29
 * Time: 上午10:10
 */

use \Phalcon\Db;

class Gygd extends ModelEx
{
    /**
     * 获取活动信息
     * @param $activity_id
     * @return object
     */
    public static function getActivityInfoById($activity_id)
    {
        $sql = <<<SQL
        select * from GYGD_Activity
        where id = :activity_id
SQL;
        $bind = array('activity_id' => $activity_id);

        return self::fetchOne($sql, $bind);
    }

    /**
     * 获取体育馆活动用户列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getStadiumActivityUserList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition_arr = array('au.activity_id = 1');
        $cte_condition_str = '';
        $page_condition_str = '';

        if ($crt->id_no)
        {
            $cte_condition_arr[] = 'u.id_no = :id_no';
            $bind['id_no'] = $crt->id_no;
        }

        if ($crt->user_name)
        {
            $cte_condition_arr[] = 'u.user_name = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if ($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if ($crt->state === 0 or $crt->state === '0')
        {
            //未中奖用户
            $cte_condition_arr[] = 'au.is_win = 0';
        }
        elseif ($crt->state == 1)
        {
            //中奖用户
            $cte_condition_arr[] = 'au.is_win = 1';
        }
        elseif ($crt->state == 2)
        {
            //已领取用户
            $cte_condition_arr[] = 'au.exchange_date is not null';
        }
        elseif ($crt->state == 3)
        {
            //未领取用户
            $cte_condition_arr[] = 'au.exchange_date is null and au.is_win = 1';
        }

        $order_by = 'order by au.draw_date desc';
        if ($crt->order_by)
        {
            if ($crt->order_by == 'draw_date')
            {
                $order_by = 'order by au.draw_date desc';
            }

            if ($crt->order_by == 'exchange_date')
            {
                $order_by = 'order by au.exchange_date desc';
            }
        }

        if (!empty($cte_condition_arr)) {
            $cte_condition_str = 'where ' . implode(' and ', $cte_condition_arr);
        }

        if ($page_num) {
            $row_start = ($page_num - 1) * $page_size + 1;
            $row_end = $page_num * $page_size;
            $page_condition_str = "where row_num between $row_start and $row_end ";
        }

        $sql = <<<SQL
        with ACT_USER_CTE as (
          select
          u.user_name,
          u.phone,
          u.id_no,
          u.book_no,
          au.activity_id,
          au.user_id,
          au.is_win,
          convert(varchar(20), au.draw_date, 20) as draw_date,
          convert(varchar(20), au.exchange_date, 20) as exchange_date,
          ROW_NUMBER() over ($order_by) as row_num
          from
          GYGD_ActivityToUser au
          left join GYGD_User u on u.id = au.user_id
          $cte_condition_str
        )
        select * from ACT_USER_CTE
        $page_condition_str
SQL;
//        echo $sql; exit();
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取体育馆活动用户总数
     * @param array|null $criteria
     * @return int
     */
    public static function getStadiumActivityUserCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array('au.activity_id = 1');
        $condition_str = '';
        $bind = array();

        if ($crt->id_no)
        {
            $condition_arr[] = 'u.id_no = :id_no';
            $bind['id_no'] = $crt->id_no;
        }

        if ($crt->user_name)
        {
            $condition_arr[] = 'u.user_name = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if ($crt->phone)
        {
            $condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if ($crt->state === 0 or $crt->state === '0')
        {
            //未中奖用户
            $condition_arr[] = 'au.is_win = 0';
        }
        elseif ($crt->state == 1)
        {
            //中奖用户
            $condition_arr[] = 'au.is_win = 1';
        }
        elseif ($crt->state == 2)
        {
            //已领取用户
            $condition_arr[] = 'au.exchange_date is not null';
        }
        elseif ($crt->state == 3)
        {
            //未领取用户
            $condition_arr[] = 'au.exchange_date is null';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
          select count(au.user_id)
          from
          GYGD_ActivityToUser au
          left join GYGD_User u on u.id = au.user_id
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 删除体育馆用户参与信息
     * @param $activity_id
     * @param $user_id
     * @return bool
     */
    public static function deleteStadiumActivityUser($activity_id, $user_id)
    {
        $connection = self::_getConnection();
        $connection->begin();
        try
        {
            $del_activity_user_sql = "delete from GYGD_ActivityToUser where activity_id = :activity_id and user_id = :user_id";
            $del_activity_user_bind = array(
                'activity_id' => $activity_id,
                'user_id' => $user_id
            );
            $del_activity_user_success = self::nativeExecute($del_activity_user_sql, $del_activity_user_bind);
            if (!$del_activity_user_success)
            {
                throw new Exception();
            }
            $del_user_awards_sql = "delete from GYGD_AwardToUser where user_id = :user_id and award_id in (select id from GYGD_ActivityAward where activity_id = :activity_id)";
            $del_user_awards_bind = array(
                'activity_id' => $activity_id,
                'user_id' => $user_id
            );
            $del_user_awards_success = self::nativeExecute($del_user_awards_sql, $del_user_awards_bind);
            if (!$del_user_awards_success)
            {
                throw new Exception();
            }
            $success = $connection->commit();
        }
        catch(Exception $e)
        {
          $connection->rollBack();
        }

        return $success;
    }

    /**
     * 获取体育馆活动用户列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getMuseumActivityUserList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition_arr = array('au_step1.activity_id = 2');
        $cte_condition_str = '';
        $page_condition_str = '';

        if ($crt->id_no)
        {
            $cte_condition_arr[] = 'u.id_no = :id_no';
            $bind['id_no'] = $crt->id_no;
        }

        if ($crt->user_name)
        {
            $cte_condition_arr[] = 'u.user_name = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if ($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if ($crt->book_no)
        {
            $cte_condition_arr[] = 'u.book_no = :book_no';
            $bind['book_no'] = $crt->book_no;
        }

        if ($crt->state == 'NO_WIN')
        {
            //未中奖用户
            $cte_condition_arr[] = '(au_step1.is_win = 0 and (au_step2.is_win = 0 or au_step2.is_win is null))';
        }
        elseif ($crt->state == 'HALF_YEAR_WIN')
        {
            //中奖用户
            $cte_condition_arr[] = 'au_step1.is_win = 1';
        }
        elseif ($crt->state == 'FULL_YEAR_WIN')
        {
            //已领取用户
            $cte_condition_arr[] = 'au_step2.is_win = 1';
        }
        elseif ($crt->state == 'NO_EXCHANGED')
        {
            //未领取用户
            $cte_condition_arr[] = '(au_step1.exchange_date is null and au_step2.exchange_date is null)';
        }
        elseif ($crt->state == 'EXCHANGED')
        {
            //已领取用户
            $cte_condition_arr[] = '(au_step1.exchange_date is not null or au_step2.exchange_date is not null)';
        }

        $order_by = 'order by au_step1.draw_date desc';
        if ($crt->order_by)
        {
            if ($crt->order_by == 'draw_date')
            {
                $order_by = 'order by isnull(au_step1.draw_date, au_step2.draw_date) desc';
            }

            if ($crt->order_by == 'exchange_date')
            {
                $order_by = 'order by isnull(au_step1.exchange_date, au_step2.exchange_date) desc';
            }
        }

        if (!empty($cte_condition_arr)) {
            $cte_condition_str = 'where ' . implode(' and ', $cte_condition_arr);
        }

        if ($page_num) {
            $row_start = ($page_num - 1) * $page_size + 1;
            $row_end = $page_num * $page_size;
            $page_condition_str = "where row_num between $row_start and $row_end ";
        }

        $sql = <<<SQL
        with ACT_USER_CTE as (
          select
          u.user_name,
          u.phone,
          u.id_no,
          u.book_no,
          isnull(au_step2.activity_id, au_step1.activity_id) as activity_id, 
          au_step1.user_id as user_id,
          au_step1.is_win as half_year_win,
          isnull(au_step2.is_win, 0) as full_year_win,
          convert(varchar(20), au_step1.draw_date, 20) as half_year_draw_date,
          convert(varchar(20), au_step1.exchange_date, 20) as half_year_exchange_date,
          convert(varchar(20), au_step2.draw_date, 20) as full_year_draw_date,
          convert(varchar(20), au_step2.exchange_date, 20) as full_year_exchange_date,
          case
            when au_step1.is_win = 1 then
                stuff(
                  (select ',' + name from GYGD_ActivityAward as award
                    left join GYGD_AwardToUser as a2u on a2u.award_id = award.id
                  where award.activity_id = au_step1.activity_id and a2u.user_id = au_step1.user_id
                  for xml path('')), 1, 1, '')
            else
              null
          end as half_year_awards,
          case
            when au_step2.is_win = 1 then
                stuff(
                  (select ',' + name from GYGD_ActivityAward as award
                    left join GYGD_AwardToUser as a2u on a2u.award_id = award.id
                  where award.activity_id = au_step2.activity_id and a2u.user_id = au_step2.user_id
                  for xml path('')), 1, 1, '')
            else
              null
          end as full_year_awards,
          ROW_NUMBER() over ($order_by) as row_num
          from
          (
            select activity_id, user_id, is_win, draw_date, exchange_date from GYGD_ActivityToUser where activity_id = 2
          ) as au_step1
          left join
          (
            select activity_id, user_id, is_win, draw_date, exchange_date from GYGD_ActivityToUser where activity_id = 3
          ) as au_step2 on au_step2.user_id = au_step1.user_id
          left join GYGD_User u on u.id = au_step1.user_id
          $cte_condition_str
        )
        select * from ACT_USER_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取体育馆活动用户总数
     * @param array|null $criteria
     * @return int
     */
    public static function getMuseumActivityUserCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array('au_step1.activity_id = 2');
        $condition_str = '';
        $bind = array();

        if ($crt->id_no)
        {
            $condition_arr[] = 'u.id_no = :id_no';
            $bind['id_no'] = $crt->id_no;
        }

        if ($crt->user_name)
        {
            $condition_arr[] = 'u.user_name = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if ($crt->phone)
        {
            $condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if ($crt->book_no)
        {
            $condition_arr[] = 'u.book_no = :book_no';
            $bind['book_no'] = $crt->book_no;
        }

        if ($crt->state == 'NO_WIN')
        {
            //未中奖用户
            $condition_arr[] = '(au_step1.is_win = 0 and (au_step2.is_win = 0 or au_step2.is_win is null))';
        }
        elseif ($crt->state == 'HALF_YEAR_WIN')
        {
            //中奖用户
            $condition_arr[] = 'au_step1.is_win = 1';
        }
        elseif ($crt->state == 'FULL_YEAR_WIN')
        {
            //已领取用户
            $condition_arr[] = 'au_step2.is_win = 1';
        }
        elseif ($crt->state == 'NO_EXCHANGED')
        {
            //未领取用户
            $condition_arr[] = 'au_step1.exchange_date is null and au_step2.exchange_date is null';
        }
        elseif ($crt->state == 'EXCHANGED')
        {
            //未领取用户
            $condition_arr[] = 'au_step1.exchange_date is not null or au_step2.exchange_date is not null';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
          select count(au_step1.user_id)
          from
          (
            select activity_id, user_id, is_win, draw_date, exchange_date from GYGD_ActivityToUser where activity_id = 2
          ) as au_step1
          left join
          (
            select activity_id, user_id, is_win, draw_date, exchange_date from GYGD_ActivityToUser where activity_id = 3
          ) as au_step2 on au_step2.user_id = au_step1.user_id
          left join GYGD_User as u on u.id = au_step1.user_id
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 添加参与用户
     * @param int $activity_id
     * @param array $users
     * @param bool $is_win
     */
    public static function addActivityUser($activity_id, array $users, $is_win=false)
    {
        $bind = array();
        if ($is_win)
        {
            $field_str = '(activity_id, user_id, is_win)';
        }
        else
        {
            $field_str = '(activity_id, user_id)';
        }

        $values_str = '';

        foreach ($users as $index => $user)
        {
            $param_activity_id = 'activity_id_'.$index;
            $param_user_id = 'user_id_'.$index;
            if ($is_win)
            {
                $values_str .= "(:$param_activity_id, :$param_user_id, 1), ";
            }
            else
            {
                $values_str .= "(:$param_activity_id, :$param_user_id), ";
            }   
            $bind[$param_activity_id] = $activity_id;
            $bind[$param_user_id] = $user['user_id'];
        }

        if (!empty($values_str))
        {
            $values_str = rtrim($values_str, ', ');
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        insert into GYGD_ActivityToUser $field_str values $values_str
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除活动参与用户
     * @param  int $activity_id
     * @param  array  $users
     * @return bool
     */
    public static function deleteActivityUser($activity_id, array $users)
    {
        $bind = array();
        $condition_arr = array();
        $condition_str = '';

        foreach ($users as $index => $user)
        {
            $param_activity_id = 'activity_id_'.$index;
            $param_user_id = 'user_id_'.$index;
            $condition_arr[] = "(activity_id = :$param_activity_id and user_id = :$param_user_id)";
            $bind[$param_activity_id] = $activity_id;
            $bind[$param_user_id] = $user['user_id'];
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' or ', $condition_arr);
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        delete from GYGD_ActivityToUser $condition_str
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 批量更新活动参与用户
     * @param  array $users
     * @param  array  $data
     * @return bool
     */
    public static function updateActivityUsers($users, array $data)
    {
        $crt = new Criteria($data);
        $bind = array();
        $field_str = '';
        $condition_arr = array();
        $conditoin_str = '';

        if (!is_null($crt->is_win))
        {
            $field_str .= 'is_win = :is_win, ';
            $bind['is_win'] = $crt->is_win;
        }

        if (!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        foreach ($users as $index => $user)
        {
            $param_user_id = 'user_id_'.$index;
            $param_activity_id = 'activity_id_'.$index;
            $condition_arr[] = "(activity_id = :$param_activity_id and user_id = :$param_user_id)";
            $bind[$param_user_id] = $user['user_id'];
            $bind[$param_activity_id] = $user['activity_id'];
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' or ', $condition_arr);
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        update GYGD_ActivityToUser set $field_str $condition_str
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新活动
     * @param  int $activity_id
     * @param  array  $data
     * @return bool
     */
    public static function updateActivity($activity_id, array $data)
    {
        $crt = new Criteria($data);
        $field_str = '';
        $bind = array(
            'activity_id' => $activity_id
        );

        if ($crt->win_limit)
        {
            $field_str .= 'win_limit = :win_limit, ';
            $bind['win_limit'] = $crt->win_limit;
        }

        if ($crt->win_rate)
        {
            $field_str .= 'win_rate = :win_rate, ';
            $bind['win_rate'] = $crt->win_rate;
        }

        if ($crt->win_rule)
        {
            $win_rule = json_encode($crt->win_rule);
            $field_str .= 'win_rule = :win_rule, ';
            $bind['win_rule'] = $win_rule;
        }

        if (!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        update GYGD_Activity set $field_str where id = :activity_id
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 活动领取
     * @param $activity_id
     * @param $user_id
     * @return bool
     */
    public function gainStadiumActivity($activity_id, $user_id)
    {
        $sql = <<<SQL
        update GYGD_ActivityToUser set exchange_date = getdate()
        where activity_id = :activity_id and user_id = :user_id
SQL;
        $bind = array(
            'activity_id' => $activity_id,
            'user_id' => $user_id
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取博物馆活动奖品列表
     * @param  int $activity_id
     * @return array
     */
    public static function getMuseumActivityAwardList($activity_id)
    {
        $sql = "select id, name from GYGD_ActivityAward where activity_id = :activity_id";
        $bind = array('activity_id' => $activity_id);

        return self::nativeQuery($sql, $bind);
    }


    /**
     * 添加用户奖品
     * @param int $award_id
     * @param array $users
     */
    public static function addAwardToUser($award_id, array $users)
    {
        $bind = array();
        $values_str = '';
        foreach ($users as $index => $user)
        {
            $param_user_id = 'user_id_'.$index;
            $param_award_id = 'award_id_'.$index;
            $values_str .= "(:$param_award_id, :$param_user_id), ";
            $bind[$param_user_id] = $user['user_id'];
            $bind[$param_award_id] = $award_id;
        }
        if (!empty($values_str))
        {
            $values_str = rtrim($values_str, ', ');
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        insert into GYGD_AwardToUser (award_id, user_id) values $values_str
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除用户奖品
     * @param  int $award_id
     * @param  array $users
     * @return bool
     */
    public static function deleteAwardToUser($award_id, array $users)
    {
        $bind = array();
        $condition_arr = array();
        $condition_str = '';

        foreach ($users as $index => $user)
        {
            $param_award_id = 'award_id_'.$index;
            $param_user_id = 'user_id_'.$index;
            $condition_arr[] = "(award_id = :$param_award_id and user_id = :$param_user_id)";
            $bind[$param_award_id] = $award_id;
            $bind[$param_user_id] = $user['user_id'];
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' or ', $condition_arr);
        }
        else
        {
            return false;
        }

        $sql = <<<SQL
        delete from GYGD_AwardToUser $condition_str
SQL;
        return self::nativeExecute($sql, $bind);
    }
}