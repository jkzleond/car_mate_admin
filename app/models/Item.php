<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-21
 * Time: 上午11:00
 */

use \Phalcon\Db;

class Item extends ModelEx
{
    /**
     * 获取商品信息列表
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getItemDetailList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $cte_condition = array('ISNULL(i.abandon, 0) != 1');
        $cte_condition_str = '';
        $page_condition = '';

        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->name)
        {
            $cte_condition[] = 'i.name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if($crt->type_id > 3)
        {
            $cte_condition[] = 'd.typeId = :type_id';
            $bind['type_id'] = $crt->type_id;
        }
        elseif($crt->type_id == 3)
        {
            $cte_condition[] = 't.visual = 0';
        }
        elseif($crt->type_id == 2)
        {
            $cte_condition[] = 't.visual = 1';
        }

        if($crt->visual or $crt->visual === '0' or $crt->visual === 0)
        {
            $cte_condition[] = 't.visual = :visual';
            $bind['visual'] = $crt->visual;
        }

        if($crt->state and $crt->state > 0)
        {
            $cte_condition[] = 'i.state = :state';
            $bind['state'] = $crt->state;
        }

        if(!empty($cte_condition))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition);
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
        WITH ITEM_CTE AS(
          select i.*,ROW_NUMBER() over (order by i.id desc) as rownum, t.id as typeId,
		  t.name as typeName, d.pic, d.contents, d.id as did, d.itemId, t.visual
		  from Hui_Item i
		  left join Hui_ItemDetail d on d.itemId=i.id
		  left join Hui_ItemType t on d.typeId=t.id
          $cte_condition_str
		) select * from ITEM_CTE
		$page_condition
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取商品总数
     * @param array $criteria
     * @return mixed
     */
    public static function getItemCount(array $criteria=null)
    {
        $condition = array('ISNULL(i.abandon, 0) != 1');
        $condition_str = '';

        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->name)
        {
            $condition[] = 'i.name like :name';
            $bind['name'] = $crt->name;
        }

        if($crt->type_id > 3)
        {
            $condition[] = 'd.typeId = :type_id';
            $bind['type_id'] = $crt->type_id;
        }
        elseif($crt->type_id == 3)
        {
            $condition[] = 't.visual = 0';
        }
        elseif($crt->type_id == 2)
        {
            $condition[] = 't.visual = 1';
        }

        if($crt->visual or $crt->visual === '0' or $crt->visual === 0)
        {
            $condition[] = 't.visual = :visual';
            $bind['visual'] = $crt->visual;
        }

        if($crt->state and $crt->state > 0)
        {
            $condition[] = 'i.state = :state';
            $bind['state'] = $crt->state;
        }

        if(!empty($condition))
        {
            $condition_str = 'where '.implode(' and ', $condition);
        }


