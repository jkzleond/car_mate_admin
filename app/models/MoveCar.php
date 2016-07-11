<?php

use \Phalcon\Db;

class MoveCar extends ModelEx
{

    /**
     * 弃用
     * @param array $data
     * @return bool|int
     */
    public static function addRecord(array $data)
    {
        $crt = new Criteria($data);
        $sql = 'insert into MC_RequestRecord (userid, wx_openid, source, hphm) values (:user_id, :openid, :source, :hphm)';
        $bind = array(
            'user_id' => $crt->user_id,
            'openid' => $crt->openid,
            'source' => $crt->source,
            'hphm' => $crt->hphm
        );

        $success = self::nativeExecute($sql, $bind);
        $connection = self::_getConnection();
        return $success ? $connection->lastInsertId() : false;
    }

    public static function getCarOwnerPhone($hphm)
    {
        $sql = 'select phone from JGCarOwner where hphm = :hphm';
        $bind = array('hphm' => $hphm);
        $car_owner = self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
        return $car_owner['phone'];
    }

    /**
     * 获取车主列表
     * @param array|null $criteria
     * @param int|null $page_num,
     * @param int|null $page_size
     * @return array | null
     */
    public static function getCarOwnerList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if($crt->hphm)
        {
            $cte_condition_arr[] = 'hphm = :hphm';
            $bind['hphm'] = $crt->hphm;
        }

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $row_start = ($page_num - 1) * $page_size + 1;
            $row_end = $page_num * $page_size;
            $page_condition_str = "where row_num between $row_start and $row_end ";
        }

        $sql = <<<SQL
        with CO_CTE as (
          select *, ROW_NUMBER() over (order by source asc) as row_num from
          (
              select
                c.id,
                c.hphm,
                isnull(mu.phone, u.phone) as phone,
                c.user_id,
                c.not_owner_count,
                c.not_link_count,
                c.call_count,
                c.state,
                'cm' as source
              from MC_Car c
              left join IAM_USER u on u.userid = c.user_id
              left join MC_User mu on mu.user_id = c.user_id
          union all
              select id, hphm, phone, null as user_id, not_owner_count, not_link_count, call_count, state, 'jg' as source from JGCarOwner
          ) co
          $cte_condition_str
        )
        select * from CO_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取车主总数
     * @param array|null $criteria
     * @return int
     */
    public static function getCarOwnerTotal(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $condition_arr = array();
        $condition_str = '';

        if($crt->hphm)
        {
            $condition_arr[] = 'hphm = :hphm';
            $bind['hphm'] = $crt->hphm;
        }

        if($crt->user_id)
        {
            $condition_arr[] = 'user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->phone)
        {
            $condition_arr[] = 'phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(id) from
          (
              select
                c.id,
                c.hphm,
                isnull(mu.phone, u.phone) as phone,
                c.user_id,
                c.not_owner_count,
                c.not_link_count,
                c.call_count,
                'cm' as source
              from MC_Car c
              left join IAM_USER u on u.userid = c.user_id
              left join MC_User mu on mu.user_id = c.user_id
          union all
              select id, hphm, phone, null as user_id, not_owner_count, not_link_count, call_count, 'jg' as source from JGCarOwner
          ) co
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return !empty($result) ? (int)$result[0] : 0;
    }

    /**
     * 获取指定ID的车主信息
     * @param $id
     * @param string $source 数据来源 'cm' or 'jg' default: 'cm'
     * @return array
     */
    public static function getCarOwnerById($id, $source='cm')
    {
        $bind = array('id' => $id);
        if($source == 'cm')
        {
            $sql = <<<SQL
            select
              c.id,
              c.hphm,
              isnull(mu.phone, u.phone) as phone
            from MC_Car c
            left join IAM_USER u on u.userid = c.user_id
            left join MC_User mu on mu.user_id = c.user_id
            where c.id = :id
SQL;

        }
        else
        {
            $sql = "select id, hphm, phone from JGCarOwner where id = :id";
        }

        return self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
    }

    /**
     * 获取指定ID车主的相关话单
     * @param $car_owner_source
     * @param $car_owner_id
     * @return array
     */
    public static function getCarOwnerCallRecord($car_owner_source, $car_owner_id)
    {
        $car_owner = MoveCar::getCarOwnerById($car_owner_id, $car_owner_source);
        $sql = <<<SQL
        select
          caller, called,
          convert(varchar(20), caller_start_time, 20) as start_time,
          convert(varchar(20), caller_end_time , 20) as end_time,
          caller_duration as duration,
          (ceiling((caller_duration + called_duration) / 60.00) * 0.12) as bill,
          case
            when called_duration > 0 then
              1
            else
              0
          end is_link
        from MC_CallRecord mc
        left join (
          select id from PayList where orderType = 'move_car'
        ) o on o.id = mc.order_id
        left join OrderToMoveCar o2mc on o2mc.order_id = o.id
        where mc.caller_start_time is not null and o2mc.hphm = :hphm and mc.called = :phone
SQL;
        $bind = array(
            'hphm' => $car_owner['hphm'],
            'phone' => $car_owner['phone']
        );


        return self::nativeQuery($sql, $bind);
    }

    /**
     * 标记车主(成功率)
     * @param int $id
     * @param bool $is_success
     * @param string $source
     * @return bool
     */
    public static function markCarOwnerById($id, $is_success, $source='cm')
    {
        $bind = array('id' => $id);
        $field_str = null;
        if($is_success)
        {
            $field_str = 'success_count += 1';
        }
        else
        {
            $field_str = 'fail_count += 1';
        }

        $table_name = 'MC_Car';

        if($source != 'cm')
        {
            $table_name = 'JGCarOwner';
        }

        $sql = <<<SQL
            update $table_name set $field_str where id = :id
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新指定ID车主信息
     * @param $id
     * @param $source
     * @param array|null $criteria
     * @return bool
     */
    public static function updateCarOwner($id, $source='cm', array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $bind = array('id' => $id);
        $field_str = '';

        if(!is_null($crt->state))
        {
            $field_str .= 'state = :state, ';
            $bind['state'] = $crt->state;
        }

        if(!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        $table_name = 'MC_Car';
        if($source == 'jg')
        {
            $table_name = 'JGCarOwner';
        }
        $sql = <<<SQL
        update $table_name set $field_str where id = :id;
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取指定ID订单的通话记录
     * @param $order_id
     * @return array
     */
    public static function getCallRecord($order_id)
    {
        $sql = <<<SQL
        select
          caller, called,
          convert(varchar(20), caller_start_time, 20) as start_time,
          convert(varchar(20), caller_end_time , 20) as end_time,
          caller_duration as duration,
          (ceiling((caller_duration + called_duration) / 60.00) * 0.12) as bill,
          case
            when called_duration > 0 then
              1
            else
              0
          end is_link
        from MC_CallRecord where order_id = :order_id
SQL;
        $bind = array(
            'order_id' => $order_id
        );

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定ID订单的申诉信息
     * @param $order_id
     * @return array
     */
    public static function getAppeal($order_id)
    {
        $sql = <<<SQL
        select
          problem, addition, advise
        from MC_Appeal
        where order_id = :order_id
SQL;
        $bind = array(
            'order_id' => $order_id
        );

        return self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
    }

    /**
     * 处理指定ID订单的申诉
     * @param $order_id
     * @param $process_des
     * @return bool
     */
    public static function processAppeal($order_id, $process_des)
    {
        $sql = <<<SQL
        update MC_Appeal set state = 1, process_des = :process_des where order_id = :order_id
SQL;
        $bind = array(
            'order_id' => $order_id,
            'process_des' => $process_des
        );
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取指定ID订单的反馈信息
     * @param $order_id
     * @return array
     */
    public static function getFeedback($order_id)
    {
        $sql = <<<SQL
        select q1_1, q1_2, q1_3, q1_4, q1_5, q1_6, q1_7, q2, advise from MC_Feedback
        where order_id = :order_id
SQL;
        $bind = array(
            'order_id' => $order_id
        );

        return self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
    }

    /**
     * 获取所有反馈意见列表数据
     * @param int $page_num
     * @param int $page_size
     * @return array
     */
    public static function getFeedbackAdviseList($page_num=null, $page_size=null)
    {
        $bind = array();
        $page_condition_str = '';
        if($page_num)
        {
            $page_condition_str = 'where row_num between :row_start and :row_end';
            $bind['row_start'] = ($page_num - 1) * $page_size + 1;
            $bind['row_end'] = $page_num * $page_size;
        }
        $sql = <<<SQL
        with ADV_CTE as (
            select order_id, convert(varchar(20), create_date, 20) as create_date, advise, q1_7 as other, row_number() over( order by create_date desc ) as row_num
            from MC_Feedback
            where advise is not null or q1_7 is not null
    )
        select
          adv.create_date, adv.other, adv.advise, o.userId as user_id, isnull(mu.phone, u.phone) as phone,
          stuff(
              (
                select ',' + hphm from MC_Car where user_id = o.userId for xml path('')
          ), 1, 1, ''
          ) as hphm
        from ADV_CTE adv
        left join PayList o on o.id = adv.order_id
        left join MC_User mu on mu.user_id = o.userId
        left join IAM_USER u on u.userid = o.userId
        $page_condition_str
SQL;

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取反馈意见总数
     * @return int
     */
    public static function getFeedbackAdviseTotal()
    {
        $sql = <<<SQL
        select count(id) from MC_Feedback where advise is not null or q1_7 is not null
SQL;
        $result = self::fetchOne($sql, null, null, Db::FETCH_NUM);
        return (int)$result[0];
    }
}
