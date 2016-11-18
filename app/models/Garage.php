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

    public function initialize()
    {
        $this->hasManyToMany(
            'id',
            'GarageToService',
            'garage_id', 'service_id',
            'GarageService',
            'id'
        );

    }
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
        $join_str = '';
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        if ($crt->name)
        {
            $cte_condition_arr[] = 'g.name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->mc_id)
        {
            $cte_condition_arr[] = 'g.mc_id = :mc_id';
            $bind['mc_id'] = $crt->mc_id;
        }

        if ($crt->tel)
        {
            $cte_condition_arr[] = 'g.tel = :tel';
            $bind['tel'] = $crt->tel;
        }

        if ($crt->address)
        {
            $cte_condition_arr[] = 'g.address like :address';
            $bind['address'] = '%'.$crt->address.'%';
        }

        if ($crt->service_id)
        {
            $join_str = 'left join GarageToService g2s on g2s.garage_id = g.id';
            $cte_condition_arr[] = 'g2s.service_id = :service_id';
            $bind['service_id'] = $crt->service_id;
        }

        if (!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        $order_by = 'order by g.id asc';

        if ($crt->order_by == 'appraise')
        {
            $order_by = 'order by appraise desc';
        }
        elseif($crt->order_by == 'reservation_count')
        {
            $order_by = 'order by reservation_count desc';
        }

        if ($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            //rownum 从 1 开始计数, $from 要 加 1
            $from = $page_size * ( $page_num - 1) + 1;
            $bind['from'] = $from;
            $bind['to'] = $from + $page_size - 1;
        }



        $sql = <<<SQL
        with G_CTE as (
          select
            g.id, g.name, g.address, g.thumbnail, g.has_point, g.tel,
            g.appraise, g.reservation_count,
            mc.name as merchant_name,
            row_number() over($order_by) as rownum
          from Garage g
          left join GarageMerchant mc on mc.id = g.mc_id
          $join_str
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
        $join_str = '';
        $condition_arr = array();
        $condition_str = '';
        $bind = array();
        

        if ($crt->name)
        {
            $condition_arr[] = 'g.name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->mc_id)
        {
            $condition_arr[] = 'g.mc_id = :mc_id';
            $bind['mc_id'] = $crt->mc_id;
        }

        if ($crt->tel)
        {
            $condition_arr[] = 'g.tel = :tel';
            $bind['tel'] = $crt->tel;
        }

        if ($crt->address)
        {
            $condition_arr[] = 'g.address like :address';
            $bind['address'] = '%'.$crt->address.'%';
        }

        if ($crt->service_id)
        {
            $join_str = 'left join GarageToService g2s on g2s.garage_id = g.id';
            $condition_arr[] = 'g2s.service_id = :service_id';
            $bind['service_id'] = $crt->service_id;
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
          select count(g.id)
          from Garage g
          left join GarageMerchant mc on mc.id = g.mc_id
          $join_str
          $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取指定ID的修理厂信息
     * @param $id
     * @return array
     */
    public static function getGarageById($id)
    {
        $sql = <<<SQL
          select g.*, mc.name as mc_name 
          from Garage as g
          left join GarageMerchant mc on mc.id = g.mc_id
          where g.id = :id
SQL;
        $bind = array('id' => $id);
        $garage = self::fetchOne($sql, $bind, null, Db::FETCH_ASSOC);
        $get_g2s_sql = <<<SQL
        select s.id, s.name from GarageToService g2s
        left join GarageService s on s.id = g2s.service_id
        where garage_id = :garage_id
SQL;
        if (!empty($garage))
        {
            $get_g2s_bind = array('garage_id' => $id);
            $g2s = self::nativeQuery($get_g2s_sql, $get_g2s_bind);
            $garage['services'] = $g2s;
        }
        return $garage;
    }

    /**
     * 添加修理厂
     * @param $data
     * @return bool
     */
    public static function addGarage($data)
    {
        $crt = new Criteria($data);
        $connection = self::_getConnection();
        $connection->begin();
        try
        {
            $garage_field_str = '';
            $garage_value_str = '';
            $add_garage_bind = array();

            if ($crt->name)
            {
                $garage_field_str .= 'name, ';
                $garage_value_str .= ':name, ';
                $add_garage_bind['name'] = $crt->name;
            }

            if ($crt->address)
            {
                $garage_field_str .= 'address, ';
                $garage_value_str .= ':address, ';
                $add_garage_bind['address'] = $crt->address;
            }

            if ($crt->lat)
            {
                $garage_field_str .= 'lat, ';
                $garage_value_str .= ':lat, ';
                $add_garage_bind['lat'] = $crt->lat;
            }

            if ($crt->lng)
            {
                $garage_field_str .= 'lng, ';
                $garage_value_str .= ':lng, ';
                $add_garage_bind['lng'] = $crt->lng;
            }

            if ($crt->ad_code)
            {
                $garage_field_str .= 'ad_code, ';
                $garage_value_str .= ':ad_code, ';
                $add_garage_bind['ad_code'] = $crt->ad_code;
            }

            if ($crt->tel)
            {
                $garage_field_str .= 'tel, ';
                $garage_value_str .= ':tel, ';
                $add_garage_bind['tel'] = $crt->tel;
            }

            if ($crt->mc_id)
            {
                $garage_field_str .= 'mc_id, ';
                $garage_value_str .= ':mc_id, ';
                $add_garage_bind['mc_id'] = $crt->mc_id;
            }

            if ($crt->thumbnail)
            {
                $garage_field_str .= 'thumbnail, ';
                $garage_value_str .= ':thumbnail, ';
                $add_garage_bind['thumbnail'] = $crt->thumbnail;
            }

            if ($crt->img)
            {
                $garage_field_str .= 'img, ';
                $garage_value_str .= ':img, ';
                $add_garage_bind['img'] = $crt->img;
            }

            if ($crt->des)
            {
                $garage_field_str .= 'des, ';
                $garage_value_str .= ':des, ';
                $add_garage_bind['des'] = $crt->des;
            }

            if (!empty($garage_field_str))
            {
                $garage_field_str = rtrim($garage_field_str, ', ');
                $garage_value_str = rtrim($garage_value_str, ', ');
            }
            else
            {
                return false;
            }

            $add_garage_sql = "insert into Garage ($garage_field_str) values ($garage_value_str)";
            $add_garage_success = self::nativeExecute($add_garage_sql, $add_garage_bind);
            if (!$add_garage_success) throw new \Exception('add garage failed');
            $garage_id = $connection->lastInsertId();
            if ($crt->services)
            {
                $g2s_value_str = '';
                $add_g2s_bind = array();
                foreach ($crt->services as $index => $service_id)
                {
                    $service_id_param_name = 'service_id_'.$index;
                    $garage_id_param_name = 'garage_id_'.$index;
                    $g2s_value_str .= "(:$service_id_param_name, :$garage_id_param_name), ";
                    $add_g2s_bind[$service_id_param_name] = $service_id;
                    $add_g2s_bind[$garage_id_param_name] = $garage_id;
                }
                $g2s_value_str = rtrim($g2s_value_str, ', ');
                $add_g2s_sql = "insert into GarageToService ( service_id, garage_id ) values $g2s_value_str";
                $add_g2s_success = self::nativeExecute($add_g2s_sql, $add_g2s_bind);
                if (!$add_g2s_success) throw new \Exception('add garage_to_service failed');
            }
            $success = $connection->commit();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            $connection->rollback();
            exit;
            return false;
        }

        return $success;
    }

    /**
     * 删除修理厂
     * @param $garage_id
     * @return bool
     */
    public static function deleteGarage($garage_id)
    {
        $connection = self::_getConnection();
        $connection->begin();
        try
        {
            $del_g2s_sql = 'delete from GarageToService where garage_id = :garage_id';
            $del_g2s_bind = array('garage_id' => $garage_id);
            $del_g2s_success = self::nativeExecute($del_g2s_sql, $del_g2s_bind);
            if (!$del_g2s_success) throw new \Exception('del garage_to_service failed');
            $del_garage_sql = 'delete from Garage where id = :garage_id';
            $del_garage_bind = array(
                'garage_id' => $garage_id
            );
            $del_garage_success = self::nativeExecute($del_garage_sql, $del_garage_bind);
            if (!$del_garage_success) throw new \Exception('del garage failed');
            $success = $connection->commit();
        }
        catch (\Exception $e)
        {
            $connection->rollback();
            return false;
        }
        return $success;
    }

    /**
     * 更新修理厂
     * @param $garage_id
     * @param $data
     * @return bool
     */
    public static function updateGarage($garage_id, $data)
    {
        $crt = new Criteria($data);
        $field_str = '';
        $bind = array('garage_id' => $garage_id);

        if ($crt->mc_id)
        {
            $field_str .= 'mc_id = :mc_id, ';
            $bind['mc_id'] = $crt->mc_id;
        }

        if ($crt->name)
        {
            $field_str .= 'name = :name, ';
            $bind['name'] = $crt->name;
        }

        if ($crt->address)
        {
            $field_str .= 'address = :address, ';
            $bind['address'] = $crt->address;
        }

        if ($crt->lat)
        {
            $field_str .= 'lat = :lat, ';
            $bind['lat'] = $crt->lat;
        }

        if ($crt->lng)
        {
            $field_str .= 'lng = :lng, ';
            $bind['lng'] = $crt->lng;
        }

        if ($crt->ad_code)
        {
            $field_str .= 'ad_code = :ad_code, ';
            $bind['ad_code'] = $crt->ad_code;
        }

        if ($crt->tel)
        {
            $field_str .= 'tel = :tel, ';
            $bind['tel'] = $crt->tel;
        }

        if ($crt->thumbnail)
        {
            $field_str .= 'thumbnail = :thumbnail, ';
            $bind['thumbnail'] = $crt->thumbnail;
        }

        if ($crt->img)
        {
            $field_str .= 'img = :img, ';
            $bind['img'] = $crt->img;
        }

        if ($crt->des)
        {
            $field_str .= 'des = :des, ';
            $bind['des'] = $crt->des;
        }

        if (!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        $connection = self::_getConnection();
        $connection->begin();
        try
        {
            $del_g2s_sql = 'delete from GarageToService where garage_id = :garage_id';
            $del_g2s_bind = array('garage_id' => $garage_id);
            $del_g2s_success = self::nativeExecute($del_g2s_sql, $del_g2s_bind);
            if (!$del_g2s_success) throw new \Exception('delete garage_to_service failed');
            $add_g2s_value_str = '';
            $add_g2s_bind = array();
            if ($crt->services)
            {
                foreach ($crt->services as $index => $service_id)
                {
                    $param_service_id_name = 'service_id_'.$index;
                    $param_garage_id_name = 'garage_id_'.$index;
                    $add_g2s_value_str .= "(:$param_garage_id_name, :$param_service_id_name), ";
                    $add_g2s_bind[$param_garage_id_name] = $garage_id;
                    $add_g2s_bind[$param_service_id_name] = $service_id;
                }

                if (!empty($add_g2s_value_str))
                {
                    $add_g2s_value_str = rtrim($add_g2s_value_str, ', ');
                }
                $add_g2s_sql = "insert into GarageToService (garage_id, service_id) values $add_g2s_value_str";
                $add_g2s_success = self::nativeExecute($add_g2s_sql, $add_g2s_bind);
                if (!$add_g2s_success) throw new \Exception('add garage_to_service failed');
            }
            $update_garage_sql = <<<SQL
        update Garage set $field_str where id = :garage_id
SQL;
            $update_garage_success = self::nativeExecute($update_garage_sql, $bind);
            if (!$update_garage_success) throw new \Exception('update garage failed');
        }
        catch (\Exception $e)
        {
            $connection->rollback();
            return false;
        }
        return $connection->commit();
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

    /**
     * 获取商家数据列表
     * @param array|null $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getMerchantList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array('state != 0');
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        if ($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->address)
        {
            $cte_condition_arr[] = 'address like :address';
            $bind['address'] = '%'.$crt->address.'%';
        }

        if ($crt->tel)
        {
            $cte_condition_arr[] = 'tel like :tel';
            $bind['tel'] = '%'.$crt->tel.'%';
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
        with MCTE as (
          select id, name, address, tel, business_license_img, owner_id_card_img,
          ROW_NUMBER() OVER (order by id desc) as rownum
          from GarageMerchant
          $cte_condition_str
        )
        select * from MCTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取商家总数
     * @param array|null $criteria
     * @return mixed
     */
    public static function getMerchantCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array('state != 0');
        $condition_str = '';
        $bind = array();

        if ($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if ($crt->address)
        {
            $condition_arr[] = 'address like :address';
            $bind['address'] = '%'.$crt->address.'%';
        }

        if ($crt->tel)
        {
            $condition_arr[] = 'tel like :tel';
            $bind['tel'] = '%'.$crt->tel.'%';
        }

        if (!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(id) from GarageMerchant
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 添加商家
     * @param $data
     * @return bool
     */
    public static function addMerchant($data)
    {
        $crt = new Criteria($data);
        $sql = 'insert into GarageMerchant (name, address, tel, business_license_img, owner_id_card_img) values (:name, :address, :tel, :business_license_img, :owner_id_card_img)';
        $bind = array(
            'name' => $crt->name,
            'address' => $crt->address,
            'tel' => $crt->tel,
            'business_license_img' => $crt->business_license_img,
            'owner_id_card_img' => $crt->owner_id_card_img
        );
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除商家
     * @param $mc_id
     * @return bool
     */
    public static function deleteMerchant($mc_id)
    {
        $sql = 'update GarageMerchant set state = 0 where id = :mc_id';
        $bind = array('mc_id' => $mc_id);
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除商家
     * @param $mc_id
     * @param $data
     * @return bool
     */
    public static function updateMerchant($mc_id, $data)
    {
        $crt = new Criteria($data);
        $field_str = '';
        $bind = array('mc_id' => $mc_id);

        if ($crt->name)
        {
            $field_str .= 'name = :name, ';
            $bind['name'] = $crt->name;
        }

        if ($crt->address)
        {
            $field_str .= 'address = :address, ';
            $bind['address'] = $crt->address;
        }

        if ($crt->tel)
        {
            $field_str .= 'tel = :tel, ';
            $bind['tel'] = $crt->tel;
        }

        if ($crt->business_license_img)
        {
            $field_str .= 'business_license_img = :business_license_img, ';
            $bind['business_license_img'] = $crt->business_license_img;
        }

        if ($crt->owner_id_card_img)
        {
            $field_str .= 'owner_id_card_img = :owner_id_card_img, ';
            $bind['owner_id_card_img'] = $crt->owner_id_card_img;
        }

        if (!empty($field_str))
        {
            $field_str = rtrim($field_str, ', ');
        }
        else
        {
            return false;
        }

        $sql = "update GarageMerchant set $field_str where id = :mc_id";
        return self::nativeExecute($sql, $bind);
    }
}