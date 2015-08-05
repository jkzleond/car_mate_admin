<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-4-29
 * Time: 下午9:32
 */

use \Phalcon\Db;

class Insurance extends ModelEx
{
    /**
     * 获取保险列表
     * @param array $criteria
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getAllInsuranceList($criteria=null, $page_num=null, $page_size=null)
    {
        $cte_condition = array();
        $cte_condition_str = '';
        $page_condition = '';

        $bind = array();

        $province_id = null;
        $user_id = null;
        $car_no = null;
        $car_type = null;
        $state = null;
        $user_name = null;
        $phone_no = null;
        $email_addr = null;
        $start_date = null;
        $end_date = null;
        $order_state = null;

        if(!empty($criteria) && is_array($criteria))
        {
            $province_id = isset($criteria['province_id']) ? $criteria['province_id'] : null;
            $user_id = isset($criteria['user_id']) ? $criteria['user_id'] : null;
            $car_no = isset($criteria['car_no']) ? $criteria['car_no'] : null;
            $car_type = isset($criteria['car_type']) ? $criteria['car_type'] : null;
            $state = isset($criteria['state']) ? $criteria['state'] : null;
            $user_name = isset($criteria['user_name']) ? $criteria['user_name'] : null;
            $phone_no = isset($criteria['phone_no']) ? $criteria['phone_no'] : null;
            $email_addr = isset($criteria['email_addr']) ? $criteria['email_addr'] : null;
            $start_date = isset($criteria['start_date']) ? $criteria['start_date'] : null;
            $end_date = isset($criteria['end_date']) ? $criteria['end_date'] : null;
            $order_state = isset($criteria['order_state']) ? $criteria['order_state'] : null;
        }

        if($province_id)
        {
            $cte_condition[] = 'u.provinceId = :province_id';
            $bind['province_id'] = $province_id;
        }

        if($user_id)
        {
            $cte_condition[] = "i.user_userId like :user_id";
            $bind['user_id'] = '%'.$user_id.'%';
        }

        if($car_no)
        {
            $cte_condition[] = "car.hphm like :car_no";
            $bind['car_no'] = '%'.$car_no.'%';
        }

        if($car_type and $car_type != '-1')
        {
            $cte_condition[] = 'i.carType_id = :car_type';
            $bind['car_type'] = $car_type;
        }

        if($state and $state != '-1')
        {
            if($state != 4)
            {
                $cte_condition[] = 'i.state_id = :state';
            }
            else
            {
                $cte_condition[] = "i.state_id = :state and i.payState = 1";
            }

            $bind['state'] = $state;
        }

        if($user_name)
        {
            $cte_condition[] = "i.userName like :user_name";
            $bind['user_name'] = '%'.$user_name.'%';
        }

        if($phone_no)
        {
            $cte_condition[] = "i.phoneNo like :phone_no";
            $bind['phone_no'] = '%'.$phone_no.'%';
        }

        if($email_addr)
        {
            $cte_condition[] = "i.emailAddr like :email_addr";
            $bind['email_addr'] = '%'.$email_addr.'%';
        }

        if($start_date)
        {
            $cte_condition[] = 'i.createDate >= :start_date';
            $bind['start_date'] = $start_date;
        }

        if($end_date)
        {
            $cte_condition[] = 'i.createDate <= dateadd(dd, 1, :end_date)';
            $bind['end_date'] = $end_date;
        }

        if($order_state)
        {
            $cte_condition[] = 'o.state = :order_state';
            $bind['order_state'] = $order_state;
        }

        if(!empty($cte_condition))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition);
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
        with InsuranceInfo_CTE as (
            select ROW_NUMBER() OVER (order by i.createDate desc ) as rownum ,i.id
              ,i.user_userId
              ,i.carNo_id
              ,i.carType_id
              ,i.insuranceParam_id
              ,i.insuranceResult_id
              ,i.state_id
              ,i.payState
              ,convert(varchar(20), i.createDate, 20) as createDate
              ,i.userName
              ,i.phoneNo
              ,i.emailAddr
              ,i.finalResult_id
              ,i.finalParam_id
              ,i.insuranceNo
              ,car.hpzl c_hpzl
              ,car.hphm c_hphm
              ,car.fdjh c_fdjh
              ,car.autoname c_autoname
              ,car.createTime c_createTime
              ,car.isState c_isState
              ,car.frameNumber c_frameNumber
              ,car.province_id c_provinceId
              ,car.city_id c_cityId
              ,o.orderNo as order_no
              ,o.tradeNo as order_trade_no
              ,o.state as order_state
              ,o.payType as order_pay_type
            from Insurance_Info i
              left join IAM_USER u on i.user_userId= u.userid
              left join CarInfo car on car.id = i.carNo_id
              left join (
                          select orderNo, tradeNo, state, payType, relId
                          from PayList
                          where orderType = 'insurance'
                        ) o on o.relId = i.id
            $cte_condition_str
        )
        SELECT i.id
          ,i.user_userId
          ,i.carType_id typeCode
          ,type.carType carType
          ,type.typeName typeName
          ,i.insuranceParam_id
          ,i.userName
          ,i.payState
          ,i.insuranceNo
          ,r.id r_id
          ,r.totalStandard r_totalStandard
          ,r.totalAfterDiscount r_totalAfterDiscount
          ,f.id f_id
          ,f.totalStandard f_totalStandard
          ,f.totalAfterDiscount f_totalAfterDiscount
          ,e.id state_id
          ,e.state stateName
          ,i.createDate
          ,i.phoneNo
          ,i.emailAddr
          ,i.finalParam_id
          ,c_hpzl
          ,c_hphm
          ,c_fdjh
          ,c_autoname
          ,c_createTime
          ,c_isState
          ,c_frameNumber
          ,c_provinceId
          ,p.name c_provinceName
          ,c_cityId
          ,c.name c_cityName
          ,u.uname
          ,u.userid
          ,u.phone
          ,i.order_no
          ,i.order_trade_no
          ,i.order_state
          ,i.order_pay_type
        from InsuranceInfo_CTE i
          left join Insurance_ExactStatus e on e.id = i.state_id
          left join Insurance_Result r on r.id = i.insuranceResult_id
          left join Insurance_FinalResult f on f.id = i.finalResult_id
          left join Insurance_CarType type on type.code = i.carType_id
          left join Province p on p.id = i.c_provinceId
          left join City c on c.id = i.c_cityId
          left join IAM_USER u on u.userid=i.user_userid
        $page_condition
        order by i.id desc
SQL;

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险总数
     * @param $criteria
     * @return array
     */
    public static function getAllInsuranceCount($criteria)
    {
        $condition = array();
        $condition_str = '';
        $bind = array();

        $province_id = null;
        $user_id = null;
        $car_no = null;
        $car_type = null;
        $state = null;
        $user_name = null;
        $phone_no = null;
        $email_addr = null;
        $start_date = null;
        $end_date = null;

        if(!empty($criteria) && is_array($criteria))
        {
            $province_id = isset($criteria['province_id']) ? $criteria['province_id'] : null;
            $user_id = isset($criteria['user_id']) ? $criteria['user_id'] : null;
            $car_no = isset($criteria['car_no']) ? $criteria['car_no'] : null;
            $car_type = isset($criteria['car_type']) ? $criteria['car_type'] : null;
            $state = isset($criteria['state']) ? $criteria['state'] : null;
            $user_name = isset($criteria['user_name']) ? $criteria['user_name'] : null;
            $phone_no = isset($criteria['phone_no']) ? $criteria['phone_no'] : null;
            $email_addr = isset($criteria['email_addr']) ? $criteria['email_addr'] : null;
            $start_date = isset($criteria['start_date']) ? $criteria['start_date'] : null;
            $end_date = isset($criteria['end_date']) ? $criteria['end_date'] : null;
        }

        if($province_id)
        {
            $condition[] = 'u.provinceId = :province_id';
            $bind['province_id'] = $province_id;
        }

        if($user_id)
        {
            $condition[] = "i.user_userId like :user_id";
            $bind['user_id'] = '%'.$user_id.'%';
        }

        if($car_no)
        {
            $condition[] = "car.hphm like :car_no";
            $bind['car_no'] = '%'.$car_no.'%';
        }

        if($car_type and $car_type != '-1')
        {
            $condition[] = 'i.carType_id = :car_type';
            $bind['car_type'] = $car_type;
        }

        if($state and $state != '-1')
        {
            if($state != 4)
            {
                $condition[] = 'i.state_id = :state';
            }
            else
            {
                $condition[] = "i.state_id = :state and i.payState = 1";
            }

            $bind['state'] = $state;
        }

        if($user_name)
        {
            $condition[] = "i.userName like :user_name";
            $bind['user_name'] = '%'.$user_name.'%';
        }

        if($phone_no)
        {
            $condition[] = "i.phoneNo like :phone_no";
            $bind['phone_no'] = '%'.$phone_no.'%';
        }

        if($email_addr)
        {
            $condition[] = "i.emailAddr like :email_addr";
            $bind['email_addr'] = '%'.$email_addr.'%';
        }

        if($start_date)
        {
            $condition[] = 'i.createDate >= :start_date';
            $bind['start_date'] = $start_date;
        }

        if($end_date)
        {
            $condition[] = 'i.createDate <= dateadd(dd, 1, :end_date)';
            $bind['end_date'] = $end_date;
        }

        if(!empty($condition))
        {
            $condition_str = 'where '.implode(' and ', $condition);
        }

        $sql = <<<SQL
        select count(i.id) from Insurance_Info i
		left join IAM_USER u on u.userId = i.user_userId
		left join CarInfo car on car.id = i.carNo_id
		%s
SQL;
        $sql = sprintf($sql, $condition_str);

        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);

