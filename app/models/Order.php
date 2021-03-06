<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-7-30
 * Time: 下午1:38
 */

use \Phalcon\Db;

class Order extends ModelEx
{
    public static function getIllegalOrderList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        $bind = array();

        if($crt->order_no)
        {
            $cte_condition_arr[] = 'o.order_no like :order_no';
            $bind['order_no'] = '%'.$crt->order_no.'%';
        }

        if($crt->trade_no)
        {
            $cte_condition_arr[] = 'o.trade_no like :trade_no';
            $bind['trade_no'] = '%'.$crt->trade_no.'%';
        }

        if($crt->refund_state == '1')
        {
            $cte_condition_arr[] = 'o.refund_state is null';
        }
        elseif($crt->refund_state == '2')
        {
            $cte_condition_arr[] = "o.refund_state = 'REFUND_PART'";
        }
        elseif($crt->refund_state == '3')
        {
            $cte_condition_arr[] = "o.refund_state = 'REFUND_FULL'";
        }   

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'o.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_type)
        {
            $cte_condition_arr[] = 'o.pay_type = :pay_type';
            $bind['pay_type'] = $crt->pay_type;
        }

        if($crt->pay_state == 1)
        {
            $cte_condition_arr[] = "(o.pay_state != 'TRADE_SUCCESS' and o.pay_state != 'TRADE_FINISHED' or o.pay_state is null)";
        }
        elseif($crt->pay_state == 2)
        {
            $cte_condition_arr[] = "(o.pay_state = 'TRADE_SUCCESS' or o.pay_state = 'TRADE_FINISHED')";
        }
        elseif($crt->pay_state == 3)
        {
            $cte_condition_arr[] = "(o.pay_state = 'TRADE_CLOSED')";
        }

        if($crt->client_type)
        {
            $cte_condition_arr[] = 'o.client_type = :client_type';
            $bind['client_type'] = $crt->client_type;
        }

        if($crt->mark == 1)
        {
            $cte_condition_arr[] = 'o.mark is null';
        }
        elseif($crt->mark == 2)
        {
            $cte_condition_arr[] = "o.mark = 'PROCESS_SUCCESS'";
        }
        elseif($crt->mark == 3)
        {
            $cte_condition_arr[] = "o.mark = 'PROCESS_FAILED'";
        }

