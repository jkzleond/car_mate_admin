<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-8
 * Time: 下午3:14
 */

use \Phalcon\Db;

class Award extends ModelEx
{
    /**
     * 获取奖品列表
     * @param null $aid
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getAwardList($aid=null, $page_num=null, $page_size=null)
    {
        $bind = array();

        $cte_condition = '';
        $page_condition = '';

        if($aid)
        {
            $cte_condition = 'where aid = :aid';
            $bind['aid'] = $aid;
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
        with AWARD_CTE as (
          select a.*, g.dayNum, ROW_NUMBER() OVER ( order by dayNum desc ) as rownum from Award a
		  left join (
		    select awid, count(id) as dayNum
		    from AwardGain where winDate>convert(varchar(10),getdate(),120)
		    and ISNULL(abandon,0)!=1  group by awid
          ) g on a.id=g.awid
          %s
        )

        select * from AWARD_CTE
        %s
SQL;
        $sql = sprintf($sql, $cte_condition, $page_condition);

        return self::nativeQuery($sql, $bind);
    }


    /**
     * 获取奖品总数
     * @param null $aid
     * @return int
     */
    public static function getAwardCount($aid=null)
    {
        $bind = array();

        $condition = '';

        if($aid)
        {
            $condition = 'where aid = :aid';
            $bind['aid'] = $aid;
        }

        $sql = 'select count(id) from Award %s';
        $sql = sprintf($sql, $condition);

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取奖品
     * @param $id
     * @return array
     */
    public static function getAwardById($id)
    {
        $sql = 'select * from Award where id = :id';
        $bind = array('id' => $id);

        return self::fetchOne($sql, $bind, null, Db::FETCH_OBJ);
    }

    /**
     * 添加奖品
     * @param $aid
     * @param $name
     * @param $des
     * @param $num
     * @param $rate
     * @param $day_limit
     * @param null $pic_data
     * @return bool
     */
    public static function addAward($aid, $name, $des, $num, $rate, $day_limit, $pic_data=null)
    {
        $sql = 'insert into Award ([name], des, num, rate, dayLimit, pic, aid) values (:name, :des, :num, :rate, :day_limit, :pic_data, :aid)';

        $bind = array(
            'name' => $name,
            'des' => $des,
            'num' => $num,
            'rate' => $rate,
            'day_limit' => $day_limit,
            'pic_data' => $pic_data,
            'aid' => $aid
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除奖品
     * @param $id
     * @return bool
     */
    public static function delAward($id)
    {
        $connection = self::_getConnection();
        $connection->begin();
        $sql_del_award_gian = 'delete from AwardGain where awid = :id';
        $sql_del_award = 'delete from Award where id = :id';
        $bind = array('id' => $id);

        $success_del_award_gain = $connection->execute($sql_del_award_gian, $bind);

        if(!$success_del_award_gain)
        {
            $connection->rollback();
            return false;
        }else
        {
            $connection->execute($sql_del_award, $bind);
            return $connection->commit();
        }
    }

    /**
     * 更新奖品
     * @param $id
     * @param Criteria $criteria
     * @return bool
     */
    public static function updateAward($id, Criteria $criteria)
    {
        $name = $criteria->name;
        $des = $criteria->des;
        $num = $criteria->num;
        $win_num = $criteria->win_num;
        $rate = $criteria->rate;
        $day_limit = $criteria->day_limit;
        $pic_data = $criteria->pic_data;
        $state = $criteria->state;
        $last_win_date = $criteria->last_win_date;

        $sql = 'update Award set %s where id = :id';
        $bind = array('id' => $id);

        $field_value = '';

        if($name)
        {
            $field_value .= '[name] = :name,';
            $bind['name'] = $name;
        }

        if($des)
        {
            $field_value .= 'des = :des,';
            $bind['des'] = $des;
        }

        if($num)
        {
            $field_value .= 'num = :num,';
            $bind['num'] = $num;
        }

        if($win_num)
        {
            $field_value .= 'winnum = :win_num,';
            $bind['win_num'] = $win_num;
        }

        if($state)
        {
            $field_value .= 'state = :state,';
            $bind['state'] = $state;
        }

        if($last_win_date)
        {
            $field_value .= 'lastWinDate = :last_win_date,';
            $bind['last_win_date'] = $last_win_date;
        }

        if($rate)
        {
            $field_value .= 'rate = :rate,';
            $bind['rate'] = $rate;
        }

        if($pic_data)
        {
            $field_value .= 'pic = :pic_data,';
            $bind['pic_data'] = $pic_data;
        }

        if($day_limit)
        {
            $field_value .= 'dayLimit = :day_limit,';
            $bind['day_limit'] = $day_limit;
        }

        if($field_value)
        {
            $field_value = rtrim($field_value, ', ');
        }

        $sql = sprintf($sql, $field_value);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取指定ID奖品的图片数据
     * @param  [type] $award_id
     * @return [type]
     */
    public static function getAwardPic($award_id)
    {
        $sql = 'select top 1 pic from Award where id = :id';
        $bind = array('id' => $award_id);

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
        return $result['pic'];
    }

    /**
     * 构建中奖总数sql cte
     * @param Criteria $crt
     * @return array [0]: string sql format [1]: array bind parameters
     */
    protected static function _buildAwardGainTotalSqlCte(Criteria $crt=null)
    {

        $criteria = $crt ? $crt : new Criteria();

        $a_condition = '';
        $g_condition_arr = array('ISNULL(abandon, 0) != 1');
        $g_condition = '';
        $gw_condition_arr = array('ISNULL(abandon, 0) != 1');
        $gw_condition = '';
        $gw_field_str = '';
        $cte_condition_arr = array();
        $cte_condition = '';

        $bind = array();

        $award_id = $criteria->award_id;
        $state = $criteria->state;
        $user_id = $criteria->user_id;
        $aid = $criteria->aid;

        if($aid)
        {
            $a_condition = 'where id = :aid';
            $gw_condition_arr[] = 'aid = :aid';
            $g_condition_arr[] = 'aid = :aid';
            $cte_condition_arr[] = 'gw.aid = :aid';

            $bind['aid'] = $aid;
        }

        if($award_id)
        {
            $g_condition_arr[] = 'awid = :award_id';
            $gw_condition_arr[] = 'awid = :award_id';
            $cte_condition_arr[] = 'awid = :award_id and winNum > 0';
            $gw_field_str .= ',awid';
            $bind['award_id'] = $award_id;
        }

        if($state and $state != -1)
        {
            $g_condition_arr[] = 'state = :state';
            $gw_condition_arr[] = 'state = :state';
            $gw_field_str .= ',state';
            $bind['state'] = $state;
        }

        if($user_id)
        {
            $g_condition_arr[] = 'userid like :user_id';
            $cte_condition_arr[] = 'ac.userid like :user_id';

            $bind['user_id'] = '%'.$user_id.'%';
        }

        if( !empty($g_condition_arr) )
        {
            $g_condition = 'where '.implode(' and ', $g_condition_arr);
        }

        if( !empty($gw_condition_arr) )
        {
            $gw_condition = 'where '.implode(' and ', $gw_condition_arr);
        }

        if( !empty($cte_condition_arr))
        {
            $cte_condition = 'where '.implode(' and ', $cte_condition_arr);
        }

        $sql = <<<SQL
        WITH USER_CTE AS (
            select ISNULL(ac.userid,g.userid) as userid,
              c.checkCount,u.wishCount, ac.chance as totalChance, g.winNum $gw_field_str,
              a.[name] as activityName, a.id as aid, ROW_NUMBER() OVER (ORDER BY ac.id desc) as rownum
            from (
                   select distinct userid, aid $gw_field_str
                   from AwardGain
                   $gw_condition
                 ) gw
            left join  AwardChance ac on ac.userid = gw.userid and ac.aid = gw.aid
            left join (select aid, userid, count(*) as checkCount from ActivityCheck group by aid,userid
                    ) c on ac.aid=c.aid and ac.userid=c.userid
            left join (select aid, userid, count(*) as wishCount from ActivityUser where abandon!=1 group by aid,userid
                    ) u on ac.aid=u.aid and ac.userid=u.userid
            left join (select userid,count(id) as winNum,aid from AwardGain
              $g_condition
              group by userid,aid
              ) g on g.aid=gw.aid and g.userid=gw.userid
            left join (
              select id, [name] from Activity
              $a_condition
            ) a on gw.aid=a.id or g.aid=a.id
          $cte_condition
        )
SQL;

        return array($sql, $bind);
    }

    /**
     * 获取指定ID抽奖时段的奖品数据列表
     * @param  [type] $period_id
     * @return [type]
     */
    public static function getDrawPeriodAwardList($period_id, $page_num=null, $page_size=null)
    {
        $page_condition_str = '';
        $bind = array('period_id' => $period_id);

        if($page_num)
        {   
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = $page_size * ($page_num - 1) + 1;
            $bind['to'] = $page_size * $page_num;
        }

        $sql = <<<SQL
        with AWARD_CTE as (
            select d2a.id as id, a.name, a.num, a.rate, a.dayLimit as day_limit, a.id as award_id,
            row_number() over(order by a.createDate desc) as rownum
            from Hui_DrawToAward d2a
            left join Award a on a.id = d2a.award_id
            where d2a.period_id = :period_id
        )
        select * from AWARD_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定ID抽奖时段的奖品数据总数
     * @param  int|string $period_id
     * @return int
     */
    public static function getDrawPeriodAwardCount($period_id)
    {
        $sql = 'select count(1) from Hui_DrawToAward where period_id = :period_id';
        $bind = array('period_id' => $period_id);

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 为指定ID的时段添加奖品
     * @param  int|string $period_id
     * @param  array      $criteria
     * @return bool
     */
    public static function addDrawPeriodAward($period_id, array $criteria=null)
    {
        $crt = new Criteria($criteria);

        $sql = 'insert into Hui_DrawToAward (aid, period_id, award_id) values (:aid, :period_id, :award_id)';
        $bind = array(
            'aid' => $crt->aid,
            'period_id' => $period_id,
            'award_id' => $crt->award_id
        );
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除抽奖时段的某个奖品
     * @param  int|string $id
     * @return bool
     */
    public static function delDrawPeriodAward($id)
    {
        $sql = 'delete from Hui_DrawToAward where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取中奖列表(用户获奖总数)
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getGainList($criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        list($sql_cte, $bind) = self::_buildAwardGainTotalSqlCte($crt);

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = $sql_cte.<<<SQL
        select * from USER_CTE
        %s
SQL;
        $sql = sprintf($sql, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取中奖总数
     * @param array $criteria
     * @return int
     */
    public static function getGainCount($criteria=null)
    {
        $crt = new Criteria($criteria);
        list($sql_cte, $bind) = self::_buildAwardGainTotalSqlCte($crt);
        $sql = $sql_cte.<<<SQL
        select count(*) from USER_CTE
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取某一用户的中奖列表
     * @param $user_id
     * @param null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getUserGainList($user_id, $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);

        $aid = $crt->aid;
        $awid = $crt->awid;
        $state = $crt->state;

        $condition_arr = array();
        $condition_str = '';

        $page_condition_str = '';

        $bind = array();

        if($user_id)
        {
            $condition_arr[] = 'g.userid = :user_id';
            $bind['user_id'] = $user_id;
        }

        if($aid)
        {
            $condition_arr[] = 'g.aid = :aid';
            $bind['aid'] = $aid;
        }

        if($awid)
        {
            $condition_arr[] = 'g.awid = :awid';
            $bind['awid'] = $awid;
        }

        if($state)
        {
            $condition_arr[] = 'g.state = :state';
            $bind['state'] = $state;
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        if( !empty($condition_arr) )
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        with GW_CTE as (
          select g.id,g.awid,g.aid,g.userid,g.gainDate,g.winDate,g.state,
		  ISNULL(g.randomColor,'') as randomColor,
		  ISNULL(g.randomCode,'') as randomCode,a.name as awardName,
		  ROW_NUMBER() OVER (ORDER BY g.id desc) as rownum
		  from AwardGain g inner join Award a on a.id=g.awid
		  $condition_str
        )
        select * from GW_CTE
        $page_condition_str
SQL;
        $sql = sprintf($sql, $condition_str);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取用户中奖总数
     * @param $user_id
     * @param null $criteria
     * @return mixed
     */
    public static function getUserGainCount($user_id, $criteria=null)
    {
        $crt = new Criteria($criteria);

        $aid = $crt->aid;
        $awid = $crt->awid;
        $state = $crt->state;

        $condition_arr = array();
        $condition_str = '';

        $bind = array();

        if($user_id)
        {
            $condition_arr[] = 'userid = :user_id';
            $bind['user_id'] = $user_id;
        }

        if($aid)
        {
            $condition_arr[] = 'aid = :aid';
            $bind['aid'] = $aid;
        }

        if($awid)
        {
            $condition_arr[] = 'awid = :awid';
            $bind['awid'] = $awid;
        }

        if($state)
        {
            $condition_arr[] = 'state = :state';
            $bind['state'] = $state;
        }

        if( !empty($condition_arr) )
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = 'select count(id) from AwardGain %s';

        $sql = sprintf($sql, $condition_str);

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 添加用户获奖记录
     * @param $user_id
     * @param $aid
     * @param $award_id
     * @param null $win_date
     * @return bool
     */
    public static function addAwardGain($user_id, $aid, $award_id, $win_date=null)
    {
        $win_date = $win_date ? $win_date : date('Y-m-d H:i:s');

        $sql = 'insert into AwardGain (userid, aid, awid, winDate, state) values(:user_id, :aid, :award_id, :win_date, 0)';
        $bind = array(
            'user_id' => $user_id,
            'aid' => $aid,
            'award_id' => $award_id,
            'win_date' => $win_date
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 设置领取
     * @param $id
     * @return bool
     */
    public static function awardGain($id)
    {
        $sql = 'update AwardGain set state = 1, gainDate = getdate() where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

}