        return $result[0][0];
    }

    /**
     * @param $id
     * @return object
     */
    public static function getInsuranceInfoById($id)
    {
        $sql = <<<SQL
            SELECT i.id
	    	,i.user_userId userId
	      	,i.carType_id typeCode
	      	,type.carType carType
	      	,type.typeName typeName
	      	,i.insuranceParam_id
	      	,i.finalParam_id
	      	,i.insuranceNo
	      	,a.id a_id
	      	,a.address a_address
	      	,i.insuranceResult_id r_insuranceResult_id
	      	,r.id r_id
	      	,r.totalStandard r_totalStandard
	      	,r.totalAfterDiscount r_totalAfterDiscount
	      	,f.id f_id
	      	,f.totalStandard f_totalStandard
	      	,f.totalAfterDiscount f_totalAfterDiscount
	      	,e.id state_id
	      	,e.state stateName
	      	,i.createDate
	      	,i.userName
	      	,i.phoneNo
	      	,i.emailAddr
	      	,i.sfzh
	      	,i.failureReason
		    ,car.hpzl c_hpzl
		    ,car.hphm c_hphm
		    ,car.fdjh c_fdjh
		    ,car.autoname c_autoname
		    ,car.createTime c_createTime
		    ,car.isState c_isState
		    ,car.frameNumber c_frameNumber
		    ,car.province_id c_provinceId
		    ,p.name c_provinceName
		    ,car.city_id c_cityId
		    ,c.name c_cityName
		from Insurance_Info i
		left join Insurance_ExactStatus e on e.id = i.state_id
		left join Insurance_Result r on r.id = i.insuranceResult_id
		left join Insurance_FinalResult f on f.id = i.finalResult_id
		left join Insurance_CarType type on type.code = i.carType_id
		left join CarInfo car on car.id = i.carNo_id
		left join Province p on p.id = car.province_Id
		left join City c on c.id = car.city_Id
		left join Insurance_Address a on a.id = i.address_id
		where i.id = :id
SQL;

        $bind = array('id' => $id);

        return self::fetchOne($sql, $bind);
    }

    /**
     * 更新保险信息
     * @param $id
     * @param $criteria
     * @return bool
     */
    public static function updateInsuranceInfo($id, $criteria=array())
    {

        $sql = 'update Insurance_Info set %s where id = :id';
        $bind = array('id' => $id);
        $field_str = 'lastModifiedTime = getdate(), ';

        $state_id = isset($criteria['state_id']) ? $criteria['state_id'] : null;

        $final_param_id = isset($criteria['final_param_id']) ? $criteria['final_param_id'] : null;

        $final_result_id = isset($criteria['final_result_id']) ? $criteria['final_result_id'] : null;

        $create_date = isset($criteria['create_date']) ? $criteria['create_date'] : null;

        $insurance_no = isset($criteria['insurance_no']) ? $criteria['insurance_no'] : null;

        $failure_reason = isset($criteria['failure_reason']) ? $criteria['failure_reason'] : null;

        $issuing_time = isset($criteria['issuing_time']) ? $criteria['issuing_time'] : null;

        $actul_amount = isset($criteria['actul_amount']) ? $criteria['actul_amount'] : null;

        $preference_items = isset($criteria['preference_items']) ? $criteria['preference_items'] : null;

        if($state_id or $state_id === 0 or $state_id === '0')
        {
            $field_str .= 'state_id = :state_id, ';
            $bind['state_id'] = $state_id;
        }

        if($final_param_id or $final_param_id === 0 or $final_param_id === '0')
        {
            $field_str .= 'finalParam_id = :final_param_id, ';
            $bind['final_param_id'] = $final_param_id;
        }

        if($final_result_id or $final_result_id === 0 or $final_result_id === '0')
        {
            $field_str .= 'finalResult_id = :final_result_id, ';
            $bind['final_result_id'] = $final_result_id;
        }

        if($create_date)
        {
            $field_str .= 'createDate = :create_date, ';
            $bind['create_date'] = $create_date;
        }

        if($insurance_no)
        {
            $field_str .= 'insuranceNo = :insurance_no, ';
            $bind['insurance_no'] = $insurance_no;
        }

        if($failure_reason)
        {
            $field_str .= 'failureReason = :failure_reason, ';
            $bind['failure_reason'] = $failure_reason;
        }

        if($issuing_time)
        {
            $field_str .= 'issuingTime = :issuing_time, ';
            $bind['issuing_time'] = $issuing_time;
        }

        if($actul_amount)
        {
            $field_str .= 'actulAmount = :actul_amount, ';
            $bind['actul_amount'] = $actul_amount;
        }

        if($preference_items)
        {
            $field_str .= 'preferenceItems = :preference_items, ';
            $bind['preference_items'] = $preference_items;
        }

        if($field_str)
        {
            $field_str = rtrim($field_str, ', ');
        }

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $id
     * @return object
     */
    public static function getInsuranceParamById($id)
    {
        $sql = <<<SQL
		SELECT p.id
			,p.carPrice
			,p.carSeat
			,p.firstYear
			,p.firstMonth
			,p.insuranceYear
			,p.insuranceMonth
			,p.compulsory_id
			,c.status compulsoryName
			,p.damage_id
			,damage.status damageName
			,p.third
			,p.driver
			,p.passenger
			,p.robbery_id
			,robbery.status robberyName
			,p.glass_id
			,g.status glassName
			,p.optionalDeductible
			,p.notDeductible_id
			,non.status notDeductibleName
			,p.newDevice
			,p.goods
			,p.offshore
			,p.ton
			,p.scratch
			,p.selfIgnition_id
			,sel.status selfIgnitionName
			,p.createDate
			,p.tax
			,p.displacement
			,d.companyId d_companyId
			,d.companyName d_companyName
			,d.ename d_ename
			,d.discount d_discount
		FROM Insurance_Param p
		left join Inrurance_Compulsory c on p.compulsory_id = c.id
		left join Inrusance_Glass g on p.glass_id = g.id
		left join Insurance_Discount d on p.discount_companyId = d.companyId
		left join Insurance_InsureStatus damage on p.damage_id = damage.id
		left join Insurance_InsureStatus robbery on p.robbery_id = robbery.id
		left join Insurance_InsureStatus non on p.notDeductible_id = non.id
		left join Insurance_InsureStatus sel on p.selfIgnition_id = sel.id
		where p.id = :id
SQL;

        $bind = array('id' => $id);

        return self::fetchOne($sql, $bind, null, Db::FETCH_OBJ);
    }

    /**
     * @param $id
     * @return object
     */
    public static function getInsuranceFinalParamById($id)
    {
        $sql = <<<SQL
        		SELECT p.id
			,p.carPrice
			,p.carSeat
			,p.firstYear
			,p.firstMonth
			,p.insuranceYear
			,p.insuranceMonth
			,p.compulsory_id
			,c.status compulsoryName
			,p.damage_id
			,damage.status damageName
			,p.third
			,p.driver
			,p.passenger
			,p.robbery_id
			,robbery.status robberyName
			,p.glass_id
			,g.status glassName
			,p.optionalDeductible
			,p.notDeductible_id
			,non.status notDeductibleName
			,p.newDevice
			,p.goods
			,p.offshore
			,p.ton
			,p.scratch
			,p.selfIgnition_id
			,sel.status selfIgnitionName
			,p.createDate
			,p.tax
			,p.displacement
			,d.companyId d_companyId
			,d.companyName d_companyName
			,d.ename d_ename
			,d.discount d_discount
		FROM Insurance_FinalParam p
		left join Inrurance_Compulsory c on p.compulsory_id = c.id
		left join Inrusance_Glass g on p.glass_id = g.id
		left join Insurance_Discount d on p.discount_companyId = d.companyId
		left join Insurance_InsureStatus damage on p.damage_id = damage.id
		left join Insurance_InsureStatus robbery on p.robbery_id = robbery.id
		left join Insurance_InsureStatus non on p.notDeductible_id = non.id
		left join Insurance_InsureStatus sel on p.selfIgnition_id = sel.id
		where p.id = :id
SQL;

        $bind = array('id' => $id);
        return self::fetchOne($sql, $bind);
    }

    /**
     * 保存精算参数
     * @param $info_id
     * @param $criteria
     * @return bool|int
     */
    public static function saveFinalParam($info_id, $criteria)
    {
        $sql_exist = 'select finalParam_id from Insurance_Info where id = :info_id';
        $bind_exist = array(
            'info_id' => $info_id
        );
        $exist = self::nativeQuery($sql_exist, $bind_exist);
        $exist_param_id = $exist['finalParam_id'];

        $connection = self::_getConnection();
        $connection->begin();

        if($exist_param_id)
        {
            $delete_param_sql = "delete from Insurance_FinalParam where id = $exist_param_id";
            $delete_param_success = $connection->execute($delete_param_sql);

            if(!$delete_param_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $new_param_id = self::addInsuranceFinalParam($criteria);

        if(!$new_param_id)
        {
            $connection->rollback();
            return false;
        }

        return $new_param_id;
    }

    /**
     * @param $id
     * @return object
     */
    public static function getInsuranceResultById($id)
    {
        $sql = <<<SQL
		SELECT id
		     ,roundYear
		     ,lastMonth
		     ,roundMonth
		     ,coefficient
		     ,taxMoney
		     ,standardCompulsoryInsurance
		     ,afterDiscountCompulsoryInsurance
		     ,singleNotDeductibleCompulsoryInsurance
		     ,standardDamageInsurance
		     ,afterDiscountDamageInsurance
		     ,singleNotDeductibleDamageInsurance
		     ,standardThird
		     ,afterDiscountThird
		     ,singleNotDeductibleThird
		     ,standardDriver
		     ,afterDiscountDriver
		     ,singleNotDeductibleDriver
		     ,standardPassenger
		     ,afterDiscountPassenger
		     ,singleNotDeductiblePassenger
		     ,standardRobbery
		     ,afterDiscountRobbery
		     ,singleNotDeductibleRobbery
		     ,standardGlass
		     ,afterDiscountGlass
		     ,singleNotDeductibleGlass
		     ,standardOptionalDeductible
		     ,afterDiscountOptionalDeductible
		     ,standardNotDeductible
		     ,afterDiscountNotDeductible
		     ,totalStandard
		     ,totalAfterDiscount
		     ,totalSingleNotDeductible
		     ,standardNewDevice
		     ,afterDiscountNewDevice
		     ,standardGoods
		     ,afterDiscountGoods
		     ,standardOffshore
		     ,afterDiscountOffshore
		     ,trailerStandardCompulsory
		     ,trailerPreferentialCompulsory
		     ,trailerStandardDamage
		     ,trailerPreferentialDamage
		     ,trailerStandardThird
		     ,trailerPreferentialThird
		     ,trailerStandardDriver
		     ,trailerPreferentialDriver
		     ,trailerStandardPassenger
		     ,trailerPreferentialPassenger
		     ,trailerStandardRobbery
		     ,trailerPreferentialRobbery
		     ,trailerStandardGlass
		     ,trailerPreferentialGlass
		     ,trailerStandardOptionalDeductible
		     ,trailerPreferentialOptionalDeductible
		     ,trailerStandardNotDeductible
		     ,trailerPreferentialNotDeductible
		     ,trailerStandardNewDevice
		     ,trailerPreferentialNewDevice
		     ,trailerStandardGoods
		     ,trailerPreferentialGoods
		     ,trailerStandardOffshore
		     ,trailerPreferentialOffshore
		     ,standardScratch
		     ,afterDiscountScratch
		     ,singleNotDeductibleScratch
		     ,standardSelfIgnition
		     ,afterDiscountSelfIgnition
		     ,singleNotDeductibleSelfIgnition
		     ,business
             ,giftMoney
		 FROM Insurance_Result where id = :id
SQL;

        $bind = array('id' => $id);

        return self::fetchOne($sql, $bind);

    }

    /**
     * @param $data
     * @return int | bool
     */
    public static function addInsuranceFinalResult($data)
    {

        $default_values = array(
            'round_year' => null,
			'last_month' => null,
			'round_month' => null,
			'coefficient' => null,
			'standard_compulsory_insurance' => null,
			'after_discount_compulsory_insurance' => null,
			'single_not_deductible_compulsory_insurance' => null,
			'standard_damage_insurance' => null,
			'after_discount_damage_insurance' => null,
			'single_not_deductible_damage_insurance' => null,
			'standard_third' => null,
			'after_discount_third' => null,
			'single_not_deductible_third' => null,
			'standard_driver' => null,
			'after_discount_driver' => null,
			'single_not_deductible_driver' => null,
			'standard_passenger' => null,
			'after_discount_passenger' => null,
			'single_not_deductible_passenger' => null,
			'standard_robbery' => null,
			'after_discount_robbery' => null,
			'single_not_deductible_robbery' => null,
			'standard_glass' => null,
			'after_discount_glass' => null,
			'single_not_deductible_glass' => null,
			'standard_optional_deductible' => null,
			'after_discount_optional_deductible' => null,
			'standard_not_deductible' => null,
			'after_discount_not_deductible' => null,
			'total_standard' => null,
			'total_after_discount' => null,
			'total_single_not_deductible' => null,
			'standard_new_device' => null,
			'after_discount_new_device' => null,
			'standard_goods' => null,
			'after_discount_goods' => null,
			'standard_offshore' => null,
			'after_discount_offshore' => null,
			'trailer_standard_compulsory' => null,
			'trailer_preferential_compulsory' => null,
			'trailer_standard_damage' => null,
			'trailer_preferential_damage' => null,
			'trailer_standard_third' => null,
			'trailer_preferential_third' => null,
			'trailer_standard_driver' => null,
			'trailer_preferential_driver' => null,
			'trailer_standard_passenger' => null,
			'trailer_preferential_passenger' => null,
			'trailer_standard_robbery' => null,
			'trailer_preferential_robbery' => null,
			'trailer_standard_glass' => null,
			'trailer_preferential_glass' => null,
			'trailer_standard_optional_deductible' => null,
			'trailer_preferential_optional_deductible' => null,
			'trailer_standard_not_deductible' => null,
			'trailer_preferential_not_deductible' => null,
			'trailer_standard_new_device' => null,
			'trailer_preferential_new_device' => null,
			'trailer_standard_goods' => null,
			'trailer_preferential_goods' => null,
			'trailer_standard_offshore' => null,
			'trailer_preferential_offshore' => null,
			'standard_scratch' => null,
			'after_discount_scratch' => null,
			'single_not_deductible_scratch' => null,
			'standard_self_ignition' => null,
			'after_discount_self_ignition' => null,
			'single_not_deductible_self_ignition' => null,
			'business' => null
            ,'gift_money' => null
        );

        if($data and is_array($data))
        {
            foreach($data as $field => $value)
            {
                if(!array_key_exists($field, $default_values)) continue;
                $default_values[$field] = $value;
            }
        }

        $bind = $default_values;

        $sql = <<<SQL
        insert into Insurance_FinalResult(
			roundYear
			,lastMonth
			,roundMonth
			,coefficient
			,standardCompulsoryInsurance
			,afterDiscountCompulsoryInsurance
			,singleNotDeductibleCompulsoryInsurance
			,standardDamageInsurance
			,afterDiscountDamageInsurance
			,singleNotDeductibleDamageInsurance
			,standardThird
			,afterDiscountThird
			,singleNotDeductibleThird
			,standardDriver
			,afterDiscountDriver
			,singleNotDeductibleDriver
			,standardPassenger
			,afterDiscountPassenger
			,singleNotDeductiblePassenger
			,standardRobbery
			,afterDiscountRobbery
			,singleNotDeductibleRobbery
			,standardGlass
			,afterDiscountGlass
			,singleNotDeductibleGlass
			,standardOptionalDeductible
			,afterDiscountOptionalDeductible
			,standardNotDeductible
			,afterDiscountNotDeductible
			,totalStandard
			,totalAfterDiscount
			,totalSingleNotDeductible
			,standardNewDevice
			,afterDiscountNewDevice
			,standardGoods
			,afterDiscountGoods
			,standardOffshore
			,afterDiscountOffshore
			,trailersStandardCompulsory
			,trailerPreferentialCompulsory
			,trailersStandardDamage
			,trailerPreferentialDamage
			,trailersStandardThird
			,trailerPreferentialThird
			,trailersStandardDriver
			,trailerPreferentialDriver
			,trailersStandardPassenger
			,trailerPreferentialPassenger
			,trailersStandardRobbery
			,trailerPreferentialRobbery
			,trailersStandardGlass
			,trailerPreferentialGlass
			,trailersStandardOptionalDeductible
			,trailerPreferentialOptionalDeductible
			,trailersStandardNotDeductible
			,trailerPreferentialNotDeductible
			,trailersStandardNewDevice
			,trailerPreferentialNewDevice
			,trailersStandardGoods
			,trailerPreferentialGoods
			,trailersStandardOffshore
			,trailerPreferentialOffshore
			,standardScratch
			,afterDiscountScratch
			,singleNotDeductibleScratch
			,standardSelfIgnition
			,afterDiscountSelfIgnition
			,singleNotDeductibleSelfIgnition
			,business
            ,giftMoney
		)values(
			:round_year
			,:last_month
			,:round_month
			,:coefficient
			,:standard_compulsory_insurance
			,:after_discount_compulsory_insurance
			,:single_not_deductible_compulsory_insurance
			,:standard_damage_insurance
			,:after_discount_damage_insurance
			,:single_not_deductible_damage_insurance
			,:standard_third
			,:after_discount_third
			,:single_not_deductible_third
			,:standard_driver
			,:after_discount_driver
			,:single_not_deductible_driver
			,:standard_passenger
			,:after_discount_passenger
			,:single_not_deductible_passenger
			,:standard_robbery
			,:after_discount_robbery
			,:single_not_deductible_robbery
			,:standard_glass
			,:after_discount_glass
			,:single_not_deductible_glass
			,:standard_optional_deductible
			,:after_discount_optional_deductible
			,:standard_not_deductible
			,:after_discount_not_deductible
			,:total_standard
			,:total_after_discount
			,:total_single_not_deductible
			,:standard_new_device
			,:after_discount_new_device
			,:standard_goods
			,:after_discount_goods
			,:standard_offshore
			,:after_discount_offshore
			,:trailer_standard_compulsory
			,:trailer_preferential_compulsory
			,:trailer_standard_damage
			,:trailer_preferential_damage
			,:trailer_standard_third
			,:trailer_preferential_third
			,:trailer_standard_driver
			,:trailer_preferential_driver
			,:trailer_standard_passenger
			,:trailer_preferential_passenger
			,:trailer_standard_robbery
			,:trailer_preferential_robbery
			,:trailer_standard_glass
			,:trailer_preferential_glass
			,:trailer_standard_optional_deductible
			,:trailer_preferential_optional_deductible
			,:trailer_standard_not_deductible
			,:trailer_preferential_not_deductible
			,:trailer_standard_new_device
			,:trailer_preferential_new_device
			,:trailer_standard_goods
			,:trailer_preferential_goods
			,:trailer_standard_offshore
			,:trailer_preferential_offshore
			,:standard_scratch
			,:after_discount_scratch
			,:single_not_deductible_scratch
			,:standard_self_ignition
			,:after_discount_self_ignition
			,:single_not_deductible_self_ignition
			,:business
            ,:gift_money
		)
SQL;

        $success =  self::nativeExecute($sql, $bind);

        if($success)
        {
            $connection = self::_getConnection();
            $success = $connection->lastInsertId();
        }

        return $success;
    }

    /**
     * @param $id
     * @return bool
     */
    public static function delInsuranceFinalResult($id)
    {
        $sql = 'delete from Insurance_FinalResult where id = :id';
        $bind = array('id' => $id);
        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $data
     * @return int | bool
     */
    public static function addInsuranceFinalParam($data)
    {
        $default_values = array(
            'car_price' => null,
			'car_seat' => null,
			'first_year' => null,
			'first_month' => null,
			'insurance_year' => null,
			'insurance_month' => null,
			'compulsory_id' => null,
			'damage_id' => null,
			'third' => null,
			'driver' => null,
			'passenger' => null,
			'robbery_id' => null,
			'glass_id' => null,
			'optional_deductible' => null,
			'not_deductible_id' => null,
			'new_device' => null,
			'goods' => null,
			'offshore' => null,
			'ton' => null,
			'scratch' => null,
			'self_ignition_id' => null,
			'create_date' => date('Y-m-d H:i:s'),
			'discount_company_id' => null,
			'tax' => null
        );

        if($data and is_array($data))
        {
            foreach($data as $field => $value)
            {
                if(!array_key_exists($field, $default_values)) continue;
                $default_values[$field] = $value;
            }
        }

        $bind = $default_values;

        $sql = <<<SQL
          insert into Insurance_FinalParam(
			carPrice
			,carSeat
			,firstYear
			,firstMonth
			,insuranceYear
			,insuranceMonth
			,compulsory_id
			,damage_id
			,third
			,driver
			,passenger
			,robbery_id
			,glass_id
			,optionalDeductible
			,notDeductible_id
			,newDevice
			,goods
			,offshore
			,ton
			,scratch
			,selfIgnition_id
			,createDate
			,discount_companyId
			,tax
		)values(
			:car_price
			,:car_seat
			,:first_year
			,:first_month
			,:insurance_year
			,:insurance_month
			,:compulsory_id
			,:damage_id
			,:third
			,:driver
			,:passenger
			,:robbery_id
			,:glass_id
			,:optional_deductible
			,:not_deductible_id
			,:new_device
			,:goods
			,:offshore
			,:ton
			,:scratch
			,:self_ignition_id
			,:create_date
			,:discount_company_id
			,:tax
		)
SQL;

        $success =  self::nativeExecute($sql, $bind);

        if($success)
        {
            $connection = self::_getConnection();
            $success = $connection->lastInsertId();
        }

        return $success;
    }

    /**
     * @param $id
     * @return bool
     */
    public static function delInsuranceFinalParam($id)
    {
        $sql = 'delete from Insurance_FinalParam where id = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @param $id
     * @return object
     */
    public static function getInsuranceFinalResultById($id)
    {
        $sql = <<<SQL
              SELECT id
		     ,roundYear
		     ,lastMonth
		     ,roundMonth
		     ,coefficient
		     ,taxMoney
		     ,standardCompulsoryInsurance
		     ,afterDiscountCompulsoryInsurance
		     ,singleNotDeductibleCompulsoryInsurance
		     ,standardDamageInsurance
		     ,afterDiscountDamageInsurance
		     ,singleNotDeductibleDamageInsurance
		     ,standardThird
		     ,afterDiscountThird
		     ,singleNotDeductibleThird
		     ,standardDriver
		     ,afterDiscountDriver
		     ,singleNotDeductibleDriver
		     ,standardPassenger
		     ,afterDiscountPassenger
		     ,singleNotDeductiblePassenger
		     ,standardRobbery
		     ,afterDiscountRobbery
		     ,singleNotDeductibleRobbery
		     ,standardGlass
		     ,afterDiscountGlass
		     ,singleNotDeductibleGlass
		     ,standardOptionalDeductible
		     ,afterDiscountOptionalDeductible
		     ,standardNotDeductible
		     ,afterDiscountNotDeductible
		     ,totalStandard
		     ,totalAfterDiscount
		     ,totalSingleNotDeductible
		     ,standardNewDevice
		     ,afterDiscountNewDevice
		     ,standardGoods
		     ,afterDiscountGoods
		     ,standardOffshore
		     ,afterDiscountOffshore
		     ,trailersStandardCompulsory
		     ,trailerPreferentialCompulsory
		     ,trailersStandardDamage
		     ,trailerPreferentialDamage
		     ,trailersStandardThird
		     ,trailerPreferentialThird
		     ,trailersStandardDriver
		     ,trailerPreferentialDriver
		     ,trailersStandardPassenger
		     ,trailerPreferentialPassenger
		     ,trailersStandardRobbery
		     ,trailerPreferentialRobbery
		     ,trailersStandardGlass
		     ,trailerPreferentialGlass
		     ,trailersStandardOptionalDeductible
		     ,trailerPreferentialOptionalDeductible
		     ,trailersStandardNotDeductible
		     ,trailerPreferentialNotDeductible
		     ,trailersStandardNewDevice
		     ,trailerPreferentialNewDevice
		     ,trailersStandardGoods
		     ,trailerPreferentialGoods
		     ,trailersStandardOffshore
		     ,trailerPreferentialOffshore
		     ,standardScratch
		     ,afterDiscountScratch
		     ,singleNotDeductibleScratch
		     ,standardSelfIgnition
		     ,afterDiscountSelfIgnition
		     ,singleNotDeductibleSelfIgnition
		     ,business
             ,giftMoney
		 FROM Insurance_FinalResult where id = :id
SQL;

        $bind = array('id' => $id);

        return self::fetchOne($sql, $bind);
    }

    /**
     * @return array
     */
    public static function getInsuranceCompany()
    {
        $sql = 'SELECT companyId, discount, companyName, ename FROM Insurance_Discount';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * 获取指定保险信息的已精算的保险公司(新保险可计算多家保险公司)
     * @param $info_id
     * @return array
     */
    public static function getHasActuaryCompany($info_id)
    {
        $sql = <<<SQL
        select c.companyId as id, c.shortName as short_name from
        (
          select company_id from Insurance_Info_To_FinalResult where info_id = :info_id
        ) i2fr
        left join Insurance_Discount c on c.companyId = i2fr.company_id
SQL;
        $bind = array('info_id' => $info_id);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定保险信息的指定保险公司的精算结果
     * @param $info_id
     * @param $company_id
     * @return array
     */
    public static function getFinalResult($info_id, $company_id)
    {
        $sql = <<<SQL
        select top 1 fr.*, i2fr.company_id from
        (
          select company_id, result_id from Insurance_Info_To_FinalResult
          where info_id = :info_id and company_id = :company_id
        ) i2fr
        left join Insurance_Discount c on c.companyId = i2fr.company_id
        left join Insurance_FinalResult fr on fr.id = i2fr.result_id
SQL;
        $bind = array(
            'info_id' => $info_id,
            'company_id' => $company_id
        );

        return self::fetchOne($sql, $bind);
    }

    /**
     * 保存指定保险信息的指定保险公司精算结果
     * @param $info_id
     * @param $company_id
     * @param array|null $criteria
     * @return bool|int
     */
    public static function saveFinalResult($info_id, $company_id, array $criteria=null)
    {
        $sql_exist = 'select top 1 id, result_id from Insurance_Info_To_FinalResult where info_id = :info_id and company_id = :company_id';
        $bind_exist = array(
            'info_id' => $info_id,
            'company_id' => $company_id
        );
        $exist = self::fetchOne($sql_exist, $bind_exist, null, Db::FETCH_ASSOC);
        $exist_i2fr_id = !empty($exist) ? $exist['id'] : null;
        $exist_result_id = !empty($exist) ? $exist['result_id'] : null;

        $connection = self::_getConnection();
        $connection->begin();

        if($exist_result_id)
        {
            $delete_i2rf_sql = "delete from Insurance_Info_To_FinalResult where id = $exist_i2fr_id";
            $delete_i2rf_success = $connection->execute($delete_i2rf_sql);

            if(!$delete_i2rf_success)
            {
                $connection->rollback();
                return false;
            }

            $delete_result_sql = "delete from Insurance_FinalResult where id = $exist_result_id";
            $delete_result_success = $connection->execute($delete_result_sql);

            if(!$delete_result_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $new_result_id = self::addInsuranceFinalResult($criteria);

        if(!$new_result_id)
        {
            $connection->rollback();
            return false;
        }

        $add_i2fr_sql = <<<SQL
        insert into Insurance_Info_To_FinalResult (
        info_id, company_id, result_id
        ) values (
        :info_id, :company_id, :new_result_id
        )
SQL;
        $add_i2fr_bind = array(
            'info_id' => $info_id,
            'company_id' => $company_id,
            'new_result_id' => $new_result_id
        );

        $add_i2fr_success = $connection->execute($add_i2fr_sql, $add_i2fr_bind);

        if(!$add_i2fr_success)
        {
            $connection->rollback();
            return false;
        }

        $success  = $connection->commit();
        if(!$success) return false;

        return $connection->lastInsertId();
    }

    /**
     * 获取保险公司列表(支持分页)
     * @param null $page_num
     * @param null $page_size
     * @return array
     */
    public static function getInsuranceCompanyList($page_num=null, $page_size=null)
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
        with COMP_CTE as (
          select companyId, discount, companyName, ename, gift, gift2,
          ROW_NUMBER() OVER ( order by isOrder asc ) as rownum
          from Insurance_Discount
        )

        select * from COMP_CTE
        %s
SQL;
        $sql = sprintf($sql, $page_condition);
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险公司总数
     * @return int
     */
    public static function getInsuranceCompanyCount()
    {
        $sql = 'select count(companyId) from Insurance_Discount';

        $result = self::fetchOne($sql, null, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 添加保险公司
     * @param null $company_name
     * @param null $ename
     * @param null $discount
     * @param null $gift
     * @param null $gift2
     * @return bool
     */
    public static function addInsuranceCompany($company_name=null, $ename=null,$discount=null, $gift=null, $gift2=null)
    {
        $sql = <<<SQL
        insert into Insurance_Discount(discount,companyName,ename,gift,gift2)
		values(:discount,:company_name,:ename,:gift,:gift2)
SQL;
        $bind = array(
            'discount' => $discount,
            'company_name' => $company_name,
            'ename' => $ename,
            'gift' => $gift,
            'gift2' => $gift2
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新保险公司
     * @param $id
     * @param array $criteria
     * @return bool
     */
    public static function updateInsuranceCompany($id, $criteria=array())
    {
        $sql ='update Insurance_Discount set %s where companyId = :id';
        $bind = array('id' => $id);

        $field_str = '';

        $discount = isset($criteria['discount']) ? $criteria['discount'] : null;
        $company_name = isset($criteria['company_name']) ? $criteria['company_name'] : null;
        $ename = isset($criteria['ename']) ? $criteria['ename'] : null;
        $gift = isset($criteria['gift']) ? $criteria['gift'] : null;
        $gift2 = isset($criteria['gift2']) ? $criteria['gift2'] : null;

        if($discount)
        {
            $field_str .= 'discount = :discount, ';
            $bind['discount'] = $discount;
        }

        if($company_name)
        {
            $field_str .= 'companyName = :company_name, ';
            $bind['company_name'] = $company_name;
        }

        if($ename)
        {
            $field_str .= 'ename = :ename, ';
            $bind['ename'] = $ename;
        }

        if($gift)
        {
            $field_str .= 'gift = :gift, ';
            $bind['gift'] = $gift;
        }

        if($gift2)
        {
            $field_str .= 'gift2 = :gift2, ';
            $bind['gift2'] = $gift2;
        }

        if($field_str)
        {
            $field_str = rtrim($field_str, ', ');
        }

        $sql = sprintf($sql, $field_str);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除保险公司
     * @param $id
     * @return bool
     */
    public static function delInsuranceCompany($id)
    {
        $sql = 'delete from Insurance_Discount where companyId = :id';
        $bind = array('id' => $id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * @return array
     */
    public static function getCompulsoryStateList()
    {
        $sql = 'select id,status from Inrurance_Compulsory';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * @return array
     */
    public static function getGlassStateList()
    {
        $sql = 'SELECT id ,status FROM Inrusance_Glass';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * @return array
     */
    public static function getInsureStatus()
    {
        $sql = 'SELECT id,status FROM Insurance_InsureStatus';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * 获取精算状态列表
     * @return array
     */
    public static function getExactStatusList()
    {
        $sql = 'SELECT id, state FROM Insurance_ExactStatus';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * 获取车辆类型列表
     * @return array
     */
    public static function getCarTypeList()
    {
        $sql = 'SELECT [code], [carType] as [type], [typeName] as [name] FROM Insurance_CarType';
        return self::nativeQuery($sql, null, null, Db::FETCH_OBJ);
    }

    /**
     * 获取保险订单信息
     * @param $insurance_info_id
     * @return object
     */
    public static function getInsuranceOrderInfo($insurance_info_id)
    {
        $sql = <<<SQL
        select i.user_userid as user_id, i.userName as user_name, i.phoneNo as phone,
        o.orderNo as order_no, o.tradeNo as trade_no, o.payType as pay_type, o.money as order_fee,
        o.state, o.payTime as pay_time
        from Insurance_Info i
        left join (
          select orderNo, tradeNo, payType, money, state, payTime, relId from PayList
          where orderType = 'insurance' and relId = :insurance_info_id
        ) o on o.relId = i.id
        where i.id = :insurance_info_id
SQL;
        $bind = array('insurance_info_id' => $insurance_info_id);

        return self::fetchOne($sql, $bind);
    }
}