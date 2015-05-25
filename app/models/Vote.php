<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-12
 * Time: 下午4:51
 */

use \Phalcon\Db;

class Vote extends ModelEx
{
    /**
     * 获取投票列表
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getVoteList($page_num=null, $page_size=null)
    {
        $page_condition = '';
        $bind = array();

        if($page_num)
        {
            $page_condition = 'where rownum between :offset and :limit';
            //rownum 从 1 开始计数, $offset 要 加 1
            $offset = $page_size * ( $page_num - 1) + 1;
            $bind['offset'] = $offset;
            $bind['limit'] = $offset + $page_size - 1 ;
        }

        $sql = <<<SQL
        with VOTE_CTE as (
            select v.id, v.name, v.startDate, v.endDate, v.createTime, v.lastModefiedTime,
            v.state, v.url, v.autoUrl, v.type, v.maxOption, o.id as oid, o.name as oname,
            o.shortName as oshortName, o.createDate as ocreateTime, u.userCount, uc.voteCount,
            vn.rownum as rownum
            from Vote v
            left join (
              select id, ROW_NUMBER() OVER ( order by id desc ) as rownum from Vote
              where abandon != 1
              ) vn on vn.id = v.id
            left join VoteOption o on v.id=o.vid
            left join (
              select void, count(*) as userCount from VoteUser
              where ISNULL(abandon,0)!=1
              group by void
                        ) u on o.id=u.void
            left join (
              select vid, count(*) as voteCount from VoteUser
              where ISNULL(abandon,0)!=1
              group by vid
                        ) uc on v.id=uc.vid
            where v.abandon!=1 and  o.abandon!=1
        )
        select * from VOTE_CTE
        $page_condition
        order by id desc
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取投票总数
     * return int
     */
    public static function getVoteCount()
    {
        $sql = 'select count(id) from Vote where abandon != 1';

        $result = self::fetchOne($sql, null, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 添加投票
     * @param array|null $criteria
     * @return int|bool
     */
    public static function addVote(array $criteria=null)
    {
        $crt = new Criteria($criteria);

        $sql = <<<SQL
        insert into Vote([name], startDate, endDate, state, url, autoUrl, abandon, [type], maxOption)
		values (:name, :start_date, :end_date, 1, :url, :auto_url, 0, :type, :max_option)
SQL;

        $bind = array(
            'name' => $crt->name,
            'start_date' => $crt->start_date,
            'end_date' => $crt->end_date,
            'url' => $crt->url,
            'auto_url' => $crt->auto_url,
            'type' => $crt->type,
            'max_option' => $crt->max_option
        );

        $success = self::nativeExecute($sql, $bind);

        if($success)
        {
            $success = self::_getConnection()->lastInsertId();
        }

        return $success;
    }

    /**
     * 添加投票选项
     * @param $vname
     * @param $short_name
     * @param $vid
     * @return bool
     */
    public static function addVoteOption($vname, $short_name, $vid)
    {
        $sql = <<<SQL
		insert into VoteOption([name], shortName, vid, abandon)
		values (:vname, :short_name, :vid, 0)
SQL;
        $bind = array(
            'vname' => $vname,
            'short_name' => $short_name,
            'vid' => $vid
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 批量添加投票选项
     * @param array $options
     * @return bool
     */
    public static function addVoteOptions(array $options)
    {
        $values_str = '';
        $bind = array();

        foreach($options as $index => $option)
        {
            $vname_param = 'vname'.$index;
            $short_name_param = 'short_name'.$index;
            $vid_param = 'vid'.$index;
            $values_str .= '(:'.$vname_param.', :'.$short_name_param.', :'.$vid_param.'), ';

            $bind[$vname_param] = $option['vname'];
            $bind[$short_name_param] = $option['short_name'];
            $bind[$vid_param] = $option['vid'];
        }

        $values_str = rtrim($values_str, ', ');

        $sql = "insert into VoteOption ([name], shortName, vid) values $values_str";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除投票
     * @param $id
     * @return bool
     */
    public static function delVote($id)
    {
        $connection = self::_getConnection();
        $connection->begin();
        $sql_del_vote_option = 'delete from VoteOption where vid = :id';
        $sql_del_vote = 'delete from Vote where id = :id';
        $bind = array('id' => $id);

        $success_del_vote_option = $connection->execute($sql_del_vote_option, $bind);

        if(!$success_del_vote_option)
        {
            $connection->rollback();
            return false;
        }else
        {
            $connection->execute($sql_del_vote, $bind);
            return $connection->commit();
        }
    }

    /**
     * 更新投票
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateVote($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);

        $field_str = '';
        $bind = array('id' => $id);

        if($crt->name)
        {
            $field_str .= '[name] = :name, ';
            $bind['name'] = $crt->name;
        }

        if($crt->start_date)
        {
            $field_str .= 'startDate = :start_date, ';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $field_str .= 'endDate = :end_date, ';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->url)
        {
            $field_str .= 'url = :url, ';
            $bind['url'] = $crt->url;
        }

        if($crt->auto_url or $crt->auto_url === '0' or $crt->auto_url === 0)
        {
            $field_str .= 'autoUrl = :auto_url, ';
            $bind['auto_url'] = $crt->auto_url;
        }

        if($crt->type)
        {
            $field_str .= '[type] = :type, ';
            $bind['type'] = $crt->type;
        }

        if($crt->state)
        {
            $field_str .= 'state = :state, ';
            $bind[':state'] = $crt->state;
        }

        if($crt->max_option)
        {
            $field_str .= 'maxOption = :max_option, ';
            $bind['max_option'] = $crt->max_option;
        }

        $sql = <<<SQL
        update Vote set $field_str
		lastModefiedTime=getDate()
		where id=:id
SQL;

        if($crt->options)
        {
            foreach($crt->options as $option)
            {
                if($option['id'])
                {
                    self::updateVoteOption($option['id'], $option);
                }
                else
                {
                    $opt_crt = new Criteria($option);
                    self::addVoteOption($opt_crt->vname, $opt_crt->short_name, $id);
                }
            }
        }

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新投票选项
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateVoteOption($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $sql = <<<SQL
        update VoteOption set [name]=:vname, shortName=:short_name where id=:id
SQL;
        $bind = array(
            'vname' => $crt->vname,
            'short_name' => $crt->short_name,
            'id' => $id
        );

//        echo preg_replace('/:([^:,\)\s]*)/e', '$bind[\'$1\']', $sql);

        return self::nativeExecute($sql, $bind);
    }
}