<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-25
 * Time: 下午1:36
 */

use \Phalcon\Db;

class WelcomePage extends ModelEx
{
    /**
     * 获取开屏广告列表
     * @param null $province_id
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getWelcomeAdvList($province_id=null, $page_num=null, $page_size=null)
    {
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        if($province_id)
        {
            $cte_condition_arr[] = 'w.provinceId in (:province_id, 0)';
            $bind['province_id'] = $province_id;
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
        with WELADV_CTE as(
          select w.id,w.isState,w.createTime,w.provinceId,w.url,w.clockType, convert(varchar(20),w.startTime,20) as startTime, convert(varchar(20),w.endTime,20) as endTime,w.repeatTime,w.duration,p.name provinceName, ROW_NUMBER() over (order by w.id desc) as rownum
		  from WelAdv w
		  left join Province p on p.id = w.provinceId
		  $cte_condition_str
        )
        select * from WELADV_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取开屏广告总数
     * @param null $province_id
     * @return mixed
     */
    public static function getWelcomeAdvCount($province_id=null)
    {
        $condition_str = '';
        $bind = array();

        if($province_id)
        {
            $condition_str = 'where w.provinceId = :province_id';
            $bind['province_id'] = $province_id;
        }

        $sql = "select count(w.id) from WelAdv w $condition_str";

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取广告图片数据
     * @param $id
     * @return string 图片数据的base64编码
     */
    public static function getAdvPicDataById($id)
    {
        $sql = 'select pic from WelAdv where id = :id';
        $bind = array('id' => $id);

        $result = self::fetchOne($sql, $bind);

        return $result->pic;
    }

    /**
     * 添加开屏广告
     * @param array $criteria
     * @return bool
     */
    public static function addWelcomeAdv(array $criteria)
    {
        $crt = new Criteria($criteria);

        $sql = 'insert into WelAdv (provinceId, url, pic, clockType, startTime, endTime, duration, repeatTime, isState) values (:province_id, :url, :pic_data, :clock_type, :start_time, :end_time, :duration, :repeat_time, 0)';
        $bind = array(
            'province_id' => $crt->province_id,
            'url' => $crt->url,
            'pic_data' => $crt->pic_data,
            'clock_type' => $crt->clock_type,
            'start_time' => $crt->start_time,
            'end_time' => $crt->end_time,
            'duration'=> $crt->duration,
            'repeat_time' => $crt->repeat_time
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除开屏广告
     * @param $id
     * @return bool
     */
    public static function delWelcomeAdv($id)
    {
        $sql = 'delete from WelAdv where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新开屏广告
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateWelcomeAdv($id, array $criteria=null)
    {
        $field_str = '';
        $bind = array('id' => $id);

        $crt = new Criteria($criteria);

        if($crt->province_id or $crt->province_id === '0' or $crt->province_id === 0)
        {
            $field_str .= 'provinceId = :province_id, ';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->url)
        {
            $field_str .= 'url = :url, ';
            $bind['url'] = $crt->url;
        }

        if($crt->is_state or $crt->is_state === '0' or $crt->is_state === 0)
        {
            $field_str .= 'isState = :is_state, ';
            $bind['is_state'] = $crt->is_state;
        }

        if($crt->pic_data)
        {
            $field_str .= 'pic = :pic_data, ';
            $bind['pic_data'] = $crt->pic_data;
        }
        
        if($crt->is_clock === 'true')
        {
            if($crt->clock_type == 1)
            {
                $field_str .= 'startTime = :start_time, endTime = :end_time, repeatTime = null, duration = null, ';
                $bind[':start_time'] = $crt->start_time;
                $bind[':end_time'] = $crt->end_time ? $crt->end_time : null;
            }
            elseif($crt->clock_type > 1)
            {
                $field_str .= 'startTime = null, endTime = null,repeatTime = :repeat_time, duration = :duration, ';
                $bind['repeat_time'] = $crt->repeat_time;
                $bind['duration'] = $crt->duration;
            }

            $field_str .= 'clockType = :clock_type, ';
            $bind['clock_type'] = $crt->clock_type;
        }
        elseif($crt->is_clock === 'false')
        {
            $field_str .= 'clockType = null, startTime = null, endTime = null, repeatTime = null, duration = null, ';
        }

        if(!$field_str)
        {
            return false;
        }
        else
        {
            $field_str = rtrim($field_str, ', ');
        }

        $sql = "update WelAdv set $field_str where id = :id";

        return self::nativeExecute($sql, $bind);
    }
}