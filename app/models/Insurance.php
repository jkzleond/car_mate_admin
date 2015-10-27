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
          select companyId, discount, carPriceDiscount, companyName, shortName, ename, gift, gift2, gift3,
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
     * @param array|null $data
     * @return bool
     */
    public static function addInsuranceCompany(array $data=null)
    {
        $crt = new Criteria($data);
        $sql = <<<SQL
        insert into Insurance_Discount(companyName,shortName,ename,discount,carPriceDiscount,gift,gift2,gift3)
		values(:company_name,:short_name,:ename,:discount,:car_price_discount,:gift,:gift2,:gift3)
SQL;
        $bind = array(
            'company_name' => $crt->company_name,
            'short_name' => $crt->short_name,
            'ename' => $crt->ename,
            'discount' => $crt->discount,
            'car_price_discount' => $crt->car_price_discount,
            'gift' => $crt->gift,
            'gift2' => $crt->gift2,
            'gift3' => $crt->gift3
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 更新保险公司
     * @param $id
     * @param array|null $data
     * @return bool
     */
    public static function updateInsuranceCompany($id, $data=null)
    {
        $crt = new Criteria($data);
        $bind = array('id' => $id);

        $field_str = '';

        if($crt->company_name)
        {
          $field_str .= 'companyName = :company_name, ';
          $bind['company_name'] = $crt->company_name;
        }

        if($crt->short_name)
        {
          $field_str .= 'shortName = :short_name, ';
          $bind['short_name'] = $crt->short_name;
        }

        if($crt->ename)
        {
          $field_str .= 'ename = :ename, ';
          $bind['ename'] = $crt->ename;
        }

        if($crt->discount)
        {
          $field_str .= 'discount = :discount, ';
          $bind['discount'] = $crt->discount; 
        }

        if($crt->car_price_discount)
        {
          $field_str .= 'carPriceDiscount = :car_price_discount, ';
          $bind['car_price_discount'] = $crt->car_price_discount; 
        }

        if($crt->gift)
        {
          $field_str .= 'gift = :gift, ';
          $bind['gift'] = $crt->gift;
        }

        if($crt->gift2)
        {
          $field_str .= 'gift2 = :gift2, ';
          $bind['gift2'] = $crt->gift2;
        }

        if($crt->gift3)
        {
          $field_str .= 'gift3 = :gift3, ';
          $bind['gift3'] = $crt->gift3;
        }

        if($field_str)
        {
            $field_str = rtrim($field_str, ', ');
        }

        $sql ="update Insurance_Discount set $field_str where companyId = :id";

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

    /**
     * 获取险种列表
     * @param  array $criteria
     * @param  int   $page_size
     * @param  int   $page_num
     * @return array
     */
    public static function getInsuranceTypeList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_str = ''; //用于构造递归查询的CTE语句
        $cte_condition_str = '';
        $cte_condition_arr = array();
        $page_condition_str = '';
        $bind = array();

        if($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if($crt->ename)
        {
            $cte_condition_arr[] = 'ename like :ename';
            $bind['ename'] = '%'.$crt->ename.'%';
        }


        if($crt->price_type)
        {
            $cte_condition_arr[] = 'price_type = :price_type';
            $bind['price_type'] = $crt->price_type;
        }

        if($crt->is_push || $crt->is_push === '0' || $crt->is_push === 0)
        {
            $cte_condition_arr[] = 'is_push = :is_push';
            $bind['is_push'] = $crt->is_push;
        }

        if($crt->p_type)
        {
            $cte_condition_arr[] = 'pid = :p_type';
            $bind['p_type'] = $crt->p_type;
        }

        if($crt->cate_id)
        {
            $cte_str = <<<CTE
            CATE_CTE as (
              select id, pid from Insurance_Category as cate where pid = :cate_id or id = :cate_id
              union all
              select c.id, c.pid from CATE_CTE as cc
              inner join Insurance_Category c on c.pid = cc.id
            ),
CTE;
            $cte_condition_arr[] = <<<SQL
            type.id in
            (
              select t2c.type_id from CATE_CTE c
              left join Insurance_TypeToCategory t2c on t2c.cate_id = c.id
            )
SQL;
            $bind['cate_id'] = $crt->cate_id;
        }

        if($crt->company_id)
        { 
            $cte_condition_arr[] = '[type].id in (select type_id from Insurance_TypeToCompany t2com2 where t2com2.company_id = :company_id)';
            $bind['company_id'] = $crt->company_id;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size; 
        }

        $sql = <<<SQL
        with
        $cte_str
        INS_TYPE_CTE as (
          select id, name, price_type, script, price, des, is_push,
              stuff(
                  (
                    select ',' + name from Insurance_Category c
                    left join Insurance_TypeToCategory t2c on t2c.cate_id = c.id
                    where t2c.type_id = type.id
                    for xml path('')
                  ), 1, 1, ''
                ) as cates,
              stuff(
                (
                  select ',' + shortName from Insurance_Discount com
                  left join Insurance_TypeToCompany t2com on t2com.company_id = com.companyId
                  where t2com.type_id = type.id
                  for xml path('')
                ), 1, 1, ''
              ) as companise,
              isnull(f.field_num, 0) as field_num,
              row_number() over (order by type.id) as rownum
          from Insurance_Type type
          left join (
            select type_id, count(1) as field_num from Insurance_TypeToField
            group by type_id
          ) f on f.type_id = type.id
          $cte_condition_str
        )
        select * from INS_TYPE_CTE
        $page_condition_str
SQL;
        //echo $sql; exit;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取险种总数
     * @param  array|null $criteria
     * @return int|mixed
     */
    public static function getInsuranceTypeCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $cte_str = '';
        $condition_str = '';
        $condition_arr = array();
        $bind = array();

        if($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if($crt->ename)
        {
            $condition_arr[] = 'ename like :ename';
            $bind['ename'] = '%'.$crt->ename.'%';
        }

        if($crt->price_type)
        {
            $condition_arr[] = 'price_type = :price_type';
            $bind['price_type'] = $crt->price_type;
        }

        if($crt->is_push || $crt->is_push === '0' || $crt->is_push === 0)
        {
            $condition_arr[] = 'is_push = :is_push';
            $bind['is_push'] = $crt->is_push;
        }

        if($crt->p_type)
        {
            $cte_condition_arr[] = 'pid = :p_type';
            $bind['p_type'] = $crt->p_type;
        }

        if($crt->cate_id)
        {
            $cte_str = <<<CTE
            with CATE_CTE as (
              select id, pid from Insurance_Category as cate where pid = :cate_id or id = :cate_id
              union all
              select c.id, c.pid from CATE_CTE as cc
              inner join Insurance_Category c on c.pid = cc.id
            )
CTE;
            $condition_arr[] = <<<SQL
            id in
            (
              select t2c.type_id from CATE_CTE c
              left join Insurance_TypeToCategory t2c on t2c.cate_id = c.id
            )
SQL;
            $bind['cate_id'] = $crt->cate_id;
        }

        if($crt->company_id)
        { 
            $condition_arr[] = <<<SQL
            id in (select type_id from Insurance_TypeToCompany where company_id = :company_id)
SQL;
            $bind['company_id'] = $crt->company_id;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        $cte_str
        select count(1) from Insurance_Type
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 添加险种
     * @param array|null $data
     * @return int       last_insert_id
     */
    public static function addInsuranceType(array $data=null)
    {
        $crt = new Criteria($data);


        $add_type_sql = 'insert into Insurance_Type (name, price_type, price, script, compiled_script, des) values (:name, :price_type, :price, :script, :compiled_script, :des)';

        $add_type_bind = array(
          'name' => $crt->name,
          'price_type' => $crt->price_type,
          'price' => round($crt->price, 2),
          'script' => $crt->script,
          'compiled_script' => $crt->compiled_script,
          'des' => $crt->des
        );

        $connection = self::_getConnection();

        $connection->begin();

        self::nativeExecute($add_type_sql, $add_type_bind);

        $new_insurance_type_id = $connection->lastInsertId();

        if(!$new_insurance_type_id)
        {
            $connection->rollback();
            return false;
        }

        $cates = $crt->cates; //Criteria代理类,cates属性实际不存在empty返回true
        if(is_array($cates) and !empty($cates))
        {
            $add_type_cate_success = self::addInsuranceTypeCategory($new_insurance_type_id, $cates);
            if(!$add_type_cate_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $fields = $crt->fields;
        if(is_array($fields) and !empty($fields))
        {
            $add_type_field_success = self::addInsuranceTypeField($new_insurance_type_id, $fields);
            if(!$add_type_field_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $companise = $crt->companise;
        if(is_array($companise) and !empty($companise))
        {
            $add_type_companise_success = self::addInsuranceTypeCompany($new_insurance_type_id, $companise);
            if(!$add_type_companise_success)
            {
                $connection->rollback();
                return false;
            }
        }

        return $connection->commit() ? $new_insurance_type_id : false;
    }

    /**
     * 跟新险种
     * @param  int|string $type_id
     * @param  array|null $data
     * @return bool
     */
    public static function updateInsuranceType($type_id, array $criteria=null)
    {
        $update_type_crt = new Criteria($criteria);
        $update_type_field_str = '';
        $update_type_bind = array('id' => $type_id);

        if($update_type_crt->name)
        {
            $update_type_field_str .= 'name = :name, ';
            $update_type_bind['name'] = $update_type_crt->name;
        }

        if($update_type_crt->price_type)
        {
            $update_type_field_str .= 'price_type = :price_type, ';
            $update_type_bind['price_type'] = $update_type_crt->price_type;
        }

        if($update_type_crt->price)
        {
            $update_type_field_str .= 'price = :price, ';
            $update_type_bind['price'] = $update_type_crt->price;
        }

        if($update_type_crt->script)
        {
            $update_type_field_str .= 'script = :script, ';
            $update_type_bind['script'] = $update_type_crt->script;
        }

        if($update_type_crt->compiled_script)
        {
            $update_type_field_str .= 'compiled_script = :compiled_script, ';
            $update_type_bind['compiled_script'] = $update_type_crt->compiled_script;
        }

        if($update_type_crt->des)
        {
            $update_type_field_str .= 'des = :des, ';
            $update_type_bind['des'] = $update_type_crt->des;
        }

        if($update_type_crt->is_push || $update_type_crt->is_push === '0' || $update_type_crt->is_push === 0)
        {
            $update_type_field_str .= 'is_push = :is_push, ';
            $update_type_bind['is_push'] = $update_type_crt->is_push;
        }

        $update_type_field_str = rtrim($update_type_field_str, ', ');

        $update_type_sql = <<<SQL
        update Insurance_Type set $update_type_field_str where id = :id
SQL;
        $connection = self::_getConnection();
        $connection->begin();

        $update_type_success =  self::nativeExecute($update_type_sql, $update_type_bind);
        if(!$update_type_success)
        {
            $connection->rollback();
            return false;
        }

        $cates = $update_type_crt->cates; //Criteria代理类,cates属性实际不存在empty返回true
        if(is_array($cates) and !empty($cates))
        {
            $del_type_cate_success = self::delInsuranceTypeCategory($type_id);
            if(!$del_type_cate_success)
            {
                $connection->rollback();
                return false;
            }

            $add_type_cate_success = self::addInsuranceTypeCategory($type_id, $cates);

            if(!$add_type_cate_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $fields = $update_type_crt->fields;
        if(is_array($fields) and !empty($fields))
        {
            $del_type_field_success = self::delInsuranceTypeField($type_id);
            if(!$del_type_field_success)
            {
                $connection->rollback();
                return false;
            }

            $add_type_field_success = self::addInsuranceTypeField($type_id, $fields);
            if(!$add_type_field_success)
            {
                $connection->rollback();
                return false;
            }
        }

        $companise = $update_type_crt->companise;
        if(is_array($companise) and !empty($companise))
        {
            $del_type_companise_success = self::delInsuranceTypeCompany($type_id);
            if(!$del_type_companise_success)
            {
                $connection->rollback();
                return false;
            }

            $add_type_companise_success = self::addInsuranceTypeCompany($type_id, $companise);
            if(!$add_type_companise_success)
            {
                $connection->rollback();
                return false;
            }
        }

        return $connection->commit();
    }

    /**
     * 删除指定ID险种
     * @param  int|string $type_id
     * @return bool
     */
    public static function delInsuranceType($type_id)
    {
        $sql = 'delete from Insurance_Type where id = :type_id';
        $bind = array('type_id' => $type_id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取指定ID险种所属类目列表
     * @param  int|string $type_id
     * @return array
     */
    public static function getInsuranceTypeCategoryList($type_id)
    {
        $sql = 'select cate_id as id from Insurance_TypeToCategory where type_id = :type_id';
        $bind = array('type_id' => $type_id);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定ID险种字段列表
     * @param  int|string $type_id
     * @return array
     */
    public static function getInsuranceTypeFieldList($type_id)
    {
        $sql = <<<SQL
        select f.id, f.name, f.ename, f.[type], f.[values], f.[default], f.prefix, f.suffix, f.des,
        t2f.links, t2f.parts, t2f.default_disabled, t2f.default_visible
        from Insurance_TypeToField t2f
        left join Field f on f.id = t2f.field_id
        where type_id = :type_id
SQL;
        $bind = array('type_id' => $type_id);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取指定ID险种支持保险公司列表
     * @param  int|string $type_id
     * @return array
     */
    public static function getInsuranceTypeCompanyList($type_id)
    {
        $sql = <<<SQL
        select com.companyId, com.companyName, com.ename, com.shortName,
        t2com.discount_type, t2com.discount_percent, t2com.discount_setting
        from Insurance_TypeToCompany t2com
        left join Insurance_Discount com on com.companyId = t2com.company_id
        where type_id = :type_id
SQL;
        $bind = array('type_id' => $type_id);

        return self::nativeQuery($sql, $bind);
    }

    /**
     * 添加险种类目
     * @param int|string $type_id
     * @param array  $cate_ids
     * @return bool
     */
    public static function addInsuranceTypeCategory($type_id, array $cate_ids)
    {
        $values_str = '';
        $bind = array('type_id' => $type_id);

        foreach($cate_ids as $index => $cate_id)
        {
            $values_str .= '(:type_id,'.':cate_id_'.$index.'), ';
            $bind['cate_id_'.$index] = $cate_id;
        }

        $values_str = rtrim($values_str, ', ');

        $sql = "insert into Insurance_TypeToCategory (type_id, cate_id) values $values_str";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加险种字段
     * @param int|string $type_id
     * @param array  $fields
     * @return bool
     */
    public static function addInsuranceTypeField($type_id, array $fields)
    {
        $values_str = '';
        $bind = array('type_id' => $type_id);

        foreach($fields as $index => $field)
        {
            $field_crt = new Criteria($field);
            $values_str .= "(:type_id, :field_id_$index, :parts_$index, :links_$index, :default_disabled_$index, :default_visible_$index), ";
            $bind['field_id_'.$index] = $field_crt->id;
            $bind['parts_'.$index] = $field_crt->parts;

            $links = $field_crt->links;
            $bind['links_'.$index] = ( is_array($links) and !empty($links) ) ? json_encode($links) : '[]';
            $bind['default_disabled_'.$index] = $field_crt->default_disabled;
            $bind['default_visible_'.$index] = $field_crt->default_visible;
        }

        $values_str = rtrim($values_str, ', ');

        $sql = "insert into Insurance_TypeToField (type_id, field_id, parts, links, default_disabled, default_visible) values $values_str";

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 添加险种支持公司
     * @param int|string $type_id
     * @param array  $companise
     * @return bool
     */
    public static function addInsuranceTypeCompany($type_id, array $companise)
    {
        $values_str = '';
        $bind = array('type_id' => $type_id);

        foreach($companise as $index => $company)
        {
            $company_crt = new Criteria($company);
            $values_str .= "(:type_id, :company_id_$index, :discount_type_$index, :discount_percent_$index, :discount_setting_$index), ";
            $bind["company_id_$index"] = $company_crt->id;
            $bind["discount_type_$index"] = $company_crt->discount_type;
            $bind["discount_percent_$index"] = (int) $company_crt->discount_percent;
            $bind["discount_setting_$index"] = $company_crt->discount_setting;
        }

        $values_str = rtrim($values_str, ', ');

        $sql = "insert into Insurance_TypeToCompany (type_id, company_id, discount_type, discount_percent, discount_setting) values $values_str";
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除指定ID险种关联的类目
     * @param  int $type_id
     * @return bool
     */
    public static function delInsuranceTypeCategory($type_id)
    {
        $sql = 'delete from Insurance_TypeToCategory where type_id = :type_id';
        $bind = array('type_id' => $type_id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除指定ID险种字段
     * @param  int $type_id
     * @return bool
     */
    public static function delInsuranceTypeField($type_id)
    {
        $sql = 'delete from Insurance_TypeToField where type_id = :type_id';
        $bind = array('type_id' => $type_id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除指定ID险种类保险公司
     * @param  int $type_id
     * @return bool
     */
    public static function delInsuranceTypeCompany($type_id)
    {
        $sql = 'delete from Insurance_TypeToCompany where type_id = :type_id';
        $bind = array('type_id' => $type_id);

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取保险类目数据列表
     * @param  array  $criteria
     * @param  int    $page_num
     * @param  int    $page_size
     * @return array
     */
    public static function getInsuranceCategoryList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_str = '';
        $cte_condition_arr = array();
        $page_condition_str = '';
        $bind = array();

        if($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if($crt->p_cate)
        {
            $cte_condition_arr[] = 'pid = :p_cate';
            $bind['p_cate'] = $crt->p_cate;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size; 
        }

        $sql = <<<SQL
        with INS_CATE_CTE as (
          select id, name, des, pid, row_number() over(order by display_order asc) as rownum from Insurance_Category
          $cte_condition_str
        )
        select * from INS_CATE_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险类目总数
     * @param  array|null $criteria
     * @return int|string
     */
    public static function getInsuranceCategoryCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_str = '';
        $condition_arr = array();
        $bind = array();

        if($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if($crt->p_cate)
        {
            $cte_condition_arr[] = 'pid = :p_type';
            $bind['p_type'] = $crt->p_type;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1) from Insurance_Category
        $condition_str
SQL;
        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 添加保险类目
     * @param array|null $data
     */
    public static function addInsuranceCategory(array $data=null)
    {
        $crt = new Criteria($data);

        $sql = 'insert into Insurance_Category (name, pid, des) values (:name, :pid, :des)';
        $bind = array(
          'name' => $crt->name,
          'pid' => $crt->pid,
          'des' => $crt->des
        );

        $success = self::nativeExecute($sql, $bind);

        if(!$success) return false;

        $connection = self::_getConnection();

        return $connection->lastInsertId();
    }

    /**
     * 更新险种类目
     * @param  int|string $cate_id
     * @param  array $criteria
     * @return bool
     */
    public static function updateInsuranceCategory($cate_id, array $criteria=null)
    {   
        $crt = new Criteria($criteria);
        $field_str = '';
        $bind = array('id' => $cate_id);

        if($crt->name)
        {
            $field_str .= 'name = :name, ';
            $bind['name'] = $crt->name;
        }

        if($crt->pid)
        {
            $field_str .= 'pid = :pid, ';
            $bind['pid'] = $crt->pid;
        }

        if($crt->display_order)
        {
            $field_str .= 'display_order = :display_order, ';
            $bind['display_order'] = $crt->display_order;
        }

        if($field_str)
        {
            $field_str = rtrim($field_str, ', ');
        }

        $sql = <<<SQL
        update Insurance_Category set $field_str where id = :id
SQL;
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 删除险种类目
     * @param  int|string $cate_id
     * @return bool
     */
    public static function delInsuranceCategory($cate_id)
    {
        $sql = "delete from Insurance_Category where id = :id";
        $bind = array('id' => $cate_id);
        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取保险信息列表(全险种)
     * @param  array|null $criteria
     * @param  [type]     $page_num
     * @param  [type]     $page_size
     * @return [type]
     */
    public static function getInsuranceNewInfoList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'i.user_id like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'u.phone like :phone';
            $bind['phone'] = '%'.$crt->phone.'%';
        }

        if($crt->company_id)
        {
            $cte_condition_arr[] = 'i.company_id = :company_id';
            $bind['company_id'] = $crt->company_id;
        }

        if($crt->type_id)
        {
            $cte_condition_arr[] = 'i.type_id = :type_id';
            $bind['type_id'] = $crt->type_id;
        }

        if($crt->state)
        {
            $cte_condition_arr[] = 'i.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->pay_state)
        {
            $cte_condition_arr[] = 'i.pay_state = :pay_state';
            $bind['pay_state'] = $crt->pay_state;
        }

        if($crt->policy_no)
        {
            $cte_condition_arr[] = 'i.policy_no = :policy_no';
            $bind['policy_no'] = $crt->policy_no;
        }

        if($crt->start_date)
        {
            $cte_condition_arr[] = 'datediff(dd, :start_date, i.create_date) >= 0';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $cte_condition_arr[] = 'datediff(dd, i.create_date, :end_date) >= 0';
            $bind['end_date'] = $crt->end_date;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size; 
        }

        $sql = <<<SQL
        with INFO_CTE as (
          select i.id, i.user_id, i.preliminary_premium, i.policy_fee, i.state, i.pay_state, i.policy_no, convert(varchar(20),
          i.create_date, 20) as create_date, convert(varchar(20), i.pay_date, 20) as pay_date,
          u.uname as user_name, u.phone, t.name as type_name, c.companyName as company_name,
          row_number() over (order by i.create_date desc) as rownum
          from Insurance_NewInfo i
          left join IAM_USER u on u.userid = i.user_id
          left join Insurance_Type t on t.id = i.type_id
          left join Insurance_Discount c on c.companyId = i.company_id
          $cte_condition_str
        )
        select * from INFO_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险信息数据总数(全险种)
     * @param  array|null $criteria
     * @return int
     */
    public static function getInsuranceNewInfoCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';
        $bind = array();

        if($crt->user_id)
        {
            $condition_arr[] = 'i.user_id like :user_id';
            $bind['user_id'] = '%'.$crt->user_id.'%';
        }

        if($crt->phone)
        {
            $condition_arr[] = 'u.phone like :phone';
            $bind['phone'] = '%'.$crt->phone.'%';
        }

        if($crt->company_id)
        {
            $condition_arr[] = 'i.company_id = :company_id';
            $bind['company_id'] = $crt->company_id;
        }

        if($crt->type_id)
        {
            $condition_arr[] = 'i.type_id = :type_id';
            $bind['type_id'] = $crt->type_id;
        }

        if($crt->state)
        {
            $condition_arr[] = 'i.state = :state';
            $bind['state'] = $crt->state;
        }

        if($crt->pay_state)
        {
            $condition_arr[] = 'i.pay_state = :pay_state';
            $bind['pay_state'] = $crt->pay_state;
        }

        if($crt->policy_no)
        {
            $condition_arr[] = 'i.policy_no = :policy_no';
            $bind['policy_no'] = $crt->policy_no;
        }

        if($crt->start_date)
        {
            $condition_arr[] = 'datediff(dd, :start_date, i.create_date) >= 0';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $condition_arr[] = 'datediff(dd, i.create_date, :end_date) >= 0';
            $bind['end_date'] = $crt->end_date;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1)
        from Insurance_NewInfo i
        left join IAM_USER u on u.userid = i.user_id
        left join Insurance_Type t on t.id = i.type_id
        left join Insurance_Discount c on c.companyId = i.company_id
        $condition_str
SQL;

        $result = self::fetchOne($sql, $bind, null, Db::FETCH_NUM);

        return $result[0];
    }

    /**
     * 获取指定保险信息(全险种)
     * @param  int|string $id
     * @return object
     */
    public static function getInsuranceNewInfoById($info_id)
    {
        $sql = <<<SQL
        select i.id, i.user_id, i.type_id, i.company_id, i.state, i.pay_state, i.policy_no,
        i.preliminary_premium, i.policy_fee, i.type_attr, i.preliminary_result, 
        convert(varchar(20), i.create_date, 20) as create_date,
        convert(varchar(20), i.pay_date, 20) as pay_date,
        u.uname as user_name, u.phone,
        t.name as type_name, t.price_type as type_price_type, c.companyName as company_name
        from Insurance_NewInfo i
        left join IAM_USER u on u.userid = i.user_id
        left join Insurance_Type t on t.id = i.type_id
        left join Insurance_Discount c on c.companyId = i.company_id
        where i.id = :info_id
SQL;
        $bind = array('info_id' => $info_id);

        $info = self::fetchOne($sql, $bind);

        if(!empty($info->type_attr))
        {
            $info->type_attr = json_decode($info->type_attr, true);
        }
        else
        {
            $info->type_attr = array();
        }

        if(!empty($info->preliminary_result))
        {
            $info->preliminary_result = json_decode($info->preliminary_result, true);
        }
        else
        {
            $info->preliminary_result = array();
        }

        return $info;
    }

    /**
     * 添加保险信息(全险种)
     * @param array $data
     */
    public static function addInsuranceNewInfo(array $data)
    {
        $crt = new Criteria($data);

        //险种属性
        $type_attr = $crt->type_attr;
        if(is_array($type_attr) && !empty($type_attr))
        {
            $crt->type_attr = json_encode($type_attr);
        }

        //初算结果
        $preliminary_result = $crt->preliminary_result;
        if(is_array($preliminary_result) && !empty($preliminary_result))
        {
            $crt->preliminary_result = json_encode($preliminary_result);
        }

        $sql = 'insert into Insurance_NewInfo (user_id, type_id, type_attr, state, pay_state, preliminary_result, preliminary_premium) values (:user_id, :type_id, :type_attr, :state, :pay_state, :preliminary_result, :preliminary_premium)';

        $bind = array(
            'user_id' => $crt->user_id,
            'type_id' => $crt->type_id,
            'type_attr' => $crt->type_attr,
            'state' => $crt->state ? $crt->state : 0,
            'pay_state' => $crt->pay_state ? $crt->pay_state : 0,
            'preliminary_result' => $crt->preliminary_result,
            'preliminary_premium' => $crt->preliminary_premium
        );

        return self::nativeExecute($sql, $bind);
    }

    /**
     * 获取保险产品数据列表
     * @param  array|null $criteria
     * @param  int        $page_num
     * @param  int        $page_size
     * @return array
     */
    public static function getInsuranceGoodsList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_str = '';
        $cte_condition_arr = array();
        $page_condition_str = '';
        $bind = array();

        if($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size; 
        }

        $sql = <<<SQL
        with INS_GOODS_CTE as (
          select * from Insurance_Goods
          $cte_condition_str
        )
        select * from INS_GOODS_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险产品条目数
     * @param  array|null $criteria
     * @return int
     */
    public static function getInsuranceGoodsCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_str = '';
        $condition_arr = array();
        $bind = array();

        if($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1) from Insurance_Goods
        $condition_str
SQL;
        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取投保方案数据列表
     * @param  array|null $criteria
     * @param  int        $page_num
     * @param  int        $page_size
     * @return array
     */
    public static function getInsuranceSchemeList($criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_str = '';
        $cte_condition_arr = array();
        $page_condition_str = '';
        $bind = array();

        if($crt->name)
        {
            $cte_condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1) * $page_size + 1;
            $bind['to'] = $page_num * $page_size; 
        }

        $sql = <<<SQL
        with INS_SCHEME_CTE as (
          select * from Insurance_Scheme
          $cte_condition_str
        )
        select * from INS_SCHEME_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取投保方案总数
     * @param  array|null $criteria
     * @return int
     */
    public static function getInsuranceSchemeCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_str = '';
        $condition_arr = array();
        $bind = array();

        if($crt->name)
        {
            $condition_arr[] = 'name like :name';
            $bind['name'] = '%'.$crt->name.'%';
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1) from Insurance_Scheme
        $condition_str
SQL;
        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取保险预约数据列表
     * @param  array|null      $criteria
     * @param  integer|null    $page_num
     * @param  integer|null    $page_size
     * @return array
     */
    public static function getInsuranceReservationList(array $criteria=null, $page_num=null, $page_size=null)
    {
        $crt = new Criteria($criteria);
        $cte_condition_arr = array();
        $cte_condition_str = '';
        $page_condition_str = '';
        $bind = array();

        if($crt->user_id)
        {
            $cte_condition_arr[] = 'r.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->phone)
        {
            $cte_condition_arr[] = 'r.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->offer_date)
        {
            $cte_condition_arr[] = 'r.offer_date = :offer_date';
            $bind['offer_date'] = $crt->offer_date;
        }

        if($crt->start_date)
        {
            $cte_condition_arr[] = 'r.create_date >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $cte_condition_arr[] = 'r.create_date <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->user_name)
        {
            $cte_condition_arr[] = 'u.uname = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if($crt->hphm)
        {
            $cte_condition_arr[] = 'c.hphm = :hphm';
            $bind['hphm'] = $crt->hphm;
        }

        if(!empty($cte_condition_arr))
        {
            $cte_condition_str = 'where '.implode(' and ', $cte_condition_arr);
        }

        if($page_num)
        {
            $page_condition_str = 'where rownum between :from and :to';
            $bind['from'] = ($page_num - 1)*$page_size + 1;
            $bind['to'] = $page_num*$page_size;
        }

        $sql = <<<SQL
        with RSV_CTE as (
          select r.id, r.user_id, r.phone, r.mark, convert(varchar(20), r.offer_date, 20) as offer_date, convert(varchar(20), r.create_date, 20) as create_date, u.uname, c.hphm, c.autoname as auto_name, c.frameNumber as frame_number, c.engineNumber as engine_number, row_number() over(order by createDate desc) as rownum 
          from Insurance_Reservation r
          left join IAM_USER u on u.userid = r.user_id
          left join CarInfo c on c.id = r.car_info_id
          $cte_condition_str
        )
        select * from RSV_CTE
        $page_condition_str
SQL;
        return self::nativeQuery($sql, $bind);
    }

    /**
     * 获取保险预约总数
     * @param  array|null $criteria
     * @return mixed
     */
    public static function getInsuranceReservationCount(array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $condition_arr = array();
        $condition_str = '';
        $bind = array();

        if($crt->user_id)
        {
            $condition_arr[] = 'r.user_id = :user_id';
            $bind['user_id'] = $crt->user_id;
        }

        if($crt->phone)
        {
            $condition_arr[] = 'r.phone = :phone';
            $bind['phone'] = $crt->phone;
        }

        if($crt->offer_date)
        {
            $condition_arr[] = 'r.offer_date = :offer_date';
            $bind['offer_date'] = $crt->offer_date;
        }

        if($crt->start_date)
        {
            $condition_arr[] = 'r.create_date >= :start_date';
            $bind['start_date'] = $crt->start_date;
        }

        if($crt->end_date)
        {
            $condition_arr[] = 'r.create_date <= :end_date';
            $bind['end_date'] = $crt->end_date;
        }

        if($crt->user_name)
        {
            $condition_arr[] = 'u.uname = :user_name';
            $bind['user_name'] = $crt->user_name;
        }

        if($crt->hphm)
        {
            $condition_arr[] = 'c.hphm = :hphm';
            $bind['hphm'] = $crt->hphm;
        }

        if(!empty($condition_arr))
        {
            $condition_str = 'where '.implode(' and ', $condition_arr);
        }

        $sql = <<<SQL
        select count(1)
        from Insurance_Reservation r
        left join IAM_USER u on u.userid = r.user_id
        left join CarInfo c on c.id = r.car_info_id
        $condition_str
SQL;
        $result = self::nativeQuery($sql, $bind, null, Db::FETCH_NUM);
        return $result[0];
    }

    /**
     * 更新指定ID保险预约数据
     * @param  int|string $id
     * @param  array|null $criteria
     * @return bool
     */
    public static function updateInsuranceReservation($id, array $criteria=null)
    {
        $crt = new Criteria($criteria);
        $field_str = '';
        $bind = array('id' => $id);

        if($crt->mark)
        {
            $field_str .= 'mark = :mark';
            $bind['mark'] = $crt->mark;
        }

        $sql = "update Insurance_Reservation set $field_str where id =:id";

        return self::nativeExecute($sql, $bind);
    }
}