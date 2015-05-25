<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-17
 * Time: 下午9:20
 *
 * 活动模型
 */

use \Phalcon\Db;

class Activity extends ModelEx
{

    /**
     * 根据活动id获取活动
     * @param $id
     * @return null
     */
    public static function getActivityById($id)
    {
        $sql = <<<SQL
        with USER_CTE as
        (
          select aid,count(aid) as num, sum(state) as gainNum
		  from ActivityUser where ISNULL(abandon, 0) !=1
		  group by aid
        ),
        AS_CTE as
        (
          select aid, Max([name]) as name, Max(optionList) as optionList,
		  Max(shortNames) as shortNames, Max(depositList) as depositList, Max(createTime) as createTime
          from ActivitySelect
          group by aid
        ),
        ORDER_CTE as
        (
          select orderName, count(orderName) as num from PayList
		  group by orderName
        )

        select a.id,a.name,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
		autoStart,a.state,isnull(u.num,0) num,isnull(u.gainNum,0) gainNum,
		[option],needCheckIn,t.name as typeName, t.id as typeId, a.awardStart, a.awardEnd, a.awardState,
		a.infos, a.[option], a.needPay, a.needNotice, a.deposit, a.payTypes,
		s.name as sname, s.optionList, s.shortNames, s.depositList,
		isnull(o.num, 0) as orderNum
		from Activity a
		left join USER_CTE u on a.id=u.aid
		left join ActivityType t on t.id=a.type
		left join AS_CTE s on s.aid = a.id
        left join ORDER_CTE o on o.orderName = a.name
		where ISNULL(a.abandon,0)!=1 and a.id = :id
SQL;

        $bind = array('id' => $id);

        $result =  self::nativeQuery($sql, $bind, null, Db::FETCH_OBJ);

        return !empty($result) ? $result[0] : null;

    }

    /**
     * 获取活动列表
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getActivityList($page_num=null, $page_size=null)
    {
        $bind = array();
        $page_condition = '';

        if($page_num)
        {
            $page_condition = 'and rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH ACT_CTE AS
        (
          SELECT *, ROW_NUMBER() OVER (ORDER BY STATE asc, endDate desc) AS rownum
          FROM Activity
          WHERE ISNULL(abandon, 0) != 1
        ),
        AS_CTE as
        (
          select aid, Max([name]) as name, Max(optionList) as optionList,
		  Max(shortNames) as shortNames, Max(depositList) as depositList, Max(createTime) as createTime
          from ActivitySelect
          group by aid
        )

		select rownum,a.id,a.name,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
		autoStart,a.state,isnull(u.num,0) num,isnull(u.gainNum,0) gainNum,
		[option],needCheckIn,t.name as typeName, t.id as typeId, a.awardStart, a.awardEnd, a.awardState,
		a.infos, a.[option], a.needPay, a.needNotice, a.deposit, a.payTypes, a.groupColumn,
		s.name as sname, s.optionList, s.shortNames, s.depositList
		from ACT_CTE a
		left join (
		  select aid,count(aid) as num, sum(state) as gainNum
			from ActivityUser where ISNULL(abandon, 0) !=1
			group by aid
		) u on u.aid = a.id
		left join ActivityType t on t.id = a.type
		left join AS_CTE s on s.aid = a.id
		where ISNULL(a.abandon, 0) != 1 %s
SQL;

        $sql = sprintf($sql, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取抽奖类型活动
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getAwardActivityList($page_num=null, $page_size=null)
    {
        $bind = array();
        $page_condition = '';

        if($page_num)
        {
            $page_condition = 'and rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH ACT_CTE AS
        (
          SELECT *, ROW_NUMBER() OVER (ORDER BY STATE asc, endDate desc) AS rownum
          FROM Activity
          WHERE ISNULL(abandon, 0) != 1 and [type] = 2
        ),
        AS_CTE as
        (
          select aid, Max([name]) as name, Max(optionList) as optionList,
		  Max(shortNames) as shortNames, Max(depositList) as depositList, Max(createTime) as createTime
          from ActivitySelect
          group by aid
        )

		select rownum,a.id,a.name,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
		autoStart,a.state,isnull(u.num,0) num,isnull(u.gainNum,0) gainNum,
		[option],needCheckIn,t.name as typeName, t.id as typeId, a.awardStart, a.awardEnd, a.awardState,
		a.infos, a.[option], a.needPay, a.needNotice, a.deposit, a.payTypes, a.groupColumn,
		s.name as sname, s.optionList, s.shortNames, s.depositList
		from ACT_CTE a
		left join (
		  select aid,count(aid) as num, sum(state) as gainNum
			from ActivityUser where ISNULL(abandon, 0) !=1
			group by aid
		) u on u.aid = a.id
		left join ActivityType t on t.id = a.type
		left join AS_CTE s on s.aid = a.id
		where ISNULL(a.abandon, 0) != 1 %s
SQL;

        $sql = sprintf($sql, $page_condition);

        return self::nativeQuery($sql, $bind);
    }


    /**
     * 获取活动总数
     * @return int
     */
    public static function getActivityCount()
    {
        $sql = 'select count(id) from Activity where ISNULL(abandon,0)!=1';

        $result = self::nativeQuery($sql, null, null, Db::FETCH_NUM);

        return $result[0][0];
    }

