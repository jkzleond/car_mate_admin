<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-24
 * Time: 下午5:09
 */

use \Phalcon\Db;

class Transaction extends ModelEx
{
    /**
     * 获取流水记录列表
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getTransactionList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $cte_condition_arr = array('ISNULL(t.abandon, 0) != 1');
        $cte_condition_str = '';
        $page_condition_str = '';

        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->user_id)
        {
            $cte_condition_arr[] = 't.userid like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->transaction_type)
        {
            $cte_condition_arr[] = 't.transactionType = :transaction_type';
            $bind['transaction_type'] = $crt->transaction_type;
        }

        if($crt->distribute_type)
        {
            $cte_condition_arr[] = 't.distributeType = :distribute_type';
            $bind['distribute_type'] = $crt->distribute_type;
        }

        if($crt->start_date)
        {
            $cte_condition_arr[] = 't.createDate >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $cte_condition_arr[] = 't.createDate <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
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
        WITH TRANS_CTE AS (
			select t.*, u.uname, u.nickname, ROW_NUMBER() OVER (order by t.id desc) as rownum
			from Hui_TransactionDetail t left join IAM_USER u
			on t.userid=u.userid
            $cte_condition_str
		)
		select * from TRANS_CTE
        $page_condition_str
SQL;

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取流水记录总数
     * @param array $criteria
     */
    public static function getTransactionCount(array $criteria=null)
    {
        $condition_arr = array('ISNULL(t.abandon, 0) != 1');
        $condition_str = '';

        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->user_id)
        {
            $condition_arr[] = 't.userid like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->transaction_type)
        {
            $condition_arr[] = 't.transactionType = :transaction_type';
            $bind['transaction_type'] = $crt->transaction_type;
        }

        if($crt->distribute_type)
        {
            $condition_arr[] = 't.distributeType = :distribute_type';
            $bind['distribute_type'] = $crt->distribute_type;
        }

        if($crt->start_date)
        {
            $condition_arr[] = 't.createDate >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $condition_arr[] = 't.createDate <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = "select count(t.id) from Hui_TransactionDetail t $condition_str";

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取分配类型列表
     * @return array
     */
    public static function getDistributeTypeList()
    {
        $sql = 'select distinct distributeType as [name] from Hui_TransactionDetail group by distributeType';
        return self::nativeQuery($sql);
    }
}