        if($crt->start_date)
        {
            $cte_condition_arr[] = 'o.create_date >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $cte_condition_arr[] = 'o.create_date <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->hphm)
        {
            $cte_condition_arr[] = 'o2i.hphm like :hphm';
            $bind['hphm'] = '%'.$crt->hphm.'%';
        }


        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            //rownum 从 1 开始计数, $from 要 加 1
            $from = $page_size * ( $page_num - 1) + 1;
            $bind['from'] = $from;
            $bind['to'] = $from + $page_size - 1 ;
        }

        $sql = <<<SQL
        with ORDER_CTE as (
            select o.id, o.order_no, o.trade_no, o.user_id, o.client_type, o2i.hphm, o2i.[count] as illegal_num, o2i.sum_fkje, o.pay_type, o.order_fee, o.pay_state, o.pay_time, o.mark, o.create_date, o.mark_time, o.fail_reason, u.phone, u.uname as user_name,
            refund_state, refund_fee, poundage, ( o2i.[count]*10 - poundage) as real_income, des,
            ROW_NUMBER() over(order by o.create_date desc) as rownum
            from (
                 select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, clientType as client_type,
                 payType as pay_type,
                 money as order_fee, state as pay_state,
                 refundState as refund_state, isnull(refundFee, 0.00) as refund_fee, isnull(poundage, 0.00) as poundage, des,
                 mark, failReason as fail_reason,
                 convert(varchar(20), markTime, 20) as mark_time,
                 convert(varchar(20), createTime, 20) as create_date,
                 convert(varchar(20), payTime, 20) as pay_time
                from PayList
                where orderType = 'illegal'
            ) o
            left join (
                select orderId as order_id, count(id) as [count], max(hphm) as hphm, sum(fkje) as sum_fkje
                from OrderToIllegal
                group by orderId
            ) o2i on o2i.order_id = o.id
            left join IAM_USER u on u.userId = o.user_id
            $cte_condition_str
        )
        select * from ORDER_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取违章代缴订单总数
     * @param array|null $criteria
     * @return mixed
     */
    public static function getIllegalOrderCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';

        $bind = array();

        if($crt->user_id)
        {
            $condition_arr[] = 'o.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_type)
        {
            $condition_arr[] = 'o.pay_type = :pay_type';
            $bind['pay_type'] = $crt->pay_type;
        }

        if($crt->pay_state == 1)
        {
            $condition_arr[] = "(o.pay_state != 'TRADE_SUCCESS' and o.pay_state != 'TRADE_FINISHED' or o.pay_state is null)";
        }
        elseif($crt->pay_state == 2)
        {
            $condition_arr[] = "(o.pay_state = 'TRADE_SUCCESS' or o.pay_state = 'TRADE_FINISHED')";
        }
        elseif($crt->pay_state == 3)
        {
            $condition_arr[] = "(o.pay_state = 'TRADE_CLOSED')";
        }

        if($crt->client_type)
        {
            $condition_arr[] = 'o.client_type = :client_type';
            $bind['client_type'] = $crt->client_type;
        }

        if($crt->mark == 1)
        {
            $condition_arr[] = 'o.mark is null';
        }
        elseif($crt->mark == 2)
        {
            $condition_arr[] = "o.mark = 'PROCESS_SUCCESS'";
        }
        elseif($crt->mark == 3)
        {
            $condition_arr[] = "o.mark = 'PROCESS_FAILED'";
        }

        if($crt->start_date)
        {
            $condition_arr[] = 'o.create_date >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $condition_arr[] = 'o.create_date <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->phone)
        {
            $condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->hphm)
        {
            $condition_arr[] = 'o2i.hphm like :hphm';
            $bind['hphm'] = '%'.$crt->hphm.'%';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(o.id)
            from (
                 select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, payType as pay_type,
                 money as order_fee, state as pay_state, mark, clientType as client_type,
                 createTime as create_date,
                 payTime as pay_time
                from PayList
                where orderType = 'illegal'
            ) o
            left join (
                select orderId as order_id, max(hphm) as hphm
                from OrderToIllegal
                group by orderId
            ) o2i on o2i.order_id = o.id
            left join IAM_USER u on u.userId = o.user_id
            $condition_str
SQL;

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取指定ID的违章代缴订单信息
     * @param $order_id
     * @return object
     */
    public static function getIllegalOrderInfoById($order_id)
    {
        $sql = <<<SQL
        select o.id, o.order_no, o.trade_no, o.user_id, o2i.hphm, o2i.[count] as illegal_num, o2i.sum_fkje, o.pay_type, o.order_fee, o.pay_state, o.pay_time, o.mark, o.fail_reason, o.des, o.create_date, di.license_no, di.archieve_no as archive_no, di.paper_id, di.id as driver_info_id, ci.id as car_info_id, isnull(ci.engineNumber, di.fdjh) as engine_no,
        u.phone, u.uname as user_name,
        ROW_NUMBER() over(order by o.create_date desc) as rownum
        from (
            select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, payType as pay_type,
            money as order_fee, state as pay_state,
            mark, failReason as fail_reason,
            createTime as create_date,
            payTime as pay_time,
            des,
            infoId as info_id,
            carInfoId as car_info_id
            from PayList
            where orderType = 'illegal'
        ) o
        left join (
            select orderId as order_id, count(id) as [count], max(hphm) as hphm, sum(fkje) as sum_fkje
            from OrderToIllegal
            group by orderId
        ) o2i on o2i.order_id = o.id
        left join DriverInfo di on di.id = o.info_id
        left join CarInfo ci on ci.id = o.car_info_id
        left join IAM_USER u on u.userId = o.user_id
        where o.id = :order_id
SQL;
        $bind = array('order_id' => $order_id);

        return self::fetchOne($sql, $bind);
    }

    /**
     * 获取违章订单用户信息列表
     * @param  array|null $criteria
     * @param  int        $page_num
     * @param  int        $page_size
     * @return array
     */
    public static function getIllegalOrderUserList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        $bind = array();

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'ou.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_order_num or $crt->pay_order_num === '0' or $crt->pay_order_num === 0)
        {
            $cte_condition_arr[] = 'ou.pay_order_num = :pay_order_num';
            $bind['pay_order_num'] = $crt->pay_order_num;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            //rownum 从 1 开始计数, $from 要 加 1
            $from = $page_size * ( $page_num - 1) + 1;
            $bind['from'] = $from;
            $bind['to'] = $from + $page_size - 1 ;
        }

        $sql = <<<SQL
        with USER_CTE as (
            select ou.user_id, ou.order_num, ou.pay_order_num, ou.processed_illegal_num,
            row_number() over(order by ou.order_num desc) as rownum
            from
            (
                select o.userId as user_id,
                count(1) as order_num,
                isnull(count(case when o.state = 'TRADE_SUCCESS' or o.state = 'TRADE_FINISHED' then 1 end), 0) as pay_order_num,
                isnull(sum(case when o.mark = 'PROCESS_SUCCESS' then o2i.illegal_num end), 0) as processed_illegal_num
                from PayList o
                left join (
                    select orderId as order_id, count(id) as illegal_num
                    from OrderToIllegal group by orderId
                ) o2i on o2i.order_id = o.id
                where o.orderType = 'illegal'
                group by o.userId
            ) ou
            $cte_condition_str
        )
        select ou_cte.user_id, ou_cte.order_num, ou_cte.pay_order_num, ou_cte.processed_illegal_num, rownum, convert(varchar(20), u.createDate, 23) as create_date, u.phone, u.uname as user_name
        from USER_CTE ou_cte
        left join IAM_USER u on u.userid = ou_cte.user_id        
        $page_condition_str
SQL;
        
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取违章订单用户信息总数
     * @param  array|null $criteria
     * @return int
     */
    public static function getIllegalOrderUserCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';
        $bind = array();

        if($crt->user_id)
        {
            $condition_arr[] = 'ou.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_order_num or $crt->pay_order_num === '0' or $crt->pay_order_num === 0)
        {
            $condition_arr[] = 'ou.pay_order_num = :pay_order_num';
            $bind['pay_order_num'] = $crt->pay_order_num;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1)
        from
        (
            select o.userId as user_id,
            count(1) as order_num,
            isnull(count(case when o.state = 'TRADE_SUCCESS' or o.state = 'TRADE_FINISHED' then 1 end), 0) as pay_order_num,
            isnull(sum(case when o.mark = 'PROCESS_SUCCESS' then o2i.illegal_num end), 0) as processed_illegal_num
            from PayList o
            left join (
                select orderId as order_id, count(id) as illegal_num
                from OrderToIllegal group by orderId
            ) o2i on o2i.order_id = o.id
            where o.orderType = 'illegal'
            group by o.userId
        ) ou
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取订单客户端类型列表
     * @return array
     */
    public static function getOrderClientTypeList()
    {
        $sql = 'select clientType as client_type from PayList group by clientType order by clientType';
        $result = self::nativeQuery($sql);
        $client_types = array();

        foreach($result as $row)
        {
            $client_types[] = $row['client_type'];
        }

        return $client_types;
    }

    /**
     * 获取违法代缴订单违法条目
     * @param $order_id
     * @return array
     */
    public static function getIllegalOrderItems($order_id)
    {
        $sql = 'select des, wfjfs, fkje, hphm, znj from OrderToIllegal where orderId = :order_id';
        $bind = array('order_id' => $order_id);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 更新订单
     * @param $id
     * @param array|null $criteria
     * @return bool
     */
    public static function updateOrder($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);

        $field_str = '';

        $bind = array('id' => $id);

        if($crt->mark)
        {
            $field_str .= 'mark = :mark, markTime = :mark_time, ';
            $bind['mark'] = $crt->mark;
            $bind['mark_time'] = date('Y-m-d H:i:s');
        }

        if($crt->refund_state == 'REFUND_PART' or $crt->refund_state == 'REFUND_FULL')
        {
            $field_str .= 'refundState = :refund_state, ';
            $bind['refund_state'] = $crt->refund_state;
        }
        elseif($crt->refund_state == 'REFUND_NO')
        {
            $field_str .= 'refundState = null, ';
        }

        if($crt->refund_fee or is_float($crt->refund_state))
        {
            $field_str .= 'refundFee = :refund_fee, ';
            $bind['refund_fee'] = $crt->refund_fee;
        }

        if($crt->des)
        {
            $field_str .= 'des = :des, ';
            $bind['des'] = $crt->des;
        }

        if($crt->fail_reason)
        {
            $field_str .= 'failReason = :fail_reason, ';
            $bind['fail_reason'] = $crt->fail_reason;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update PayList set $field_str where id = :id";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取指定ID活动订单信息
     * @param  string|int $order_id
     * @return array
     */
    public static function getActivityOrderInfoById($order_id)
    {
        $sql = <<<SQL
        select o.orderNo as order_no, o.tradeNo as trade_no, convert(varchar(20), o.createTime, 20) as create_date,
        convert(varchar(20), o.payTime, 20) as pay_time, o.payType as pay_type, o.state as pay_state, o.money as order_fee, o.userId as user_id, u.uname as user_name, u.phone
        from PayList o
        left join IAM_USER u on u.userid = o.userId
        where o.id = :order_id
SQL;
        $bind = array(
            'order_id' => $order_id
        );

        return self::fetchOne($sql, $bind);
    }

    /**
     * 获取指定ID活动订单付款项目
     * @param  string|int $order_id
     * @return array
     */
    public static function getActivityOrderItems($order_id)
    {
        $sql = <<<SQL
        select o2g.number, o2g.price, g.name from OrderToGoods o2g
        left join Hui_Goods g on g.id = o2g.goods_id
        where o2g.order_id = :order_id
SQL;
        $bind = array('order_id' => $order_id);
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取挪车订单列表
     * @param array $criteria
     * @param $page_num
     * @param $page_size
     * @return array
     */
    public static function getMoveCarOrderList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        $bind = array();

        if($crt->order_no)
        {
            $cte_condition_arr[] = 'o.order_no like :order_no';
            $bind['order_no'] = '%'.$crt->order_no.'%';
        }

        if($crt->trade_no)
        {
            $cte_condition_arr[] = 'o.trade_no like :trade_no';
            $bind['trade_no'] = '%'.$crt->trade_no.'%';
        }

        if($crt->refund_state == '1')
        {
            $cte_condition_arr[] = 'o.refund_state is null';
        }
        elseif($crt->refund_state == '2')
        {
            $cte_condition_arr[] = "o.refund_state = 'REFUND_PART'";
        }
        elseif($crt->refund_state == '3')
        {
            $cte_condition_arr[] = "o.refund_state = 'REFUND_FULL'";
        }

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'o.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_type)
        {
            $cte_condition_arr[] = 'o.pay_type = :pay_type';
            $bind['pay_type'] = $crt->pay_type;
        }

        if($crt->pay_state == 1)
        {
            $cte_condition_arr[] = "(o.pay_state != 'TRADE_SUCCESS' and o.pay_state != 'TRADE_FINISHED' or o.pay_state is null)";
        }
        elseif($crt->pay_state == 2)
        {
            $cte_condition_arr[] = "(o.pay_state = 'TRADE_SUCCESS' or o.pay_state = 'TRADE_FINISHED')";
        }
        elseif($crt->pay_state == 3)
        {
            $cte_condition_arr[] = "(o.pay_state = 'TRADE_CLOSED')";
        }
        elseif($crt->pay_state == 4)
        {
            $cte_condition_arr[] = "o.pay_state = 'ORDER_FREE'";
        }

        if($crt->client_type)
        {
            $cte_condition_arr[] = 'o.client_type = :client_type';
            $bind['client_type'] = $crt->client_type;
        }

        if($crt->order_no)
        {
            $cte_condition_arr[] = 'o.orderNo = :order_no';
            $bind['order_no'] = $crt->order_no;
        }

        if($crt->trade_no)
        {
            $cte_condition_arr[] = 'o.tradeNo = :trade_no';
            $bind['trade_no'] = $crt->trade_no;
        }

        if($crt->appeal_state or $crt->appeal_state === 0 or $crt->appeal_state === '0')
        {
            $cte_condition_arr[] = "ma.state = :appeal_state";
            $bind['appeal_state'] = $crt->appeal_state;
        }

        if($crt->is_link === 0 or $crt->is_link === '0' )
        {
            $cte_condition_arr[] = '( mcc.called_duration_total <= 0 or mcc.called_duration_total is null )';
        }
        elseif($crt->is_link == 1)
        {
            $cte_condition_arr[] = 'mcc.called_duration_total > 0';
        }

        if($crt->is_feedbacked === '0' or $crt->is_feedbacked === 0)
        {
            $cte_condition_arr[] = 'o2mc.feedback_date is null';
        }
        elseif($crt->is_feedbacked == 1)
        {
            $cte_condition_arr[] = 'o2mc.feedback_date is not null';
        }

        if($crt->start_date)
        {
            $cte_condition_arr[] = 'convert(varchar(10), o.create_date, 23) >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $cte_condition_arr[] = 'convert(varchar(10), o.create_date, 23) <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->hphm)
        {
            $cte_condition_arr[] = 'o2mc.hphm like :hphm';
            $bind['hphm'] = '%'.$crt->hphm.'%';
        }


        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            //rownum 从 1 开始计数, $from 要 加 1
            $from = $page_size * ( $page_num - 1) + 1;
            $bind['from'] = $from;
            $bind['to'] = $from + $page_size - 1 ;
        }

        $sql = <<<SQL
        with ORDER_CTE as (
            select o.id, o.order_no, o.trade_no, o.user_id, o.client_type,
            o2mc.source, o2mc.hphm, o2mc.price as origin_price, o2mc.phone_bill,
            convert(varchar(20), o2mc.feedback_date, 20) as feedback_date,
            convert(varchar(20), o2mc.last_call_time, 20) as last_call_time,
            o.pay_type, o.order_fee, o.pay_state, o.pay_time, o.mark, o.create_date, o.mark_time, o.fail_reason, isnull(mu.phone, u.phone) as phone, u.uname as user_name,
            refund_state, refund_fee, poundage, des,
            convert(varchar(20), ma.create_date, 20) as appeal_date,
            ma.state as appeal_state,
            ma.process_des as appeal_process_des,
            mcc.call_count, mcc.bill,
            case
              when mcc.called_duration_total > 0 then
                1
              else
                0
            end is_link,
            ROW_NUMBER() over(order by o.create_date desc) as rownum
            from (
                 select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, clientType as client_type,
                 payType as pay_type,
                 money as order_fee, state as pay_state,
                 refundState as refund_state, isnull(refundFee, 0.00) as refund_fee, isnull(poundage, 0.00) as poundage, des,
                 mark, failReason as fail_reason,
                 convert(varchar(20), markTime, 20) as mark_time,
                 convert(varchar(20), createTime, 20) as create_date,
                 convert(varchar(20), payTime, 20) as pay_time
                from PayList
                where orderType = 'move_car'
            ) o
            left join OrderToMoveCar o2mc on o2mc.order_id = o.id
            left join IAM_USER u on u.userId = o.user_id
            left join MC_User mu on mu.user_id = o.user_id
            left join MC_Appeal ma on ma.order_id = o.id
            left join (
              select order_id as order_id, count(id) as call_count, (ceiling((sum(caller_duration) + sum(called_duration)) / 60.00) * 0.12) as bill, sum(called_duration) as called_duration_total from MC_CallRecord
              group by order_id
            ) mcc on mcc.order_id = o.id
            $cte_condition_str
        )
        select * from ORDER_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取挪车订单总数
     * @param array $criteria
     * @return mixed
     */
    public static function getMoveCarOrderTotal(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';

        $bind = array();

        if($crt->user_id)
        {
            $condition_arr[] = 'o.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->pay_type)
        {
            $condition_arr[] = 'o.pay_type = :pay_type';
            $bind['pay_type'] = $crt->pay_type;
        }

        if($crt->pay_state == 1)
        {
            $condition_arr[] = "(o.pay_state != 'TRADE_SUCCESS' and o.pay_state != 'TRADE_FINISHED' or o.pay_state is null)";
        }
        elseif($crt->pay_state == 2)
        {
            $condition_arr[] = "(o.pay_state = 'TRADE_SUCCESS' or o.pay_state = 'TRADE_FINISHED')";
        }
        elseif($crt->pay_state == 3)
        {
            $condition_arr[] = "(o.pay_state = 'TRADE_CLOSED')";
        }
        elseif($crt->pay_state == 4)
        {
            $condition_arr[] = "o.pay_state = 'ORDER_FREE'";
        }

        if($crt->client_type)
        {
            $condition_arr[] = 'o.client_type = :client_type';
            $bind['client_type'] = $crt->client_type;
        }

        if($crt->order_no)
        {
            $condition_arr[] = 'o.orderNo = :order_no';
            $bind['order_no'] = $crt->order_no;
        }

        if($crt->trade_no)
        {
            $condition_arr[] = 'o.tradeNo = :trade_no';
            $bind['trade_no'] = $crt->trade_no;
        }

        if($crt->appeal_state and $crt->appeal_state === 0 or $crt->appeal_state === '0')
        {
            $condition_arr[] = "ma.state = :appeal_state";
            $bind['appeal_state'] = $crt->appeal_state;
        }

        if($crt->is_link === 0 or $crt->is_link === '0' )
        {
            $condition_arr[] = '( mcc.called_duration_total <= 0 or mcc.called_duration_total is null )';
        }
        elseif($crt->is_link == 1)
        {
            $condition_arr[] = 'mcc.called_duration_total > 0';
        }

        if($crt->is_feedbacked === '0' or $crt->is_feedbacked === 0)
        {
            $condition_arr[] = 'o2mc.feedback_date is null';
        }
        elseif($crt->is_feedbacked == 1)
        {
            $condition_arr[] = 'o2mc.feedback_date is not null';
        }

        if($crt->start_date)
        {
            $condition_arr[] = 'convert(varchar(10), o.create_date, 23) >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $condition_arr[] = 'convert(varchar(10), o.create_date, 23) <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->phone)
        {
            $condition_arr[] = 'u.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->hphm)
        {
            $condition_arr[] = 'o2mc.hphm like :hphm';
            $bind['hphm'] = '%'.$crt->hphm.'%';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(o.id)
            from (
                 select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, payType as pay_type,
                 money as order_fee, state as pay_state, mark, clientType as client_type,
                 createTime as create_date,
                 payTime as pay_time
                from PayList
                where orderType = 'move_car'
            ) o
            left join OrderToMoveCar o2mc on  o2mc.order_id = o.id
            left join IAM_USER u on u.userId = o.user_id
            left join MC_Appeal ma on ma.order_id = o.id
            left join (
              select order_id as order_id, count(id) as call_count, (ceiling((sum(caller_duration) + sum(called_duration)) / 60.00) * 0.12) as bill, sum(called_duration) as called_duration_total from MC_CallRecord
              group by order_id
            ) mcc on mcc.order_id = o.id
            $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取指定ID挪车订单信息
     * @param $order_id
     * @return array
     */
    public static function getMoveCarOrderById($order_id)
    {
        $sql = <<<SQL
    select o.id, o.userId as user_id, o.orderNo as order_no, o.orderType as order_type, o.money as total_fee, o.relId as rel_id, o.ticket_id, convert(varchar(20), o.createTime, 20) as create_date,
        o.state as pay_state,
        o.payType as pay_type,
        o.tradeNo as trade_no,
        convert(varchar(20), o.payTime, 20) pay_time,
        convert(varchar(20), dateadd(hh, 2, o.createTime), 20) as expire_date,
        case
          when getdate() >= dateadd(hh, 2, o.createTime) then
           1
          else
           0
        end as is_expired,
        u.uname,
        isnull(mu.phone, u.phone) as phone
     from PayList as o
     left join IAM_USER u on u.userid = o.userId
     left join MC_User mu on mu.user_id = o.userId
     where o.id = :order_id
SQL;
        $bind = array('order_id' => $order_id);
        $order = self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);

        $get_record_sql = "select hphm, price, phone_bill, uphone, source, convert(varchar(20),last_call_time, 20) as last_call_time from OrderToMoveCar where order_id = :order_id";
        $get_record_bind = array('order_id' => $order['id']);
        $record = self::fetchOne($get_record_sql, $get_record_bind, null, Db::FETCH_ASSOC);
        $order['record'] = $record;

        if($order['ticket_id'])
        {
            //获取订单所使用的票券信息
            $ticket = Ticket::getTicketById($order['ticket_id']);
            $order['ticket'] = $ticket;
        }

        return $order;
    }
}