    /**
     * 获取抽奖类型活动总数
     * @return int
     */
    public static function getAwardActivityCount()
    {
        $sql = 'select count(id) from Activity where ISNULL(abandon,0) != 1 and [type] = 2';

        $result = self::nativeQuery($sql, null, null, Db::FETCH_NUM);

        return $result[0][0];
    }

    /**
     * 获取活动类型列表
     * @return array
     */
    public static function getActivityTypeList()
    {
        $sql = 'select id, [name] from ActivityType order by aOrder';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * 获取签到列表
     * @param null $aid
     * @param null $user_id
     * @return array
     */
    public static function getCheckList($aid=null, $user_id=null, $page_num=null, $page_size=10)
    {

        $cte_condition_arr = array();
        $cte_condition = '';
        $page_condition = '';




        $bind = array();

        if($aid)
        {
            $cte_condition_arr[] = 'aid = :aid';
            $bind['aid'] = $aid;
        }

        if($user_id)
        {
            $cte_condition_arr[] = 'userid = :user_id';
            $bind['user_id'] = $user_id;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition = 'where '.implode(' and ', $cte_condition_arr);
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
        with CHECK_CTE as (
          select userid, ROW_NUMBER() OVER (order by userid) as rownum
          from dbo.ActivityCheck
          %s
          group by userid
        )

        select rownum, c.id, c.userid, c.url, c.des, c.createDate from CHECK_CTE cte
        left join dbo.ActivityCheck c
        on c.userid = cte.userid
        %s
SQL;

        $sql = sprintf($sql, $cte_condition, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取签到总数
     * @param null $aid
     * @param null $user_id
     * @return int
     */
    public static function getCheckCount($aid=null, $user_id=null)
    {
        $where_stm = '';
        $condition = array();

        $bind = array();

        if($aid)
        {
            $condition[] = 'aid = :aid';
            $bind['aid'] = $aid;
        }

        if($user_id)
        {
            $condition[] = 'userid = :user_id';
            $bind['user_id'] = $user_id;
        }

        if(!empty($condition))
        {
            $where_stm = 'where '.implode(' and ', $condition);
        }

        $sql = 'select count(id) from ActivityCheck %s group by userid';

        $sql = sprintf($sql, $where_stm);

        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);

        return count($result);
    }


    /**
     * 获取付款信息列表
     * @param null $aid
     * @param null $page_num
     * @param int $page_size
     * @return array
     */
    public static function getPayList($aid=null, $page_num=null, $page_size=10)
    {

        $aid_condition = '';
        $page_condition = '';

        $bind = array();

        if($aid || $aid == 0)
        {
            $aid_condition = 'where a.id = :aid';
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
        with PAY_CTE as (
          select p.orderNo, p.orderName, p.money, p.state, p.userId,
          p.payTime, p.buyerName, p.createTime, ROW_NUMBER() OVER (order by p.createTime desc,
          p.state desc ) as rownum from PayList as p
          left join Activity as a on a.name = p.orderName
          %s
        )
        select * from PAY_CTE p
        %s
SQL;
        $sql = sprintf($sql, $aid_condition, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    public static function getPayCount($aid)
    {
        $where_stm = '';
        $field_str = '';

        $bind = array();

        if($aid)
        {
            $field_str .= 'a.id = :aid';
            $bind['aid'] = $aid;
        }

        if($field_str)
        {
            $where_stm = 'where '.$field_str;
        }

        $sql = <<<SQL
        select count(p.id) from dbo.PayList p
		left join Activity as a on a.name = p.orderName
		%s
SQL;

        $sql = sprintf($sql, $where_stm);

        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0][0];
    }

    /**
     * 添加活动
     * @param $name
     * @param $place
     * @param $url
     * @param $start_date
     * @param $end_date
     * @param $auto_start
     * @param $info
     * @param $option
     * @param $type_id
     * @param $need_check_in
     * @param $need_notice
     * @param null $award_start
     * @param null $award_end
     * @param $need_pay
     * @param $deposit
     * @param $pay_types
     * @param $group_column
     * @return bool|int
     */
    public static function addActivity($name, $place, $url, $start_date, $end_date, $auto_start, $info, $option, $type_id, $need_check_in, $need_notice, $award_start=null, $award_end=null, $need_pay, $deposit, $pay_types, $group_column)
    {

        $award_start = $award_start ? $award_start : date('Y-m-d H:i:s');
        $award_end = $award_end ? $award_end : date('Y-m-d H:i:s');

        $award_state = 0;

        if($type_id == '2')
        {
            $award_state = 1;
        }

        $sql = <<<SQL
        insert into Activity([name], place, url, createDate, startDate, endDate,
		state, autoStart, infos, [option], [type], needCheckIn, needNotice, awardStart, awardEnd, awardState,
		needPay, deposit, payTypes,groupColumn)
		values ( :name, :place, :url, getdate(), :start_date,:end_date, 0, :auto_start, :info, :option, :type_id, :need_check_in, :need_notice,:award_start, :award_end, :award_state, :need_pay, :deposit , :pay_types,:group_column)
SQL;
        $bind = array(
            'name' => $name,
            'place' => $place,
            'url' => $url,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'auto_start' => $auto_start,
            'info' => $info,
            'option' => $option,
            'type_id' => $type_id,
            'need_check_in' => $need_check_in,
            'need_notice' => $need_notice,
            'award_start' => $award_start,
            'award_end' => $award_end,
            'award_state' => $award_state,
            'need_pay' => $need_pay,
            'deposit' => $deposit,
            'pay_types' => $pay_types,
            'group_column' => $group_column
        );
        
        $success = self::nativeExecute($sql, $bind);

        return $success ? self::_getConnection()->lastInsertId() : false;
    }

    /**
     * 删除活动
     * @param $id
     * @return bool
     */
    public static function delActivity($id)
    {
        $sql = 'update Activity set abandon=1 where id=:id';
        $bind = array('id' => $id);
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新活动
     * @param $id
     * @param null $name
     * @param null $place
     * @param null $url
     * @param null $auto_start
     * @param null $start_date
     * @param null $end_date
     * @param null $state
     * @param null $info
     * @param null $option
     * @param null $type_id
     * @param null $need_check_in
     * @param null $award_start
     * @param null $award_end
     * @param null $award_state
     * @param null $need_pay
     * @param null $deposit
     * @param null $need_notice
     * @param null $pay_types
     * @param null $group_column
     * @return bool
     */
    public static function updateActivity($id, $name=null, $place=null, $url=null, $auto_start=null, $start_date=null, $end_date=null, $state=null, $info=null, $option=null, $type_id=null, $need_check_in=null, $award_start=null, $award_end=null, $award_state=null, $need_pay=null, $deposit=null, $need_notice=null, $pay_types=null, $group_column=null)
    {

        $sql = 'update Activity set %s where id = :id';

        $field_str = '';
        $bind = array('id' => $id);

        if($name)
        {
            $field_str .= '[name] = :name, ';
            $bind['name'] = $name;
        }

        if($place)
        {
            $field_str .= 'place = :place, ';
            $bind['place'] = $place;
        }

        if($url)
        {
            $field_str .= 'url = :url, ';
            $bind['url'] = $url;
        }

        if($auto_start || $auto_start === 0 || $auto_start === '0')
        {
            $field_str .= 'autoStart = :auto_start, ';
            $bind['auto_start'] = $auto_start;
        }

        if($start_date)
        {
            $field_str .= 'startDate = :start_date, ';
            $bind['start_date'] = $start_date;
        }

        if($end_date)
        {
            $field_str .= 'endDate = :end_date, ';
            $bind['end_date'] = $end_date;
        }

        if($state || $state === 0 || $state === '0')
        {
            $field_str .= 'state = :state, ';
            $bind['state'] = $state;
        }

        if($info)
        {
            $field_str .= 'infos = :info, ';
            $bind['info'] = $info;
        }

        if($option)
        {
            $field_str .= '[option] = :option, ';
            $bind['option'] = $option;
        }

        if($type_id)
        {
            $field_str .= '[type] = :type_id, ';
            $bind['type_id'] = $type_id;
        }

        if($need_check_in || $need_check_in === 0 || $need_check_in === '0')
        {
            $field_str .= 'needCheckIn = :need_check_in, ';
            $bind['need_check_in'] = $need_check_in;
        }

        if($award_start)
        {
            $field_str .= 'awardStart = :award_start, ';
            $bind['award_start'] = $award_start;
        }

        if($award_end)
        {
            $field_str .= 'awardEnd = :award_end, ';
            $bind['award_end'] = $award_end;
        }

        if($award_state)
        {
            $field_str .= 'awardState = :award_state, ';
            $bind['award_state'] = $award_state;
        }

        if($need_pay)
        {
            $field_str .= 'needPay = :need_pay, ';
            $bind['need_pay'] = $need_pay;
        }

        if($deposit)
        {
            $field_str .= 'deposit = :deposit, ';
            $bind['deposit'] = $deposit;
        }

        if($need_notice || $need_notice === 0 || $need_notice === '0')
        {
            $field_str .= 'needNotice = :need_notice, ';
            $bind['need_notice'] = $need_notice;
        }

        if($pay_types)
        {
            $field_str .= 'payTypes = :pay_types, ';
            $bind['pay_types'] = $pay_types;
        }

        if($group_column)
        {
            $field_str .= 'groupColumn = :group_column, ';
            $bind['group_column'] = $group_column;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $aid
     * @param $name
     * @param $option_list
     * @param $short_names
     * @param $deposit_list
     * @return bool
     */
    public static function addActivitySelect($aid, $name, $option_list, $short_names, $deposit_list)
    {
        $sql = <<<SQL
        insert into ActivitySelect(aid, [name], optionList, shortNames, depositList) values (:aid,
		:name, :option_list, :short_names, :deposit_list)
SQL;
        $bind = array(
            'aid' => $aid,
            'name' => $name,
            'option_list' => $option_list,
            'short_names' => $short_names,
            'deposit_list' => $deposit_list
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新ActivitySelect
     * @param $aid
     * @param null $name
     * @param null $option_list
     * @param null $short_names
     * @param null $deposit_list
     * @return bool
     */
    public static function updateActivitySelect($aid, $name=null, $option_list=null, $short_names=null, $deposit_list=null)
    {
        
        $sql = 'update ActivitySelect set %s where aid = :aid';
        
        $field_str = '';
        $bind = array('aid' => $aid);
        
        if($name)
        {
            $field_str .= '[name] = :name, ';
            $bind['name'] = $name;
        }

        if($option_list || $option_list === '')
        {
            $field_str .= 'optionList = :option_list, ';
            $bind['option_list'] = $option_list;
        }

        if($short_names || $short_names === '')
        {
            $field_str .= 'shortNames = :short_names, ';
            $bind['short_names'] = $short_names;
        }

        if($deposit_list || $deposit_list === '')
        {
            $field_str .= 'depositList = :deposit_list';
            $bind['deposit_list'] = $deposit_list;
        }
        
        $field_str = rtrim($field_str, ', ');

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }
}