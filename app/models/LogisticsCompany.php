<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-24
 * Time: 下午8:04
 */

class LogisticsCompany extends ModelEx
{
    /**
     * 获取物流公司列表
     * @return array
     */
    public static function getCompanyList()
    {
        $sql = 'select * from LogisticsCompany where [using] = 1';
        return self::nativeQuery($sql);
    }
}