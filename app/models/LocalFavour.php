<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-3
 * Time: 上午10:02
 */

use \Phalcon\Db;

class LocalFavour extends ModelEx
{

    public static function getLocalFavourTypeList()
    {
        $sql = 'select id,FavourType typeName from LocalFavourType';
        $result = self::nativeQuery($sql, null, null, DB::FETCH_OBJ);
        return $result;
    }

    /**
     * @param $local_favour_id
     * @return array
     */
    public static function getLocalFavour($local_favour_id)
    {
        $sql = <<<SQL
        select
		l.id,
		l.title,
		l.contents,
		l.publishTime,
		l.des,
		l.cityId,
		l.provinceId,
		l.isState,
		l.orderTime,
		c.name cityName,
		p.name provinceName,
		type.id typeId,
		type.FavourType typeName,
		i.id index_id,
		i.releId index_releId,
		i.title index_title,
		i.subTitle index_subTitle,
		i.groupId index_groupId,
		i.isOrder index_indexOrder,
		d.id adv_id,
		d.releId adv_releId,
		d.isOrder adv_isOrder,
		d.isState adv_isState,
		d.adv adv_adv,
		d.adv3 adv_adv3,
		d.createTime createTime,
		pic.id pic_id,
		pic.pId pic_pId,
		pic.picTitle pic_picTitle,
		pic.isOrder pic_isOrder
		from LocalFavour l
		left join Province p on p.id = l.provinceId
		left join City c on c.id = l.cityId
		left join LocalFavourType type on type.id = l.typeId
		left join LocalFavourIndex i on i.releId = l.id
		left join LocalFavourAdv d on d.releId = l.id
		left join LocalFavourPic pic on pic.pId = l.id
		where l.id = :local_favour_id
SQL;
        //TODO result map
        $result = self::nativeQuery($sql, array('local_favour_id', $local_favour_id));
        return $result;
    }


    public static function getLocalFavourList($province_id=null, $page_num=null, $page_size=null )
    {

        $bind = array();

        $province_condition = '';
        $page_condition = '';

        if($province_id)
        {
            $province_condition = 'where l.provinceId in(:province_id,0)';
            $bind['province_id'] = $province_id;
        }

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
    			WITH LocalFavour_CTE AS (
			    SELECT *, ROW_NUMBER() OVER (ORDER BY [id] desc) AS rownum FROM
			    LocalFavour l
			    %s
			)

		select
		l.id,
		l.title,
		l.des,
		--l.contents,
		l.publishTime,
		l.cityId,
		l.provinceId,
		l.isState,
		l.orderTime,
		l.countFavourRead,
		c.name cityName,
		p.name provinceName,
		type.id typeId,
		type.FavourType typeName,
		i.id index_id,
		i.releId index_releId,
		i.title index_title,
		i.subTitle index_subTitle,
		i.groupId index_groupId,
		i.isOrder index_indexOrder,
		d.id adv_id,
		d.releId adv_releId,
		d.isOrder adv_isOrder,
		d.isState adv_isState,
		d.adv adv_adv,
		d.createTime createTime,
		d.adv3 adv_adv3,
		pic.id pic_id,
		pic.pId pic_pId,
		pic.picTitle pic_picTitle,
		pic.isOrder pic_isOrder
		from LocalFavour_CTE l
		left join Province p on p.id = l.provinceId
		left join City c on c.id = l.cityId
		left join LocalFavourType type on type.id = l.typeId
		left join LocalFavourIndex i on i.releId = l.id
		left join LocalFavourAdv d on d.releId = l.id
		left join LocalFavourPic pic on pic.pId = l.id
		%s
		order by l.id desc
SQL;

        $sql = sprintf($sql, $province_condition, $page_condition);
        $result = self::nativeQuery($sql, $bind);
        //TODO result map
        return $result;
    }

    /**
     * @param null $province_id
     * @return int
     */
    public static function getLocalFavourCount($province_id=null)
    {
        $bind = array();
        $province_condition = '';
        if($province_id)
        {
            $province_condition = 'where provinceId in (:provinceId,0)';
            $bind['province_id'] = $province_id;
        }

        $sql = 'select count(*) from LocalFavour %s';
        $sql = sprintf($sql, $province_condition);
        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }


