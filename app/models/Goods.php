<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-7-3
 * Time: 上午2:42
 */

class Goods extends ModelEx
{
    public static function updateGoods($id, array $criteria)
    {
        $crt = new Criteria($criteria);

        $field_str = '';
        $bind = array('id' => $id);

        if($crt->name)
        {
            $field_str .= '[name] = :name, ';
            $bind['name'] = $crt->name;
        }

        if($crt->des)
        {
            $field_str .= 'des = :des, ';
            $bind['des'] = $crt->des;
        }

        if($crt->price)
        {
            $field_str .= 'price = :price, ';
            $bind['price'] = $crt->price;
        }

        if($crt->is_shelf or $crt->is_shelf === '0' or $crt->is_shelf === 0)
        {
            $field_str .= 'isShelf = :is_shelf, ';
            $bind['is_shelf'] = $crt->is_shelf;
        }

        if($crt->abandon or $crt->abandon === '0' or $crt->abandon === 0)
        {
            $field_str .= 'abandon = :abandon, ';
            $bind['abandon'] = $crt->abandon;
        }

        if($crt->cat_id)
        {
            $field_str .= 'cat_id = :cat_id, ';
            $bind['cat_id'] = $crt->cat_id;
        }

        if($crt->type_id)
        {
            $field_str .= 'type_id = :type_id, ';
            $bind['type_id'] = $crt->type_id;
        }

        $field_str = rtrim($field_str, ', ');

        $sql = "update Hui_Goods set $field_str where id = :id";

        return self::nativeExecute($sql, $bind);
    }
}