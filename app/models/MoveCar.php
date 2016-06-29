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
     * @param $hphm
     * @return array | null
     */
    public static function getCarOwnerList($hphm)
    {
        $get_mc_car_sql = "
          select
            c.id,
            case
              when c.success_count > 0 or c.fail_count > 0 then
                c.success_count / (c.success_count + c.fail_count) * 10
              else
                5
            end as success_rate,
            isnull(mu.phone, u.phone) as phone,
            c.user_id,
            'cm' as source
          from MC_Car c
          left join IAM_USER u on u.userid = c.user_id
          left join MC_User mu on mu.user_id = c.user_id
          where c.hphm = :hphm and c.state = 1
          order by success_rate desc
";
        $get_mc_car_bind = array('hphm' => $hphm);

        $mc_car_list = self::nativeQuery($get_mc_car_sql, $get_mc_car_bind);

        if(!empty($mc_car_list))
        {
            return $mc_car_list;
        }

        $get_jg_car_sql = "select id, phone, 'jg' as source from JGCarOwner where hphm = :hphm";
        $get_jg_car_bind = array('hphm' => $hphm);
        $jg_car_list = self::nativeQuery($get_jg_car_sql, $get_jg_car_bind);
        return $jg_car_list;
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
}
