<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-19
 * Time: 下午5:58
 */

use \Phalcon\Db;
use \Phalcon\Db\Column;

class AutoTree extends ModelEx
{
    /**
     * @param null|int $page_num
     * @param null|int $page_size
     * @return array
     */
    public static function getAutoList($page_num=null, $page_size=null)
    {
        $page_condition = '';
        $limit_condition = '';

        $bind = array();

        if($page_num)
        {
            $page_condition = <<<Cond
where id not in ( select top :offset id from AutoTree order by id )
Cond;
            $limit_condition = 'top :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1);
            $bind['offset'] = $offset;
            $bind['limit'] = $page_size;
        }

        $sql = <<<SQL
select %s id,brand,autoName,nullPrice,state,urlCode from AutoTree
%s
order by id
SQL;
        $sql = sprintf($sql, $limit_condition, $page_condition);

        return self::nativeQuery($sql, $bind, array('limit' => Column::BIND_PARAM_INT, 'offset' => Column::BIND_PARAM_INT));
    }

    /**
     * @return int
     */
    public static function getCount()
    {
        $sql = 'select count(*) from AutoTree';
        $result = self::nativeQuery($sql, null, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取图片数据
     * @param $id
     * @return string
     */
    public static function getPicData($id)
    {
        $sql = 'select picData from AutoTree where id = :id';
        $result = self::nativeQuery($sql, array('id' => $id));
        return $result[0]['picData'];
    }
}