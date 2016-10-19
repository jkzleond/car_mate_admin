<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-24
 * Time: 下午4:45
 */

use Phalcon\Db;

class Interaction extends ModelEx
{
    protected static $_table_name = 'Interaction';

    public static function getInteractionListById() 
    { 
        $sql = <<<SQL
        select Province.name province_name,i.id,i.provinceId province_id,i.publishTime publish_time from Province,( 
                select Interaction.id,Interaction.provinceId,Interaction.publishTime 
                 from Interaction,( 
                select provinceId,MAX(publishTime) maxTime from Interaction  group by provinceId ) t
                 where Interaction.provinceId=t.provinceId and Interaction.publishTime = t.maxTime 
                 ) i 
                  where Province.id =i.provinceId
SQL;
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ); 
    }

    public static function getInteractionList(array $criteria=null, $page_num=1, $page_size=10)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if($crt->contents)
        {
            $cte_condition[] = 'contents like :contents';
            $bind['contents'] = '%'.$crt->contents.'%';
        }

        if(!empty($cte_condition))
        {
            $cte_condition_str = implode(' and ', $cte_condition);
        }

        if($page_num)
        {
            $page_condition_str = 'rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = ($page_num - 1) * $page_size;
        }

        $sql = <<<SQL
        with INTERACTION_CTE as (
            select *, ROW_NUMBER over (order by publishTime desc) from Interaction
            $cte_condition_str
        )
        select * from INTERACTION_CTE i
        left join Province p on p.id = i.provinceId
        $page_condition_str
SQL;
    }
}