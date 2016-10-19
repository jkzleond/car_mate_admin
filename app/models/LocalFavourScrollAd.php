<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-18
 * Time: 下午3:33
 */

use \Phalcon\Db;

class LocalFavourScrollAd extends ModelEx
{
    /**
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getScrollAdList($page_num=null, $page_size=null)
    {

        $bind = array();

        $page_condition = '';

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH LocalFavourScrollAd_CTE AS (
			    SELECT *, ROW_NUMBER() OVER (ORDER BY [id] desc) AS rownum FROM
			    LocalFavourScrollAd
			)
        select
		id,picData,reDirecURL as redirectUrl,showOrder,uploadTime,isState from
		LocalFavourScrollAd_CTE
		%s
		order by uploadTime desc
SQL;
        $sql = sprintf($sql, $page_condition);
        return self::nativeQuery($sql, $bind);
    }


    /**
     * @return int
     */
    public static function getScrollAdCount()
    {
        $sql = <<<SQL
        select count(id) from LocalFavourScrollAd
SQL;
        $result = self::nativeQuery($sql, null, null, Db::FETCH_NUM);
        return $result[0];

    }

    /**
     * @return array
     */
    public static function getUseList()
    {
        $sql = <<<SQL
        select
		id,picData,reDirecURL as redirectUrl,showOrder,uploadTime,isState from
		LocalFavourScrollAd
		where id in(
		 select max(id) from LocalFavourScrollAd
		 where isState=1 group by showOrder
		)
		order by showOrder
SQL;
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * @param $pic_data
     * @param $redirect_url
     * @param $show_order
     * @return bool
     */
    public static function addScrollAd($pic_data, $redirect_url, $show_order=0)
    {
        $sql = <<<SQL
        insert into
		LocalFavourScrollAd(picData,reDirecURL,uploadTime,showOrder,isState)
		values(:pic_data, :redirect_url, getdate(), :show_order, 0);
SQL;
        return self::nativeExecute($sql, array(
            'pic_data' => $pic_data,
            'redirect_url' => $redirect_url,
            'show_order' => $show_order
        ));
    }

    /**
     * @param $id
     * @return bool
     */
    public static function delScrollAd($id)
    {
        $sql = <<<SQL
        delete from LocalFavourScrollAd where id = :id
SQL;
        return self::nativeExecute($sql, array('id' => $id));
    }

    /**
     * @param $id
     * @return bool
     */
    public static function useScrollAd($id)
    {
        $sql = <<<SQL
        update LocalFavourScrollAd set isState=1 where id = :id
SQL;
        return self::nativeExecute($sql, array('id' => $id));
    }

    /**
     * @param $id
     * @param null $pic_data
     * @param null $redirect_url
     * @param int $show_order
     * @param int $is_state
     * @return bool
     */
    public static function updateScrollAd($id, $pic_data=null, $redirect_url=null, $show_order=null, $is_state=null)
    {
        $sql = 'update LocalFavourScrollAd set %s where id = :id';

        $field_str = 'lastModifiedTime = GETDATE(), ';
        $bind = array('id' => $id);

        if($pic_data)
        {
            $field_str .= 'picData = :pic_data, ';
            $bind['pic_data'] = $pic_data;
        }

        if($redirect_url)
        {
            $field_str .= 'reDirecURL = :redirect_url, ';
            $bind['redirect_url'] = $redirect_url;
        }

        if($show_order)
        {
            $field_str .= 'showOrder = :show_order, ';
            $bind['show_order'] = $show_order;
        }

        if($is_state || $is_state === 0 || $is_state === '0')
        {
            $field_str .= 'isState = :is_state, ';
            $bind['is_state'] = $is_state;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }



}