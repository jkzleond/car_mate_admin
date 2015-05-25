<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-24
 * Time: 下午6:50
 */

use \Phalcon\Db;

class Deal extends ModelEx
{
    /**
     * 获取订单列表
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getDealList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $cte_condition = array('ISNULL(e.abandon, 0) != 1');
        $cte_condition_str = '';
        $page_condition_str = '';

        $bind = array();

        $crt = new Criteria($criteria);


        if($crt->user_id)
        {
            $cte_condition[] = 'e.userid like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->state or $crt->state === '0' or $crt->state === 0)
        {
            $cte_condition[] = 'e.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->visual or $crt->visual === '0' or $crt->visual === 0)
        {
            $cte_condition[] = 't.visual = :visual';
            $bind['visual'] = $crt->visual;
        }

        if(!empty($cte_condition))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition);
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
        WITH ITEM_CTE AS(select e.*, i.name as itemName, goldPrice,
		ISNULL(a.province,'') AS province, ISNULL(a.city,'') AS city, ISNULL(a.area,'')
		AS area, ISNULL(a.address,'') AS address, ISNULL(a.phone,'') AS phone,
		ISNULL (a.trueName, '') as trueName, ROW_NUMBER() over (order by e.id desc) as rownum,
		ISNULL(c.name,'') comName, t.visual
		from Hui_ItemExchange e left join IAM_USERADDRESS a on e.addressId=a.id
		left join Hui_Item i on i.id=e.itemId
		left join Hui_ItemDetail d on d.itemId=e.itemId
		left join Hui_ItemType t on t.id=d.typeId
		left join LogisticsCompany c on c.id=e.comId
        $cte_condition_str
		)
		select * from ITEM_CTE
        $page_condition_str
SQL;

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取订单总数
     * @param array $criteria
     */
    public static function getDealCount(array $criteria=null)
    {
        $condition_arr = array('ISNULL(e.abandon, 0) != 1');
        $condition_str = '';
        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->user_id)
        {
            $condition_arr[] = 'e.userid like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->state or $crt->state === '0' or $crt->state === 0)
        {
            $condition_arr[] = 'e.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->visual or $crt->visual === '0' or $crt->visual === 0)
        {
            $condition_arr[] = 't.visual = :visual';
            $bind['visual'] = $crt->visual;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }


        $sql = <<<SQL
        select count(*) from Hui_ItemExchange e
		left join Hui_Item i on i.id=e.itemId
		left join Hui_ItemDetail d on d.itemId=e.itemId
		left join Hui_ItemType t on t.id=d.typeId
		$condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 订单发货
     * @param $id
     * @param $comp_id
     * @param null $order_no
     * @return bool
     */
    public static function deliver($id, $comp_id=null, $order_no=null)
    {
        $field_str = 'state = :state, ';
        $bind = array('id' => $id);

        if($order_no)
        {
            $field_str .= 'comId = :comp_id, orderNo = :order_no, ';
            $bind['comp_id'] = $comp_id;
            $bind['order_no'] = $order_no;
            $bind['state'] = 1;
        }
        else
        {
            $bind['state'] = 2;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update Hui_ItemExchange set $field_str where id = :id";
        return self::nativeExecute($sql, $bind);
    }
}