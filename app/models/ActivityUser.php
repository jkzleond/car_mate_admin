<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-4
 * Time: 下午9:11
 */

use \Phalcon\Db;

class ActivityUser extends ModelEx
{
    /**
     * 根据条件构造 CTE sql 部分
     * @param Criteria $crit
     * @return array [0] 为 sql, [1] 为 bind
     */
    protected static function _buildCTE(Criteria $crit=null)
    {
        if(!$crit)
        {
            $criteria = new Criteria();
        }
        else
        {
            $criteria = $crit;
        }

        $order_column = $criteria->order_column ? $criteria->order_column : 'id';
        $cte_condition_arr = array('ISNULL(u.abandon, 0) != 1');
        $bind = array();
        $cte_condition = '';

        $user_id = $criteria->user_id;
        $uname = $criteria->uname;
        $id_card_no = $criteria->id_card_no;
        $phone = $criteria->phone;
        $qq_num = $criteria->qq_num;
        $aid = $criteria->aid;
        $state = $criteria->state;
        $pay_state = $criteria->pay_state;
        $pay_type = $criteria->pay_type;
        $is_noticed = $criteria->is_noticed;
        $selected = $criteria->selected;

        if($user_id || $user_id === '0' || $user_id === 0)
        {
            $cte_condition_arr[] = 'i.userid like :user_id';
            $bind['user_id'] = '%'.$user_id.'%';
        }

        if($uname)
        {
            $cte_condition_arr[] = 'i.uname like :uname';
            $bind['uname'] = '%'.$uname.'%';
        }

        if($id_card_no)
        {
            $cte_condition_arr[] = 'i.idcardno like :id_card_no';
            $bind['id_card_no'] = '%'.$id_card_no.'%';
        }

        if($phone)
        {
            $cte_condition_arr[] = 'i.phone like :phone';
            $bind['phone'] = '%'.$phone.'%';
        }

        if($qq_num)
        {
            $cte_condition_arr[] = 'i.qqNum like :qq_num';
            $bind['qq_num'] = '%'.$qq_num.'%';
        }

        if($aid and $aid != -1)
        {
            $cte_condition_arr[] = 'a.id = :aid';
            $bind['aid'] = $aid;
        }
        /*elseif($aid != -1)
        {
            $cte_condition_arr[] = <<<SQL
              a.id = (
                select top 1 id from Activity
                where state=1 and ISNULL(abandon,0)!=1
                order by STATE ASC, endDate DESC
              )
SQL;
        }*/

        if($state and $state != -1)
        {
            $cte_condition_arr[] = 'u.state = :state';
            $bind['state'] = $state;
        }

        if($pay_state)
        {
            if($pay_state == 1)
            {
                $cte_condition_arr[] = '(u.payState = :pay_state or p.id is not null)';
            }
            else
            {
                $cte_condition_arr[] = '(u.payState = :pay_state and p.id is null)';
            }

            $bind['pay_state'] = $pay_state;
        }

        if($pay_type)
        {
            $cte_condition_arr[] = 'u.payType = :pay_type';
            $bind['pay_type'] = $pay_type;
        }

        if($is_noticed)
        {
            $cte_condition_arr[] = 'u.isNoticed = :is_noticed';
            $bind['is_noticed'] = $is_noticed;
        }

        if($selected)
        {
            $cte_condition_arr[] = 'u.selected = :selected';
            $bind['selected'] = $selected;
        }

        $cte_condition = 'where '.implode(' and ', $cte_condition_arr);

        $sql = <<<SQL
        WITH USER_CTE AS ( select u.*,uname,phone,sex,idcardno,province,city,area,
		address,sinaWeibo,weixin,hphm,hpzl,people,p.id as payId, o.id as orderId, o.payType as orderPayType, o.payTime as orderPayTime, a.name as aname,astate,needPay,
		needNotice, i.qqNum,
		ROW_NUMBER() OVER (ORDER BY u.%s desc) AS rownum from ActivityUser u
		left join ( select
			userid,uname,phone,sex,idcardno,province,city,area,address,sinaWeibo,
			weixin,hphm,hpzl,people,qqNum from IAM_USER_INFO where ISNULL(abandon, 0) !=1
		) i on u.userid = i.userid
		left join ( select id,name,state as astate,needPay,needNotice from Activity
			where ISNULL(abandon,0)!=1
		) a on u.aid=a.id
		left join (select max(id) as id,userId,orderName from PayList where state='TRADE_FINISHED' group by userId, orderName
		) p on p.userId=u.userid and p.orderName=a.name
        left join (
            select id, relId, payType, state, payTime from PayList where orderType = 'activity'    
        ) o on o.relId = u.id
		%s
		)

SQL;

        $sql = sprintf($sql, $order_column, $cte_condition);

        return array($sql, $bind);
    }

    /**
     * 获取参与活动的用户列表
     * @param Criteria $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getActivityUserList(Criteria $criteria=null, $page_num=null, $page_size=null)
    {
        list($sql, $bind) = self::_buildCTE($criteria);

        $sql .= <<<SQL
        select * from USER_CTE
        %s
SQL;

        $page_condition = '';

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = sprintf($sql, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定条件的活动用户总数
     * @param Criteria $criteria
     * @return mixed
     */
    public static function getActivityUserCount(Criteria $criteria=null)
    {
        list($sql, $bind) = self::_buildCTE($criteria);

        $sql .= <<<SQL
        select count(*) from USER_CTE
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 通知用户(批量)
     * @param array $ids
     * @return bool
     */
    public static function noticeUser(array $ids)
    {
        $where_in_values = '';
        $bind = array();
        foreach($ids as $index => $value)
        {
            $param_name = ':id'.$index;
            $where_in_values .= $param_name.', ';
            $bind[$param_name] = $value;
        }

        $where_in_values = rtrim($where_in_values, ', ');

        $sql = 'update dbo.ActivityUser set isNoticed = 1, noticeTime = getDate() where id in (%s) and ( isNoticed != 1 or isNoticed is NULL )';

        $sql = sprintf($sql, $where_in_values);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 领取(批量)
     * @param array $ids
     * @return bool
     */
    public static function userGain(array $ids)
    {
        $where_in_values = '';
        $bind = array();
        foreach($ids as $index => $value)
        {
            $param_name = ':id'.$index;
            $where_in_values .= $param_name.', ';
            $bind[$param_name] = $value;
        }

        $where_in_values = rtrim($where_in_values, ', ');

        $sql = 'update dbo.ActivityUser set state = 1, gainTime = getDate() where id in (%s) and state != 1';
        $sql = sprintf($sql, $where_in_values);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 用户付款
     * @param array $ids
     * @return bool
     */
    public static function userPay(array $ids)
    {
        $where_in_values = '';
        $bind = array();
        foreach($ids as $index => $value)
        {
            $param_name = ':id'.$index;
            $where_in_values .= $param_name.', ';
            $bind[$param_name] = $value;
        }

        $where_in_values = rtrim($where_in_values, ', ');

        $sql = 'update dbo.ActivityUser set payState = 1, payTime = getDate() where id in (%s) and payState != 1';
        $sql = sprintf($sql, $where_in_values);

        return self::nativeExecute($sql, $bind);
    }


}