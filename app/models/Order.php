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
        else if($crt->pay_state == 2)
        {
            $cte_condition_arr[] = "(o.pay_state = 'TRADE_SUCCESS' or o.pay_state = 'TRADE_FINISHED')";
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
            select o.id, o.order_no, o.trade_no, o.user_id, o.client_type, o2i.hphm, o2i.[count] as illegal_num, o2i.sum_fkje, o.pay_type, o.order_fee, o.pay_state, o.pay_time, o.mark, o.create_date, o.fail_reason, u.phone, u.uname as user_name,
            refund_state, refund_fee, poundage, (o2i.[count]*10 - poundage) as real_income, des,
            ROW_NUMBER() over(order by o.create_date desc) as rownum
            from (
                 select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, clientType as client_type,
                 payType as pay_type,
                 money as order_fee, state as pay_state,
                 refundState as refund_state, refundFee as refund_fee, poundage, des,
                 mark, failReason as fail_reason,
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
        select o.id, o.order_no, o.trade_no, o.user_id, o2i.hphm, o2i.[count] as illegal_num, o2i.sum_fkje, o.pay_type, o.order_fee, o.pay_state, o.pay_time, o.mark, o.fail_reason, o.create_date, di.license_no, di.archieve_no as archive_no, di.id as driver_info_id, ci.id as car_info_id, isnull(ci.engineNumber, di.fdjh) as engine_no,
        u.phone, u.uname as user_name,
        ROW_NUMBER() over(order by o.create_date desc) as rownum
        from (
            select id, orderNo as order_no, tradeNo as trade_no, userId as user_id, payType as pay_type,
            money as order_fee, state as pay_state,
            mark, failReason as fail_reason,
            createTime as create_date,
            payTime as pay_time,
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
        $sql = 'select des, wfjfs, fkje, hphm from OrderToIllegal where orderId = :order_id';
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
            $field_str .= 'mark = :mark, ';
            $bind['mark'] = $crt->mark;
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
}