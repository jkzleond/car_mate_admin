<?php

use \Phalcon\Db;

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 17-2-8
 * Time: 下午10:02
 */
class EvalPrice extends ModelEx
{
    /**
     * 获取估价记录数据列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getEvalPriceRecordList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if ($crt->user_id)
        {
            $cte_condition_arr[] = 'ep.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if ($crt->user_name)
        {
            $cte_condition_arr[] = '(ep.user_name = :user_name_0 or u.uname = :user_name_1)';
            $bind['user_name_0'] = $crt->user_name;
            $bind['user_name_1'] = $crt->user_name;
        }


        if ($crt->phone)
        {
            $cte_condition_arr[] = '(ep.phone = :phone_0 or u.phone = :phone_1)';
            $bind['phone_0'] = $crt->phone;
            $bind['phone_1'] = $crt->phone;
        }

        if ($crt->status !== null and $crt->status !== '')
        {
            $cte_condition_arr[] = 'ep.status = :status';
            $bind['status'] = $crt->status;
        }

        if ($crt->start_date)
        {
            $cte_condition_arr[] = 'datediff(dd, :start_date, create_date) >= 0';
            $bind['start_date'] = $crt->start_date;
        }

        if ($crt->end_date)
        {
            $cte_condition_arr[] = 'datediff(dd, create_date, :end_date) >=0';
            $bind['end_date'] = $crt->end_date;
        }

        if (!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if ($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size;
        }

        $sql = <<<SQL
        with EP_CTE as (
          select
            ep.id,
            ep.user_id,
            ep.mile_age,
            convert(varchar(20), ep.first_reg_time, 23) as first_reg_time,
            ep.min_price,
            ep.max_price,
            ep.mid_price,
            isnull(ep.phone, u.phone) as phone,
            ep.user_name,
            ep.status,
            ep.des,
--             isnull(ep.user_name, u.uname) as user_name,
            convert(varchar(20), create_date, 20) as create_date,
            brd.name as brand_name, srs.name as series_name, spc.name as spec_name,
            row_number() over (order by ep.create_date desc) as rownum
          from USEDCAR_eval_price ep
          left join USEDCAR_brand brd on brd.id = ep.brand_id
          left join USEDCAR_series srs on srs.id = ep.series_id
          left join USEDCAR_spec spc on spc.id = ep.spec_id
          left join IAM_USER u on u.userId = ep.user_id
          $cte_condition_str
        )
        select * from EP_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取估价记录总数
     * @param array|null $criteria
     * @return mixed
     */
    public static function getEvalPriceRecordCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();
        $condition_arr = array();
        $condition_str = '';

        if ($crt->user_id)
        {
            $condition_arr[] = 'ep.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if ($crt->user_name)
        {
            $condition_arr[] = '(ep.user_name = :user_name_0 or u.uname = :user_name_1)';
            $bind['user_name_0'] = $crt->user_name;
            $bind['user_name_1'] = $crt->user_name;
        }

        if ($crt->phone)
        {
            $condition_arr[] = '(ep.phone = :phone_0 or u.phone = :phone_1)';
            $bind['phone_0'] = $crt->phone;
            $bind['phone_1'] = $crt->phone;
        }

        if ($crt->status !== null)
        {
            $condition_arr[] = 'ep.status = :status';
            $bind['status'] = $crt->status;
        }

        if ($crt->start_date)
        {
            $condition_arr[] = 'datediff(dd, :start_date, create_date) >= 0';
            $bind['start_date'] = $crt->start_date;
        }

        if ($crt->end_date)
        {
            $condition_arr[] = 'datediff(dd, create_date, :end_date) >=0';
            $bind['end_date'] = $crt->end_date;
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select
            count(ep.id)
          from USEDCAR_eval_price ep
          left join USEDCAR_brand brd on brd.id = ep.brand_id
          left join USEDCAR_series srs on srs.id = ep.series_id
          left join USEDCAR_spec spc on spc.id = ep.spec_id
          left join IAM_USER u on u.userId = ep.user_id
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 更新估价记录
     * @param array $data
     * @param array $criteria
     * @return boolean
     */
    public static function updateEvalPriceRecord(array $data, array $criteria)
    {
        if (empty($criteria) or empty($data)) return false;
        $bind = array();
        $field_str = '';
        $condition_str = '';

        foreach ($data as $field => $value)
        {
            $field_value_bind_name = $field.'_v';
            $field_str .= $field.' = :'.$field_value_bind_name.',';
            $bind[$field_value_bind_name] = $value;
        }

        $field_str = rtrim($field_str, ', ');

        foreach ($criteria as $field => $value)
        {
            $field_condition_bind_name = $field.'_c';
            $condition_str .= $field.' = :'.$field_condition_bind_name.' and ';
            $bind[$field_condition_bind_name] = $value;
        }

        $condition_str = 'where '.rtrim($condition_str, ' and ');

        $sql = <<<SQL
        update USEDCAR_eval_price set $field_str $condition_str
SQL;
        return self::nativeExecute($sql, $bind);
    }
}