        $sql = <<<SQL
        select count(*) from Hui_Item i
		left join Hui_ItemDetail d on d.itemId=i.id
		left join Hui_ItemType t on d.typeId=t.id
		$condition_str
SQL;

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取商品分类列表
     * @param $page_num
     * @param $page_size
     * @return array
     */
    public static function getTypeList($page_num=null, $page_size=null)
    {
        $page_condition = '';
        $bind = array();

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        with ITEM_TYPE_CTE as (
          select *, ROW_NUMBER() OVER( order by id asc) as rownum from Hui_ItemType
        )
        select * from ITEM_TYPE_CTE
        $page_condition
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取商品分类总数
     * @return mixed
     */
    public static function getTypeCount()
    {
        $sql = 'select count(id) from Hui_ItemType';

        $result = self::fetchOne($sql, null, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 活取商品对换信息列表
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getExchangeList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $cte_condition_arr = array('ISNULL(e.abandon, 0) != 1');
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        $crt = new Criteria($criteria);

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'e.userid like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->state or $crt->state === 0 or $crt->state === '0')
        {
            $cte_condition_arr[] = 'e.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->item_id)
        {
            $cte_condition_arr[] = 'e.itemId = :item_id';
            $bind['item_id'] = $crt->item_id;
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
        WITH ITEM_CTE AS(select e.id, e.userid, itemId, exchangeDate, state,
		ISNULL(a.province,'') AS province, ISNULL(a.city,'') AS city, ISNULL(a.area,'')
		AS area, ISNULL(a.address,'') AS address, ISNULL(a.phone,'') AS phone,
		ISNULL (a.trueName, '') as trueName, ROW_NUMBER() over (order by e.id desc) as rownum
		from Hui_ItemExchange e left join IAM_USERADDRESS a
		on e.addressId=a.id
		$cte_condition_str
		)
		select * from ITEM_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 活取商品对换信息总数
     * @param array $criteria
     * @return mixed
     */
    public static function getExchangeCount(array $criteria=null)
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

        if($crt->state or $crt->state === 0 or $crt->state === '0')
        {
            $condition_arr[] = 'e.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->item_id)
        {
            $condition_arr[] = 'e.itemId = :item_id';
            $bind['item_id'] = $crt->item_id;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(*) from Hui_ItemExchange e
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 添加商品
     * @param null $name
     * @param null $real_price
     * @param null $gold_price
     * @param null $num
     * @param null $pic_data
     * @param null $contents
     * @param null $type_id
     * @return bool
     */
    public static function addItem($name=null, $real_price=null, $gold_price=null, $num=null, $pic_data=null, $contents=null, $type_id=null)
    {
        $item_sql = 'insert into Hui_Item ([name], realPrice, goldPrice, state, num, exchangeNum) values(:name, :real_price, :gold_price, 1, :num, 0)';
        $item_bind = array(
            'name' => $name,
            'real_price' => $real_price,
            'gold_price' => $gold_price,
            'num' => $num
        );
        $connection = self::_getConnection();
        $connection->begin();

        $item_success = $connection->execute($item_sql, $item_bind);

        if($item_success)
        {
            $item_id = $connection->lastInsertId();
            $detail_sql = 'insert into Hui_ItemDetail (itemId, pic, contents, typeId) values (:item_id, :pic_data, :contents, :type_id)';
            $detail_bind = array(
                'item_id' => $item_id,
                'pic_data' => $pic_data,
                'contents' => $contents,
                'type_id' => $type_id
            );
            $detail_success = $connection->execute($detail_sql, $detail_bind);
            if(!$detail_success)
            {
                $connection->rollback();
                return false;
            }
            return $connection->commit();
        }
        else
        {
            $connection->rollback();
            return false;
        }
    }

    /**
     * 更新商品信息
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateItem($id, array $criteria=null)
    {
        $item_field_str = '';
        $item_bind = array('id' => $id);
        $detail_field_str = '';
        $detail_bind = array('item_id' => $id);

        $crt = new Criteria($criteria);

        if($crt->name)
        {
            $item_field_str .= '[name] = :name, ';
            $item_bind['name'] = $crt->name;
        }

        if($crt->real_price)
        {
            $item_field_str .= 'realPrice = :real_price, ';
            $item_bind['real_price'] = $crt->real_price;
        }

        if($crt->gold_price)
        {
            $item_field_str .= 'goldPrice = :gold_price, ';
            $item_bind['gold_price'] = $crt->gold_price;
        }

        if($crt->state)
        {
            $item_field_str .= 'state = :state, ';
            $item_bind['state'] = $crt->state;
        }

        if($crt->num)
        {
            $item_field_str .= 'num = :num, ';
            $item_bind['num'] = $crt->num;
        }

        if($crt->contents)
        {
            $detail_field_str .= 'contents = :contents, ';
            $detail_bind['contents'] = $crt->contents;
        }

        if($crt->type_id)
        {
            $detail_field_str .= 'typeId = :type_id, ';
            $detail_bind['type_id'] = $crt->type_id;
        }

        if($crt->pic_data)
        {
            $detail_field_str .= 'pic = :pic_data, ';
            $detail_bind['pic_data'] = $crt->pic_data;
        }

        $success = true;

        if($item_field_str)
        {
            $item_field_str = rtrim($item_field_str, ', ');
            $item_sql = "update Hui_Item set $item_field_str where id = :id";
            $item_success = self::nativeExecute($item_sql, $item_bind);
            $success = $success and $item_success;
        }

        if($detail_field_str)
        {
            $detail_field_str = rtrim($detail_field_str, ', ');
            $detail_sql = "update Hui_ItemDetail set $detail_field_str where itemId = :item_id";
            $detail_success = self::nativeExecute($detail_sql, $detail_bind);
            $success = $success and $detail_success;
        }

        return $success;
    }

    /**
     * 批量上架商品
     * @param array $ids
     * @return bool
     */
    public static function onShelfItemBatchById(array $ids)
    {
        $ids_string = '';
        $bind = array();

        foreach($ids as $index => $id)
        {
            $ids_string .= ':id'.$index.', ';
            $bind['id'.$index] = $id;
        }

        $ids_string = rtrim($ids_string, ', ');

        $sql = "update Hui_Item set onShelf = 1, lastModifiedTime = getDate() where id in ($ids_string)";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 批量下架商品
     * @param array $ids
     * @return bool
     */
    public static function offShelfItemBatchById(array $ids)
    {
        $ids_string = '';
        $bind = array();

        foreach($ids as $index => $id)
        {
            $ids_string .= ':id'.$index.', ';
            $bind['id'.$index] = $id;
        }

        $ids_string = rtrim($ids_string, ', ');

        $sql = "update Hui_Item set onShelf = 0, lastModifiedTime = getDate() where id in ($ids_string)";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除商品(支持批量)
     * @param array $ids
     * @return bool
     */
    public static function delItem(array $ids)
    {
        $ids_string = '';
        $bind = array();

        foreach($ids as $index => $id)
        {
            $ids_string .= ':id'.$index.', ';
            $bind['id'.$index] = $id;
        }

        $ids_string = rtrim($ids_string, ', ');

        $sql = "update Hui_Item set abandon = 1, lastModifiedTime = getDate() where id in ($ids_string)";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加商品类目
     * @param $name
     * @param $visual
     * @return bool
     */
    public static function addItemType($name, $visual)
    {
        $sql = 'insert into Hui_ItemType ([name], visual) values (:name, :visual)';
        $bind = array(
            'name' => $name,
            'visual' => $visual
        );
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除商品类目
     * @param $id
     * @return bool
     */
    public static function delItemType($id)
    {
        $sql = 'delete from Hui_ItemType where id = :id';
        $bind = array(
            'id' => $id
        );
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新商品类目
     * @param id
     * @param $name
     * @param $visual
     * @return bool
     */
    public static function updateItemType($id, $name=null, $visual=null)
    {
        $field_str = '';
        $bind = array('id' => $id);

        if($name)
        {
            $field_str .= 'name = :name, ';
            $bind['name'] = $name;
        }

        if($visual or $visual === 0 or $visual === '0')
        {
            $field_str .= 'visual = :visual, ';
            $bind['visual'] = $visual;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update Hui_ItemType set $field_str where id = :id";
        return self::nativeExecute($sql, $bind);
    }
}