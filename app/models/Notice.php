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
            $offset = $page_size * ( $page_num - 1);
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH Notice_CTE AS (
		    SELECT *, ROW_NUMBER() OVER (ORDER BY [publishTime] desc) AS rownum FROM
		    Notice n
		    %s
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
		left join LocalFavourAdv d on d.releId = n.id
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
}