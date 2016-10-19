<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-19
 * Time: 下午3:55
 */

use \Phalcon\Db;

class AdminUser extends ModelEx
{
    protected static $_table_name = 'IAM_ADMINUSER';

    /**
     * @param string $user_id
     * @return array
     */
    public static function getAdminUserByUserId($user_id)
    {
        $sql = <<<SQL
        select
        a.ID id,
        a.USERID user_id,
        u.PWD password,
        a.STATUS status,
        status.codeName code_name,
        a.CREATEDATE create_date,
        a.PASSWORDEXPIREDATE password_expire_date,
        a.ACCOUNTEXPIREDATE account_expire_date,
        u.UNAME u_name,
        u.NICKNAME nick_name,
        a.LASTLOGINTIME last_login_time
       	from IAM_ADMINUSER a
       	left join IAM_USER u on a.USERID = u.USERID
       	left join IAM_ADMINUSERSTATUS status on status.code = a.status
       	where a.USERID = :user_id
SQL;

        $result = self::nativeQuery($sql,array(
            'user_id' => $user_id
        ));
        return $result[0];
    }

    /**
     * @param $user_id
     * @return array
     */
    public static function getAdminAuthorities($user_id)
    {
        $result =  self::nativeQuery('select authority from IAM_AUTHORITIES where userId = :user_id', array('user_id' => $user_id));
        $auths = array();
        foreach($result as $row)
        {
            $auths[] = $row['authority'];
        }
        return $auths;
    }

    /**
     * @param $user_id
     * @return array
     */
    public static function getAdminCityAuthorities($user_id)
    {
        $result = self::nativeQuery('select cityAuthority from CityAuthorities where userId = :user_id order by cityAuthority', array('user_id' => $user_id));
        $city_auths = array();
        foreach($result as $row)
        {
            $city_auths[] = $row['cityAuthority'];
        }
        return $city_auths;
    }

    public static function getCurrentUser()
    {
        $di = Phalcon\DI::getDefault();
        $user_arr = $di->getShared('session')->get('user');

        $user_obj = (object)$user_arr;

        return $user_obj;
    }

    /**
     * @return mixed
     */
    public static function getCurrentProvinceId()
    {
        $di = Phalcon\DI::getDefault();
        $user = $di->getShared('session')->get('user');
        return $user['province_id'];
    }

}