    /**
     * 添加本地惠图片
     * @param $pid
     * @param $pic_title
     * @param $pic_data
     */
    public static function addLocalFavourPic($pid, $pic_title, $pic_data)
    {
        $sql = 'insert into dbo.LocalFavourPic(pId,picTitle,picData) values(:pid, :pic_title, :pic_data)';

        self::nativeExecute($sql, array(
            'pid' => $pid,
            'pic_title' => $pic_title,
            'pic_data' => $pic_data
            ));
    }

    /**
     * @param $id
     */
    public static function delLocalFavourPic($id)
    {
        $sql = 'delete from LocalFavourPic where id = :id';

        self::nativeQuery($sql, array('id' => $id));
    }

    /**
     * 获取本地惠内容
     * @param $id
     * @return null
     */
    public static function getLocalFavourContent($id)
    {
        $sql = 'select contents from LocalFavour where id = :id';
        $bind = array('id' => $id);
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * @param $pid
     * @return array
     */
    public static function getLocalFavourPicList($pid)
    {
        $sql = 'select id,pId,picTitle,picData,isOrder picOrder from dbo.LocalFavourPic where pId = :pid';
        return self::nativeQuery($sql, array('pid' => $pid));
    }

    /**
     * @param $id
     * @return array
     */
    public static function getLocalFavourPic($id)
    {
        $sql = 'select id,pId,picTitle,picData,isOrder picOrder from dbo.LocalFavourPic where id = :id';
        $result = self::nativeQuery($sql, array('id' => $id));
        return $result[0];
    }

    /**
     * 更新本地惠图片
     * @param $id
     * @param $pid
     * @param $pic_title
     * @param $pic_data
     * @param $is_order
     * @return bool
     */
    public static function updateLocalFavourPic($id, $pid=null, $pic_title=null, $pic_data=null, $is_order=null)
    {

        $sql = 'update LocalFavourPic set %s where id = :id';

        $field_str = '';
        $bind = array('id' => $id);

        if($pid || $pid === '0' || $pid === 0)
        {
            $field_str .= 'pId = :pid, ';
            $bind['pid'] = $pid;
        }
        if($pic_title)
        {
            $field_str .= 'picTitle = :pic_title, ';
            $bind['pic_title'] = $pic_title;
        }
        if($pic_data)
        {
            $field_str .= 'picData = :pic_data, ';
            $bind['pic_data'] = $pic_data;
        }
        if($is_order)
        {
            $field_str .= 'isOrder = :is_order, ';
            $bind['is_order'] = $is_order;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $type_id
     * @param $city_id
     * @param $province_id
     * @param $title
     * @param $contents
     * @param $publish_time
     * @param $des
     * @param $order_time
     * @return array
     */
    public static function addLocalFavour($type_id, $province_id, $city_id, $title, $contents, $publish_time, $des, $order_time)
    {
        $sql = 'insert into LocalFavour(typeId,cityId,provinceId,title,contents,publishTime,des,isState,orderTime) values(:type_id,:city_id,:province_id,:title,:contents,:publish_time,:des,0,:order_time)';

        self::nativeExecute($sql, array(
            'type_id' => $type_id,
            'city_id' => $city_id,
            'province_id' => $province_id,
            'title' => $title,
            'contents' => $contents,
            'publish_time' => $publish_time,
            'des' => $des,
            'order_time' => $order_time
        ));

        $connection = self::_getConnection();
        return $connection->lastInsertId();
    }

    /**
     * @param $id
     * @param $province_id
     * @param $city_id
     * @param $title
     * @param $contents
     * @param $type_id
     * @param $des
     * @param $is_state
     * @param $order_time
     * @return array
     */
    public static function updateLocalFavour($id, $province_id=null, $city_id=null, $title=null, $contents=null, $type_id=null, $des=null, $is_state=null, $order_time=null)
    {
        $sql = 'update LocalFavour set %s where id = :id';

        $field_str = '';
        $bind = array('id' => $id);

        if($province_id)
        {
            $field_str .= 'provinceId = :province_id, ';
            $bind['province_id'] = $province_id;
        }
        if($city_id)
        {
            $field_str .= 'cityId = :city_id, ';
            $bind['city_id'] = $city_id;
        }
        if($title)
        {
            $field_str .= 'title = :title, ';
            $bind['title'] = $title;
        }
        if($contents)
        {
            $field_str .= 'contents = :contents, ';
            $bind['contents'] = $contents;
        }
        if($type_id)
        {
            $field_str .= 'typeId = :type_id, ';
            $bind['type_id'] = $type_id;
        }
        if($des)
        {
            $field_str .= 'des = :des, ';
            $bind['des'] = $des;
        }
        if($is_state || $is_state === '0')
        {
            $field_str .= 'isState = :is_state, ';
            $bind['is_state'] = $is_state;
        }
        if($order_time)
        {
            $field_str .= 'orderTime = :order_time, ';
            $bind['order_time'] = $order_time;
        }

        $field_str = rtrim($field_str, ', ');


        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $id
     * @return array
     */
    public static function delLocalFavour($id)
    {
        $sql = 'delete from LocalFavour where id = :id';
        return self::nativeExecute($sql, array('id' => $id));
    }

    /**
     * @param null $province_id
     * @return array
     */
    public static function getLocalFavourIndexList($province_id=null)
    {
        $sql = <<<SQL
        select
		i.id,
		i.releId,
		i.title,
		i.subTitle,
		i.groupId,
		i.isOrder indexOrder,
		l.id l_id,
		l.title l_title,
		l.contents l_contents,
		l.publishTime l_publishTime,
		type.id l_typeId,
		type.FavourType l_typeName
		from LocalFavourIndex i
		left join LocalFavour l on l.id = i.releId
		left join LocalFavourType type on type.id = l.typeId
SQL;
        $bind = array();
        if($province_id)
        {
            $sql .= ' where l.provinceId in (:province_id, 0)';
            $bind['province_id'] = $province_id;
        }
        $sql .= ' order by groupId, isOrder';

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取本地惠推广
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getLocalFavourAdvList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        if($crt->id)
        {
            $cte_condition_arr[] = 'd.id = :id';
            $bind['id'] = $crt->id;
        }

        if($crt->province_id)
        {
            $cte_condition_arr[] = 'l.provinceId in(:province_id,0)';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->is_state)
        {
            $cte_condition_arr[] = 'd.isState = :is_state';
            $bind['is_state'] = $crt->is_state;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :offset and :limit';
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }
        $sql = <<<SQL
        WITH LocalFavourAdv_CTE AS (
		  SELECT d.id id,
                d.releId releId,
                d.isOrder isOrder,
                d.isState isState,
                d.adv adv,
                d.provinceId,
                p.name provinceName,
                d.createTime createTime,
                d.adv3 adv3,
                d.contents,
                --case when d.type = 'Link' then d.contents else null end as contents,
                --regexp_replace(d.contents, '[^\u4e00-\u9fa5a-zA-Z]', '') as contents,
                d.type,
                l.id l_id,
                isnull(l.title, n.title) as title,
                l.des l_des,
                --l.contents l_contents,
                l.publishTime l_publishTime,
                type.id l_typeId,
                type.FavourType l_typeName,
                ROW_NUMBER() OVER (ORDER BY d.isState desc, d.isOrder asc, d.createTime desc) AS rownum
                FROM
			    LocalFavourAdv d
			    left join Province p on p.id = d.provinceId
		        left join LocalFavour l on l.id = d.releId and d.type = 'LocalFavour'
		        left join LocalFavourType type on type.id = l.typeId and d.type = 'LocalFavour'
		        left join Notice n on n.id = d.releId and d.type = 'Notice'
			    $cte_condition_str
		)
        select *
		from LocalFavourAdv_CTE d
		$page_condition_str
SQL;
        $result = self::nativeQuery($sql, $bind);
        return $result;
    }

    /**
     * 获取本地惠推广总数
     * @param array|null $criteria
     * @return int
     */
    public static function getLocalFavourAdvCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';
        $bind = array();

        if($crt->province_id)
        {
            $condition_arr[] = 'l.provinceId in(:province_id,0)';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->is_state)
        {
            $condition_arr[] = 'd.isState = :is_state';
            $bind['is_state'] = $crt->is_state;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
		SELECT count(d.id)
        FROM
        LocalFavourAdv d
        left join Province p on p.id = d.provinceId
        left join LocalFavour l on l.id = d.releId
        left join LocalFavourType type on type.id = l.typeId
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return (int)$result[0];
    }

    /**
     * @param $rele_id
     * @param $title
     * @param $subtitle
     * @param $group_id
     * @return array
     */
    public static function addLocalFavourIndex($rele_id, $title, $subtitle, $group_id)
    {
        $sql = 'insert into LocalfavourIndex(releId,title,subTitle,groupId,isOrder) values(:rele_id,:title,:subtitle,:group_id,0)';
        return self::nativeQuery($sql, array(
            'rele_id' => $rele_id,
            'title' => $title,
            'subtitle' => $subtitle,
            'group_id' => $group_id
        ));
    }

    /**
     * @param $rele_id
     * @param $title
     * @param $subtitle
     * @param $group_id
     * @param $is_order
     * @return array
     */
    public static function updateLocalFavourIndex($rele_id, $title=null, $subtitle=null, $group_id=null, $is_order=null)
    {
        $sql = 'update LocalFavourIndex %s where releId = :rele_id';

        $field_str = '';
        $bind = array('rele_id' => $rele_id);

        if($title)
        {
            $field_str .= 'title = :title, ';
            $bind['title'] = $title;
        }
        if($subtitle)
        {
            $field_str .= 'subTitle = :subtitle, ';
            $bind['subtitle'] = $subtitle;
        }
        if($group_id !== null)
        {
            $field_str .= 'groupId = :group_id, ';
            $bind['group_id'] = $group_id;
        }
        if($is_order !== null)
        {
            $field_str .= 'isOrder = :is_order, ';
            $bind['is_order'] = $is_order;
        }

        $field_str = rtrim($field_str, ', ');


        $sql = sprintf($sql, $field_str);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * @param $rele_id
     * @return array
     */
    public static function delLocalFavourIndex($rele_id)
    {
        $sql = 'delete from LocalFavourIndex where releId = :rele_id';
        return self::nativeQuery($sql, array('rele_id' => $rele_id));
    }

    /**
     * @param $rele_id
     * @param $adv
     * @param $adv3
     * @param $is_state
     * @param $type
     * @param $province_id
     * @return array
     */
    public static function addLocalFavourAdv($rele_id, $adv, $adv3, $is_state, $type, $province_id, $contents)
    {
        $sql = 'insert into LocalFavourAdv(releId,adv,adv3,isState,isOrder,[type],provinceId, contents) values(:rele_id,:adv,:adv3,:is_state,0,:type,:province_id, :contents)';
        return self::nativeExecute($sql, array(
            'rele_id' => $rele_id,
            'adv' => $adv,
            'adv3' => $adv3,
            'is_state' => $is_state,
            'type' => $type,
            'province_id' => $province_id,
            'contents' => $contents
        ));
    }

    /**
     * @param $id
     * @return array
     */
    public static function delLocalFavourAdv($id)
    {
        $sql = 'delete from LocalFavourAdv where id = :id';
        return self::nativeExecute($sql, array('id' => $id));
    }

    /**
     * 更新首页推广
     * @param $id
     * @param array $criteria
     * @return array
     */
    public static function updateLocalFavourAdv($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $bind = array();

        $field_str = '';
        $bind = array('id' => $id);

        if($crt->rele_id)
        {
            $field_str .= 'releId = :rele_id, ';
            $bind['rele_id'] = $crt->rele_id;
        }

        if($crt->type)
        {
            $field_str .= 'type = :type, ';
            $bind['type'] = $crt->type;
        }

        if($crt->province_id)
        {
            $field_str .= 'provinceId = :province_id, ';
            $bind['province_id'] = $crt->province_id;
        }

        if($crt->contents)
        {
            $field_str .= 'contents = :contents, ';
            $bind['contents'] = $crt->contents;
        }

        if($crt->adv)
        {
            $field_str .= 'adv3 = :adv, ';
            $bind['adv'] = $crt->adv;
        }

        if($crt->is_state !== null)
        {
            $field_str .= 'isState = :is_state, ';
            $bind['is_state'] = $crt->is_state;
        }

        if($crt->is_order !== null)
        {
            $field_str .= 'isOrder = :is_order, ';
            $bind['is_order'] = $crt->is_order;
        }

        if(!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        $sql = "update LocalFavourAdv set $field_str where id = :id";
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取本地惠稿件列表
     * @param null $province_id
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getLocalFavourSubList($province_id=null, $page_num=null, $page_size=null)
    {
        $bind = array();

        $province_condition = '';
        $page_condition = '';

        if($province_id)
        {
            $province_condition = 'where l.provinceId in(:province_id,0)';
            $bind['province_id'] = $province_id;
        }

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        WITH LocalFavourSub_CTE AS (
			    SELECT *, ROW_NUMBER() OVER (ORDER BY [id] desc) AS rownum FROM
			    LocalfavourSub l
			    %s
			)
		select
		s.id,
		s.title,
		s.contents,
		s.addr,
		s.tel,
		s.userId,
		s.publistTime publishTime,
		s.provinceId,
		pro.name provinceName,
		s.cityId,
		p.id pic_id,
		p.pid pic_pid,
		p.createTime pic_createTime,
		p.userId pic_userId
		from LocalFavourSub_CTE s
		left join LocalFavourSubPic p on p.pid = s.id
		left join Province pro on pro.id = s.provinceId
        %s
		order by s.id desc
SQL;

        $sql = sprintf($sql, $province_condition, $page_condition);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * @param $id
     * @return array
     */
    public static function getLocalFavourSub($id)
    {
        $sql = <<<SQL
        select
		s.id,
		s.title,
		s.contents,
		s.addr,
		s.tel,
		s.userId,
		s.publistTime publishTime,
		s.provinceId,
		pro.name provinceName,
		s.cityId,
		p.id pic_id,
		p.pid pic_pid,
		p.createTime pic_createTime,
		p.pic pic_pic,
		p.userId pic_userId
		from LocalfavourSub s
		left join LocalFavourSubPic p on p.pid = s.id
		left join Province pro on pro.id = s.provinceId
		where s.id = :id
SQL;
        return self::nativeQuery($sql, array('id' => $id));
    }

    /**
     * @param $province_id
     * @return array
     */
    public static function getLocalFavourSubCount($province_id)
    {
        $sql = 'select count(*) from LocalFavourSub';
        $where = ' where provinceId in (:province_id,0)';
        $bind = array();
        if($province_id)
        {
            $sql .= $where;
            $bind['province_id'] = $province_id;
        }

        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * @param $id
     * @return bool
     */
    public static function delLocalFavourSub($id)
    {
        $connection = self::_getConnection();
        $connection->begin();
        $connection->delete('LocalFavourSub', 'id = :id', array('id' => $id));
        $connection->delete('LocalFavourSubPic', 'pid = :pid', array('pid' => $id));
        $success = $connection->commit();
        if(!$success) $connection->rollback();
        $connection->close();
        return $success;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getLocalFavourSubPic($id)
    {
        $sql = <<<SQL
        select
		id,
		pid,
		createTime,
		pic,
		userId
		from LocalFavourSubPic where id = :id
SQL;
        $result = self::nativeQuery($sql, array('id' => $id));
        return $result[0];
    }

    /**
     * @param $pid
     * @return array
     */
    public static function getLocalFavourSubPicList($pid)
    {
        $sql = <<<SQL
        select
		id,
		pid,
		createTime,
		pic,
		userId
		from LocalFavourSubPic where pid = :pid
		order by id
SQL;
        return self::nativeQuery($sql, array('pid' => $pid));
    }

    /**
     * @param $adv_id
     * @return int
     */
    public static function cancelExtend($adv_id)
    {
        $sql = 'update LocalFavourAdv set isState=0 where id=:id';
        return self::nativeExecute($sql, array('id' => $adv_id));
    }

    /**
     * @param $id
     * @param $contents
     * @return int
     */
    public static function replyComment($id, $contents)
    {
        $sql = 'update LocalFavourComment set commentReply=:contents where id= :id';;
        return self::nativeExecute($sql, array('id' => $id, 'contents' => $contents));
    }


    /**
     * @param $pid parent comment id
     * @return array
     */
    public static function getLocalFavourComment($pid)
    {
        $sql = 'select * from LocalFavourComment where pid = :pid';

        return self::nativeQuery($sql, array('pid' => $pid), null, Db::FETCH_OBJ);
    }

}