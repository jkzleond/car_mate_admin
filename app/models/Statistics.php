<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-23
 * Time: 上午11:19
 */

use Phalcon\Db;

class Statistics extends ModelEx
{

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getQueryCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getQueryCount', $start_date, $end_date, $group_type, $province_id);
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getQueryTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getQueryTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getUserCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getUserCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getUserTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getUserTotalCount', $start_date, $end_date, null, $province_id);
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getActCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getModStatistics', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getActTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getModTotalStatistics', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getUserActivityCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getUserActivityCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getMemberCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getMemberCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getMemberTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getMemberTotalCount', $start_date, $end_date, null, $province_id);
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getFXCQueryCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getFXCQueryCount', $start_date, $end_date, $group_type, $province_id);
    }
    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getFXCQueryTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getFXCQueryTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getLocalFavourCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getLocalFavourCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getLocalFavourTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getLocalFavourTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getInteractionCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getInteractionCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getInteractionTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getInteractionTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getTalkCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getTalkCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getTalkTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getTalkTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getLocationCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getLocationCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getLocationTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getLocationTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getNewIndexCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getNexIndexCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getNewIndexTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getNexIndexTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getInsuranceCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getInsuranceCount', $start_date, $end_date, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getInsuranceTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getInsuranceTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @return array
     */
    public static function getProvinceUserTotalCount()
    {
        $sql = <<<SQL
        WITH userProvince_CTE AS (
	select name as provinceName ,CLIENTTYPE,isnull(count,0) count
	from Province p
	left join
	(
	select clientType,count(*) count,PROVINCEID
	from IAM_USER
	group by PROVINCEID,clientType
	) results on results.PROVINCEID = p.id
	where p.id != 0
	)

	SELECT provinceName,
	ISNULL(SUM(CASE WHEN clientType like '%android%' THEN [count] END),0) [android],
	ISNULL(SUM(CASE WHEN clientType like '%iPad%' THEN [count] END),0) [iPad],
	ISNULL(SUM(CASE WHEN clientType like '%iPhone%' THEN [count] END),0) [iPhone],
	ISNULL(SUM(CASE WHEN clientType like '%iPodtouch%' THEN [count] END),0) [iPodTouch],
	ISNULL(SUM(CASE WHEN clientType like '%windows phone%' THEN [count] END),0) [windowsPhone],
	ISNULL(SUM(CASE WHEN (clientType not like '%android%') and (clientType not like '%iPad%') and (clientType not like '%iPhone%') and (clientType not like '%iPodtouch%') and (clientType not like '%windows phone%') THEN [count] END),0) [other],
	ISNULL(SUM([count]),0) [totalCount]
	FROM [userProvince_CTE] GROUP BY provinceName order by [totalCount] desc
SQL;
        return self::_normalize_data( self::nativeQuery($sql) );
    }

    /**
     * @param $province_id
     * @return array
     */
    public static function getUserClientVersion($province_id)
    {
        return self::_call_statistics_process('getUserClientVersionCount', null, null, null, $province_id);
    }

    /**
     * @return array
     */
    public static function getViolationCount()
    {
        $sql = <<<SQL
        select
        SUBSTRING(c.fzjg,1,1) province,
        max(i.createTime) max_time,
        p.name province_name
		from City c
		left join Province p on p.id = c.pId
		left join CarIllegal i on SUBSTRING(c.fzjg,1,1) = SUBSTRING(i.hphm,1,1)
		where c.fzjg is not null and c.fzjg <> ''
		group by SUBSTRING(c.fzjg,1,1),p.name
		order by max_time desc
SQL;
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getFriendCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getFriendCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getFriendTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getFriendTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getHotListCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getHotListCount', $start_date, $end_date, $group_type, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getHotListTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getHotListTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @param $province_id
     * @return array
     */
    public static function getActivityCount($start_date, $end_date, $group_type, $province_id)
    {
        return self::_call_statistics_process('getActivityCount', $start_date, $end_date, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $province_id
     * @return array
     */
    public static function getActivityTotalCount($start_date, $end_date, $province_id)
    {
        return self::_call_statistics_process('getActivityTotalCount', $start_date, $end_date, null, $province_id);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @return array
     */
    public static function getFirstPreliminaryCount($start_date, $end_date, $group_type)
    {
        return self::_call_statistics_process('getFirstPreliminaryCount', $start_date, $end_date, $group_type);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getFirstPreliminaryTotalCount($start_date, $end_date)
    {
        return self::_call_statistics_process('getFirstPreliminaryTotalCount', $start_date, $end_date);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @return array
     */
    public static function getInsuranceActCount($start_date, $end_date, $group_type)
    {
        return self::_call_statistics_process('getInsuranceActCount', $start_date, $end_date, $group_type);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group_type
     * @return array
     */
    public static function getInsuranceActTotalCount($start_date, $end_date)
    {
        return self::_call_statistics_process('getInsuranceActTotalCount', $start_date, $end_date);
    }

    /**
     * 获取违章代缴业务统计数据
     * @param  string $start_date
     * @param  string $end_date
     * @param  string $group_type
     * @return array
     */
    public static function getOrderIllegalCount($start_date=null, $end_date=null, $group_type=null)
    {
        return self::_call_statistics_process('getOrderIllegalCount', $start_date, $end_date, $group_type);
    }

    /**
     * 获取违章代缴业务总数
     * @param  string $start_date
     * @param  string $end_date
     * @return array
     */
    public static function getOrderIllegalTotalCount($start_date=null, $end_date=null)
    {
        return self::_call_statistics_process('getOrderIllegalTotalCount', $start_date, $end_date);
    }

    /**
     * 调用指定的存储过程
     * @param $proc_name 过程名称
     * @param $start_date
     * @param $end_date
     * @param null $group_type
     * @param null $province_id
     * @return array
     */
    protected static function _call_statistics_process($proc_name, $start_date=null, $end_date=null, $group_type=null, $province_id=null)
    {
       /*
        $sql = 'EXEC '.$process_name.' %s ';
        $param_str = '';
        $bind = array();
        if($start_date)
        {
            $param_str .= ':start_date, ';
            $bind['start_date'] = $start_date;
        }
        if($end_date)
        {
            $param_str .= ':end_date, ';
            $bind['end_date'] = $end_date;
        }
        if($group_type)
        {
            $param_str .= ':group_type, ';
            $bind['group_type'] = $group_type;
        }
        if(!$province_id && $province_id !== 0 )
        {
            $param_str .= ':province_id, ';
            $bind['province_id'] = $province_id;
        }

        //去除最后一个参数后面的逗号,并格式化sql
        $sql = sprintf($sql, rtrim($param_str, ', '));
        echo $sql;
        return self::nativeQuery($sql, $bind);
       */

        //此处调用windows服务器php脚本返回正确的存储过程调用结果
        $url = "http://116.55.248.76/statistics/statistics.php?proc_name=$proc_name&start_date=$start_date&end_date=$end_date&group_type=$group_type&province_id=$province_id";
        $json_data = file_get_contents($url);
        $arr_data = json_decode($json_data, true);
        $data = self::_normalize_data($arr_data);
        return $data;
    }


    protected static function _normalize_data($data)
    {
        $count = count($data);
        $normalized_data = array(
            'total' => $count,
            'count' => $count,
            'rows' => $data
        );
        return $normalized_data;
    }
}