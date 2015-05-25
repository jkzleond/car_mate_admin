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


}