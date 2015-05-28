<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-15
 * Time: 下午5:15
 */

use \Phalcon\Db;

class Notice extends ModelEx
{
    public static function getNoticeList($province_id=null, $page_num=null, $page_size=null)
    {

        $bind = array();

        $cte_province_condition = '';
        $page_condition = '';

        if($province_id)
        {
            $cte_province_condition = 'where n.provinceId in(:province_id,0)';
            $bind['province_id'] = $province_id;
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
        WITH Notice_CTE AS (
		    SELECT *, ROW_NUMBER() OVER (ORDER BY [publishTime] desc) AS rownum FROM
		    Notice n
		    %s
		),
		LF_CTE as (
		  select * from LocalFavourAdv where [type] = 'Notice'
		)
		select n.id,
		n.title,
		n.contents,
		n.publishTime,
		n.isState,
		n.isOrder,
		n.typeId,
		n.provinceId,
		p.name provinceName,
		t.typeName typeName,
		d.id adv_id,
		d.releId adv_releId,
		d.isOrder adv_isOrder,
		d.isState adv_isState,
		d.adv adv_adv,
		d.adv3 adv_adv3,
		d.createTime createTime
		from Notice_CTE n
		left join NoticeType t on t.id = n.typeId
		left join Province p on p.id = n.provinceId
		left join LF_CTE d on d.releId = n.id
		%s
		order by publishTime desc
SQL;

        $sql = sprintf($sql, $cte_province_condition, $page_condition);
        $result = self::nativeQuery($sql, $bind);
        //TODO result map
        return $result;
    }

    /**
     * 获取公告总数
     * @param null $province_id
     * @return mixed
     */
    public static function getNoticeCount($province_id=null)
    {
        $bind = array();
        $province_condition = '';
        if($province_id)
        {
            $province_condition = 'where provinceId in (:provinceId,0)';
            $bind['province_id'] = $province_id;
        }

        $sql = 'select count(id) from Notice %s';
        $sql = sprintf($sql, $province_condition);
        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取通知类型列表
     * @return array
     */
    public static function getNoticeTypeList()
    {
        $sql = 'select id, typeName as [name] from NoticeType';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }


    /**
     * 添加公告
     * @param $title
     * @param $contents
     * @param $type_id
     * @param $province_id
     * @return bool
     */
    public static function addNotice($title, $contents, $type_id, $province_id)
    {
        $sql = <<<SQL
        insert into Notice(title,contents,publishTime,isState,isOrder,typeId,provinceId)
		values(:title, :contents, getdate(), 1, 0, :type_id, :province_id)
SQL;
        $bind = array(
            'title' => $title,
            'contents' => $contents,
            'type_id' => $type_id,
            'province_id' => $province_id
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除通知
     * @param $id
     * @return bool
     */
    public static function delNotice($id)
    {
        $sql = 'delete from Notice where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新公告
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateNotice($id, array $criteria=null)
    {
        $field_str = '';
        $bind = array('id' => $id);

        $crt = new Criteria($criteria);

        if($crt->title)
        {
            $field_str .= 'title = :title, ';
            $bind['title'] = $crt->title;
        }

        if($crt->contents)
        {
            $field_str .= 'contents = :contents, ';
            $bind['contents'] = $crt->contents;
        }

        if($crt->type_id)
        {
            $field_str .= 'typeId = :type_id, ';
            $bind['type_id'] = $crt->type_id;
        }

        if($crt->province_id)
        {
            $field_str .= 'provinceId = :province_id, ';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->is_state or $crt->is_state === '0' or $crt->is_state === 0)
        {
            $field_str .= 'isState = :is_state, ';
            $bind['is_state'] = $crt->is_state;
        }

        if($crt->is_order or $crt->is_order === '0' or $crt->is_order === 0)
        {
            $field_str .= 'isOrder = :is_order, ';
            $bind['is_order'] = $crt->is_order;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update Notice set $field_str where id = :id";

        return self::nativeExecute($sql, $bind);
    }

}