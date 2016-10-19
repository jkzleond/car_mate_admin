<?php

class IamUser extends ModelEx
{
    protected static $_table_name = 'IAM_USER';

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $rid;

    /**
     *
     * @var string
     */
    public $nick_name;

    /**
     *
     * @var string
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $pwd;

    /**
     *
     * @var string
     */
    public $uname;

    /**
     *
     * @var string
     */
    public $abbrname;

    /**
     *
     * @var string
     */
    public $phome;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $imei;

    /**
     *
     * @var string
     */
    public $imsi;

    /**
     *
     * @var integer
     */
    public $now_score;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var string
     */
    public $last_login_time;

    /**
     *
     * @var string
     */
    public $create_date;

    /**
     *
     * @var integer
     */
    public $sex;

    /**
     *
     * @var string
     */
    public $province;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $birthday;

    /**
     *
     * @var string
     */
    public $client_type;

    /**
     *
     * @var string
     */
    public $ios_token;

    /**
     *
     * @var integer
     */
    public $city_id;

    /**
     *
     * @var integer
     */
    public $province_id;

    /**
     *
     * @var string
     */
    public $android_token;

    /**
     *
     * @var integer
     */
    public $rell_login;

    /**
     *
     * @var string
     */
    public $push_time;

    /**
     *
     * @var string
     */
    public $no_talk;

    /**
     *
     * @var string
     */
    public $client_version;

    /**
     *
     * @var integer
     */
    public $login_state;

    /**
     *
     * @var integer
     */
    public $face_update;

    /**
     *
     * @var integer
     */
    public $system_push;

    /**
     *
     * @var integer
     */
    public $talk_push;

    /**
     *
     * @var integer
     */
    public $private_push;

    /**
     *
     * @var integer
     */
    public $weather_push;

    /**
     *
     * @var integer
     */
    public $road_rss_push;

    /**
     *
     * @var string
     */
    public $weixin_token;

    /**
     *
     * @var integer
     */
    public $is_member;

    /**
     *
     * @var integer
     */
    public $experience;

    /**
     *
     * @var integer
     */
    public $huigold;

    /**
     *
     * @var string
     */
    public $sign_time;

    /**
     *
     * @var integer
     */
    public $hd;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('IAM_USER');
        $this->useDynamicUpdate(true);
    }

    /**
     * @return IamUser[]
     */
    public static function find($parameters = array())
    {
        return parent::find($parameters);
    }

    /**
     * @return IamUser
     */
    public static function findFirst($parameters = array())
    {
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'id',
            'RID' => 'rid',
            'NICKNAME' => 'nick_name',
            'USERID' => 'user_id',
            'PWD' => 'pwd',
            'UNAME' => 'uname',
            'ABBRNAME' => 'abbr_name',
            'PHONE' => 'phone',
            'EMAIL' => 'email',
            'IMEI' => 'imei',
            'IMSI' => 'imsi',
            'NOWSCORE' => 'now_score',
            'STATUS' => 'status',
            'LASTLOGINTIME' => 'last_login_time',
            'CREATEDATE' => 'create_time',
            'SEX' => 'sex',
            'PROVINCE' => 'province',
            'CITY' => 'city',
            'BIRTHDAY' => 'birthday',
            'CLIENTTYPE' => 'client_type',
            'IOSTOKEN' => 'ios_token',
            'CITYID' => 'city_id',
            'PROVINCEID' => 'province_id',
            'ANDROIDTOKEN' => 'android_token',
            'RELLOGIN' => 'rell_login',
            'PUSHTIME' => 'push_time',
            'NOTALK' => 'no_talk',
            'CLIENTVERSION' => 'client_version',
            'LOGINSTATE' => 'login_state',
            'FACEUPDATE' => 'face_update',
            'SYSTEMPUSH' => 'system_push',
            'TALKPUSH' => 'talk_push',
            'PRIVATEPUSH' => 'private_push',
            'WEATHERPUSH' => 'weather_push',
            'ROADRSSPUSH' => 'road_rss_push',
            'WEIXINTOKEN' => 'weixin_token',
            'ISMEMBER' => 'is_member',
            'EXPERIENCE' => 'experience',
            'HuiGold' => 'huigold',
            'SIGNTIME' => 'sign_time',
            'HD' => 'hd'
        );
    }

}
