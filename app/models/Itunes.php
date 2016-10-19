<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-5
 * Time: 下午2:41
 */

class Itunes extends ModelEx
{

    /**
     * 获取app排行
     * @param int $page_num
     * @param int $page_size
     * @return array|string
     */
    public static function getItunes($page_num=1, $page_size=10)
    {
        $sql_feedtype = 'select feedtype,genre from iTuensRank group by feedtype,genre order by genre,feedtype';
        $feedtypes = self::nativeQuery($sql_feedtype);

        $sql_rank = <<<SQL

        select Rank,
        look.appId as appId,
        look.appName as appName,
        feedType, Genre
        from iTuensRank_Look as look
        left join iTuensRank as rank
        on rank.appId = look.appId
        order by look.id;
SQL;
        $ranks = self::nativeQuery($sql_rank);


        $data = array();

        if(!empty($ranks))
        {
            foreach($ranks as $rank)
            {
                $data[$rank['appId']]['appName'] = $rank['appName'];
                $data[$rank['appId']]['appId'] = $rank['appId'];

                if(!empty($rank['Rank']))
                {

                    $data[$rank['appId']][$rank['feedType'].'_'.$rank['Genre']] = $rank['Rank'];
                }

                foreach($feedtypes as $feedtype)
                {
                    if(empty($data[$rank['appId']][$feedtype['feedtype'].'_'.$feedtype['genre']]))
                    {
                        $data[$rank['appId']][$feedtype['feedtype'].'_'.$feedtype['genre']] = '-';
                    }
                }
            }
        }

        foreach ($data as $k=>$v){
            foreach ($v as $sk=>$sv){
                $sk=str_replace(' ', '_', $sk);
                $v[$sk]=$sv;
            }
            $r[]=$v;
        }
        $total = count ( $r );
        for ($i=($page_size*($page_num-1));$i<$page_size*$page_num;$i++){
            if (!empty($r[$i])){
                $arr[]=$r[$i];
            }
        }
        $result = array (
            'total' => $total,
            'count' => count($arr),
            'rows' => $arr
        );
        return $result;
    }

    /**
     * 获取最近更新时间
     * @return string
     */
    public static function getLateUpTime()
    {
        $sql='select top 1 updateTime from dbo.iTuensRank order by id';
        $result = self::nativeQuery($sql);
        return $result[0]['updateTime'];
    }
}