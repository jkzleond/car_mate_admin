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
          select aid,count(aid) as num, sum(state) as gainNum, sum(payState) as payNum
		  from ActivityUser where ISNULL(abandon, 0) !=1
		  group by aid
        ),
        AS_CTE as
        (
          select aid, Max([name]) as name, Max(optionList) as optionList,
		  Max(shortNames) as shortNames, Max(depositList) as depositList, Max(createTime) as createTime
          from ActivitySelect
          group by aid
        )

        select a.id,a.name,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
		autoStart,a.state,isnull(u.num,0) num,isnull(u.gainNum,0) gainNum, u.payNum,
		[option],needCheckIn,t.name as typeName, t.id as typeId, a.awardStart, a.awardEnd, a.awardState,
		a.infos, a.[option], a.needPay, a.needNotice, a.deposit, a.payTypes,
		s.name as sname, s.optionList, s.shortNames, s.depositList
		from Activity a
		left join USER_CTE u on a.id=u.aid
		left join ActivityType t on t.id=a.type
		left join AS_CTE s on s.aid = a.id
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

		select rownum,a.id,a.name,a.picData,a.contents,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
		signStartDate, signEndDate, tripLine, autoStart, a.state, isnull(u.num,0) num, isnull(u.gainNum,0) gainNum, a.viewNum,
		[option],needCheckIn,t.name as typeName, t.id as typeId, a.awardStart, a.awardEnd, a.awardState, a.is_period,
		a.infos, a.[option], a.needPay, a.needNotice, a.deposit, a.payTypes, a.groupColumn,
		g.id as pay_item_id, g.name as pay_item_name, g.price as pay_item_price,
		s.name as sname, s.optionList, s.shortNames, s.depositList
		from ACT_CTE a
		left join (
		  select aid,count(aid) as num, sum(state) as gainNum
			from ActivityUser where ISNULL(abandon, 0) !=1
			group by aid
		) u on u.aid = a.id
		left join ActivityType t on t.id = a.type
		left join Hui_ActivityToGoods a2g on a2g.activity_id = a.id
		left join Hui_Goods g on g.id = a2g.goods_id
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
            $page_condition = 'and a.rownum between :offset and :limit';
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

		select a.rownum,a.id,a.name,place,ISNULL(url,0) url,a.createDate,startDate,endDate,
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
     * 获取抽奖时段数据列表
     */
    public static function getDrawPeriodList($aid)
    {
        $sql = <<<SQL
        select id, aid, convert(varchar(20), start_time, 20) as start_time, convert(varchar(20), end_time, 20) as end_time 
        from Hui_DrawPeriod
        where aid = :aid
        order by start_time asc
SQL;
        $bind = array('aid' => $aid);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定ID抽奖时段数据
     * @param  int|string $period_id
     * @return int|string
     */
    public static function getDrawPeriodById($period_id)
    {
        $sql = <<<SQL
        select id, aid, convert(varchar(20), start_time, 20) as start_time, convert(varchar(20), end_time, 20) as end_time
        from Hui_DrawPeriod 
        where id = :id
SQL;
        $bind = array('id' => $period_id);

        return self::fetchOne($sql, $bind);
    }

    /**
     * 为指定ID活动添加时段
     * @param int $aid
     * @param array $criteria
     * @return bool
     */
    public static function addDrawPeriod($aid, array $criteria)
    {
        $crt = new Criteria($criteria);
        $sql = 'insert into Hui_DrawPeriod (aid, start_time, end_time) values (:aid, :start_time, :end_time)';
        $bind = array(
            'aid' => $aid,
            'start_time' => $crt->start_time,
            'end_time' => $crt->end_time
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新指定ID的时段
     * @param  int|string $id
     * @param  array      $criteria
     * @return bool
     */
    public static function updateDrawPeriod($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $field_str = '';
        $bind = array('id' =>$id);

        if($crt->start_time)
        {
            $field_str .= 'start_time = :start_time, ';
            $bind['start_time'] = $crt->start_time;
        }

        if($crt->end_time)
        {
            $field_str .= 'end_time = :end_time, ';
            $bind['end_time'] = $crt->end_time;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update Hui_DrawPeriod set $field_str where id = :id";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除指定ID抽奖时段
     * @param  int|string $id
     * @return bool
     */
    public static function delDrawPeriod($id)
    {
        $sql = 'delete from Hui_DrawPeriod where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
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
            $aid_condition = 'where au.aid = :aid';
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
          select o.orderNo, o.orderName, o.money, o.state, o.userId,
          o.payTime, o.createTime, u.uname as buyerName, ROW_NUMBER() OVER (order by o.createTime desc) as rownum
          from (
            select orderNo, orderName, money, state, userId,
            payTime, buyerName, createTime, relId from PayList
            where orderType = 'activity' and ( state = 'TRADE_SUCCESS' or state = 'TRADE_FINISHED')
          ) as o
          left join ActivityUser as au on au.id = o.relId
          left join IAM_USER as u on u.userid = au.userId
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
            $field_str .= 'au.aid = :aid';
            $bind['aid'] = $aid;
        }

        if($field_str)
        {
            $where_stm = 'where '.$field_str;
        }

        $sql = <<<SQL
        select count(1) from (
            select orderNo, orderName, money, state, userId,
            payTime, buyerName, createTime, relId from PayList
            where orderType = 'activity' and ( state = 'TRADE_SUCCESS' or state = 'TRADE_FINISHED')
        ) o
        left join ActivityUser as au on au.id = o.relId
		%s
SQL;

        $sql = sprintf($sql, $where_stm);

        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0][0];
    }

    /**
     * 添加活动
     * @param  array $criteria
     * @return bool|int
     */
    public static function addActivity(array $criteria)
    {

        $crt = new Criteria($criteria);

        $crt->award_state = 0;

        if($crt->type_id == '2')
        {
            $crt->award_state = 1;
        }

        $sql = <<<SQL
        insert into Activity([name], place, url, createDate, startDate, endDate, signStartDate, signEndDate, tripLine,
		state, autoStart, infos, [option], [type], needCheckIn, needNotice, awardStart, awardEnd, awardState,
		needPay, deposit, payTypes,groupColumn,picData,contents, is_period)
		values ( :name, :place, :url, getdate(), :start_date,:end_date, :sign_start_date, :sign_end_date, :trip_line, 0, :auto_start, :info, :option, :type_id, :need_check_in, :need_notice,:award_start, :award_end, :award_state, :need_pay, :deposit , :pay_types,:group_column, :pic_data, :contents, :is_period)
SQL;
        $bind = array(
            'name' => $crt->name,
            'place' => $crt->place,
            'url' => $crt->url,
            'start_date' => $crt->start_date,
            'end_date' => $crt->end_date,
            'sign_start_date' => $crt->sign_start_date,
            'sign_end_date' => $crt->sign_end_date,
            'trip_line' => $crt->trip_line,
            'auto_start' => $crt->auto_start,
            'info' => $crt->info,
            'option' => $crt->option,
            'type_id' => $crt->type_id,
            'need_check_in' => $crt->need_check_in,
            'need_notice' => $crt->need_notice,
            'award_start' => $crt->award_start ? $crt->award_start : date('Y-m-d H:i:s'),
            'award_end' => $crt->award_end ? $crt->award_end : date('Y-m-d H:i:s'),
            'award_state' => $crt->award_state,
            'need_pay' => $crt->need_pay,
            'deposit' => $crt->deposit,
            'pay_types' => $crt->pay_types,
            'group_column' => $crt->group_column,
            'pic_data' => $crt->pic_data,
            'contents' => $crt->contents,
            'is_period' => $crt->is_period
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
     * @param int|string $id
     * @param array      $criteria
     * @return bool
     */
    public static function updateActivity($id, array $criteria=null)
    {

        $crt = new Criteria($criteria);

        $sql = 'update Activity set %s where id = :id';

        $field_str = '';
        $bind = array('id' => $id);


        if($crt->name)
        {
            $field_str .= '[name] = :name, ';
            $bind['name'] = $crt->name;
        }

        if($crt->pic_data)
        {
            $field_str .= 'picData = :pic_data, ';
            $bind['pic_data'] = $crt->pic_data;
        }

        if($crt->contents)
        {
            $field_str .= 'contents = :contents, ';
            $bind['contents'] = $crt->contents;
        }

        if($crt->place)
        {
            $field_str .= 'place = :place, ';
            $bind['place'] = $crt->place;
        }

        if($crt->url)
        {
            $field_str .= 'url = :url, ';
            $bind['url'] = $crt->url;
        }

        if($crt->auto_start || $crt->auto_start === 0 || $crt->auto_start === '0')
        {
            $field_str .= 'autoStart = :auto_start, ';
            $bind['auto_start'] = $crt->auto_start;
        }

        if($crt->start_date)
        {
            $field_str .= 'startDate = :start_date, ';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $field_str .= 'endDate = :end_date, ';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->sign_start_date)
        {
            $field_str .= 'signStartDate = :sign_start_date, ';
            $bind['sign_start_date'] = $crt->sign_start_date;
        }

        if($crt->sign_end_date)
        {
            $field_str .= 'signEndDate = :sign_end_date, ';
            $bind['sign_end_date'] = $crt->sign_end_date;
        }

        if($crt->trip_line)
        {
            $field_str .= 'tripLine = :trip_line, ';
            $bind['trip_line'] = $crt->trip_line;
        }

        if($crt->state || $crt->state === 0 || $crt->state === '0')
        {
            $field_str .= 'state = :state, ';
            $bind['state'] = $crt->state;
        }

        if($crt->info)
        {
            $field_str .= 'infos = :info, ';
            $bind['info'] = $crt->info;
        }

        if($crt->option)
        {
            $field_str .= '[option] = :option, ';
            $bind['option'] = $crt->option;
        }

        if($crt->type_id)
        {
            $field_str .= '[type] = :type_id, ';
            $bind['type_id'] = $crt->type_id;
        }

        if($crt->need_check_in || $crt->need_check_in === 0 || $crt->need_check_in === '0')
        {
            $field_str .= 'needCheckIn = :need_check_in, ';
            $bind['need_check_in'] = $crt->need_check_in;
        }

        if($crt->award_start)
        {
            $field_str .= 'awardStart = :award_start, ';
            $bind['award_start'] = $crt->award_start;
        }

        if($crt->award_end)
        {
            $field_str .= 'awardEnd = :award_end, ';
            $bind['award_end'] = $crt->award_end;
        }

        if($crt->is_period or $crt->is_period === '0' or $crt->is_period === 0)
        {
            $field_str .= 'is_period = :is_period, ';
            $bind['is_period'] = $crt->is_period;
        }

        if($crt->award_state)
        {
            $field_str .= 'awardState = :award_state, ';
            $bind['award_state'] = $crt->award_state;
        }

        if($crt->need_pay or $crt->need_pay === 0 or $crt->need_pay === '0')
        {
            $field_str .= 'needPay = :need_pay, ';
            $bind['need_pay'] = $crt->need_pay;
        }

        if($crt->deposit)
        {
            $field_str .= 'deposit = :deposit, ';
            $bind['deposit'] = $crt->deposit;
        }

        if($crt->need_notice || $crt->need_notice === 0 || $crt->need_notice === '0')
        {
            $field_str .= 'needNotice = :need_notice, ';
            $bind['need_notice'] = $crt->need_notice;
        }

        if($crt->pay_types)
        {
            $field_str .= 'payTypes = :pay_types, ';
            $bind['pay_types'] = $crt->pay_types;
        }

        if($crt->group_column)
        {
            $field_str .= 'groupColumn = :group_column, ';
            $bind['group_column'] = $crt->group_column;
        }

        $field_str = rtrim($field_str, ', ');
        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加付款项目
     * @param $aid 活动id
     * @param array $pay_items 付款项目
     * @return bool
     */
    public static function addPayItems($aid, array $pay_items)
    {

        $connection = self::_getConnection();
        $connection->begin();

        $add_goods_sql = 'insert into Hui_Goods ( [name], price, type_id ) values (:name, :price, 1)';

        $add_a2g_sql = 'insert into Hui_ActivityToGoods (activity_id, goods_id) values (:aid, :goods_id) ';

        foreach($pay_items as $index => $pay_item)
        {
            $goods_bind = array();

            $goods_bind['name'] = $pay_item['name'];
            $goods_bind['price'] = $pay_item['price'];

            $add_goods_success = self::nativeExecute($add_goods_sql, $goods_bind);
            if(!$add_goods_success)
            {
                $connection->rollback();
                return false;
            }

            $new_goods_id =  $connection->lastInsertId();

            $a2g_bind = array(
                'aid' => $aid,
                'goods_id' => $new_goods_id
            );

            $add_a2g_success = self::nativeExecute($add_a2g_sql, $a2g_bind);
            if(!$add_a2g_success)
            {
                $connection->rollback();
                return false;
            }
        }

        return $connection->commit();
    }

    /**
     * 删除付款项目(仅删除活动与付款项的关联)
     * @param $aid
     * @param array $pay_item_ids
     * @return bool
     */
    public static function delPayItems($aid, array $pay_item_ids = null)
    {
        $pay_item_condition = '';
        $bind = array(
            'aid' => $aid
        );

        if(!empty($pay_item_ids))
        {
            foreach($pay_item_ids as $index => $pay_item_id)
            {
                $id_param = 'id'.$index;
                $pay_item_condition .= ":$id_param, ";
                $bind[$id_param] = $pay_item_id;
            }

            $pay_item_condition = 'and goods_id in ('.rtrim($pay_item_condition, ', ').')';
        }

        $sql = "delete from Hui_ActivityToGoods where activity_id = :aid $pay_item_condition";
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除某活动指定id以外的付款项
     * @param $aid
     * @param array $but_pay_item_ids
     * @return bool
     */
    public static function delPayItemsButIds($aid, array $but_pay_item_ids = null)
    {
        $pay_item_condition = '';
        $bind = array(
            'aid' => $aid
        );

        if(!empty($but_pay_item_ids))
        {
            foreach($but_pay_item_ids as $index => $pay_item_id)
            {
                $id_param = 'id'.$index;
                $pay_item_condition .= ":$id_param, ";
                $bind[$id_param] = $pay_item_id;
            }

            $pay_item_condition = 'and goods_id not in ('.rtrim($pay_item_condition, ', ').')';
        }

        $sql = "delete from Hui_ActivityToGoods where activity_id = :aid $pay_item_condition";
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
        
        /*$sql = 'update ActivitySelect set %s where aid = :aid';
        
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

        return self::nativeExecute($sql, $bind);*/
        $connection = self::_getConnection();
        $connection->begin();

        $del_success = self::delActivitySelect($aid);

        if(!$del_success)
        {
            $connection->rollback();
            return false;
        }

        self::addActivitySelect($aid, $name, $option_list, $short_names, $deposit_list);

        return $connection->commit();
    }

    /**
     * 删除活动下拉选项
     * @param  int|string $aid
     * @return bool
     */
    public static function delActivitySelect($aid)
    {
        $sql = 'delete from ActivitySelect where aid = :aid';
        $bind = array('aid' => $aid);

        return self::nativeExecute($sql, $bind);
    }
}