<?php
use \Phalcon\Db;

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-10-10
 * Time: 下午2:31
 */
class Garage extends ModelEx
{
    /**
     * 获取修理厂列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getGarageList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if ($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->mc_id)
        {
            $cte_condition_arr[] = 'mc_id = :mc_id';
            $bind['mc_id'] = $crt->mc_id;
        }

        if ($crt->tel)
        {
            $cte_condition_arr[] = 'tel = :tel';
            $bind['tel'] = $crt->tel;
        }

        if (!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if ($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            //rownum 从 1 开始计数, $from 要 加 1
            $from = $page_size * ( $page_num - 1) + 1;
            $bind['from'] = $from;
            $bind['to'] = $from + $page_size - 1;
        }


        $bind = array();
        $sql = <<<SQL
        with G_CTE as (
          select
            g.id, g.name, g.address, g.thumbnail, g.has_point, g.tel,
            gm.name as merchant_name,
            row_number() over(order by g.id asc) as rownum
          from Garage g
          left join GarageMerchant gm on gm.id = g.mc_id
          $cte_condition_str
        )
        select * from G_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取修理厂总数
     * @param array|null $criteria
     * @return mixed
     */
    public static function getGarageCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';

        if ($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->mc_id)
        {
            $condition_arr[] = 'mc_id = :mc_id';
            $bind['mc_id'] = $crt->mc_id;
        }

        if ($crt->tel)
        {
            $condition_arr[] = 'tel = :tel';
            $bind['tel'] = $crt->tel;
        }

        if (!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        $bind = array();
        $sql = <<<SQL
          select count(g.id)
          from Garage g
          left join GarageMerchant gm on gm.id = g.mc_id
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取服务类型列表
     * @return array
     */
    public static function getServiceList()
    {
        $sql = 'select id, name from GarageService';
        return self::nativeQuery($sql);
    }
}