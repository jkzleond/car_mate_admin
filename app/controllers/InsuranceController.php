<?php

class InsuranceController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 保险列表页面
     */
    public function insuranceListAction()
    {
        $exact_state_list = Insurance::getExactStatusList();
        $car_type_list = Insurance::getCarTypeList();

        $this->view->setVars(array(
            'exact_state_list' => $exact_state_list,
            'car_type_list' => $car_type_list
        ));
    }

    /**
     * 获取保险列表
     */
    public function getInsuranceListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria');
        $insurance_list = Insurance::getAllInsuranceList($criteria, $page_num, $page_size);
        $insurance_total = Insurance::getAllInsuranceCount($criteria);
        $this->view->setVar('data', array(
            'total' => $insurance_total,
            'count' => count($insurance_list),
            'rows' => $insurance_list
        ));
    }

    /**
     * 保险详情/保险精算页面
     */
    public function insuranceResultAction()
    {
        $param_id = $this->request->get('param_id');
        $result_id = $this->request->get('result_id');
        $info_id = $this->request->get('info_id');
        $final_result_id = $this->request->get('final_result_id');
        $final_param_id = $this->request->get('final_param_id');
        $state = $this->request->get('state');
        if($state == 2 || $state == 4 || $state == 1 || $state == 7)
        {
            $info = Insurance::getInsuranceInfoById($info_id);
            $insurance = Insurance::getInsuranceParamById($param_id);
            $result = Insurance::getInsuranceResultById($result_id);
            $discount_list = Insurance::getInsuranceCompany();
            $compulsory_state_list = Insurance::getCompulsoryStateList();
            $glass_state_list = Insurance::getGlassStateList();
            $insurance_status_list = Insurance::getInsureStatus();
        }
        else
        {
            $info = Insurance::getInsuranceInfoById($info_id);
            $insurance = Insurance::getInsuranceFinalParamById($final_param_id);
            $result = Insurance::getInsuranceFinalResultById($final_result_id);
            $discount_list = Insurance::getInsuranceCompany();
            $compulsory_state_list = Insurance::getCompulsoryStateList();
            $glass_state_list = Insurance::getGlassStateList();
            $insurance_status_list = Insurance::getInsureStatus();
        }

        $this->view->setVars(array(
            'info' => $info,
            'insurance' => $insurance,
            'result' => $result,
            'discount_list' => $discount_list,
            'compulsory_state_list' => $compulsory_state_list,
            'glass_state_list' => $glass_state_list,
            'insurance_status_list' => $insurance_status_list
        ));
    }

    /**
     * 保险订单信息页面
     * @param insurance_info_id
     */
    public function insuranceOrderInfoAction($insurance_info_id)
    {
        $insurance_order_info = Insurance::getInsuranceOrderInfo($insurance_info_id);

        $this->view->setVar('order_info', $insurance_order_info);
    }

    /**
     * 获取指定保险信息的已有精算结果的公司
     * @param $info_id
     */
    public function getHasActuaryCompanyAction($info_id)
    {
        $company_list = Insurance::getHasActuaryCompany($info_id);

        $this->view->setVar('data', array(
            'rows' => $company_list
        ));
    }

    /**
     * 获取指定保险信息的指定保险公司精算结果
     * @param $info_id
     * @param $company_id
     */
    public function getFinalResultAction($info_id, $company_id)
    {
        $result = Insurance::getFinalResult($info_id, $company_id);

        $this->view->setVar('data', array(
            'row' => $result
        ));
    }

    /**
     * 保存指定保险信息的指定保险公司尽算结果
     * @param $info_id
     * @param $company_id
     */
    public function saveFinalResultAction($info_id, $company_id)
    {
        $result = $this->request->getPost('data');

        $success = Insurance::saveFinalResult($info_id, $company_id, $result);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 提交无法精算理由
     */
    public function insuranceCantExactReasonAction()
    {
        $data = $this->request->getPut('data');

        $id = $data['id'];
        $reason = $data['reason'];

        $criteria = array(
            'failure_reason' => $reason,
            'state_id' => '7'
        );

        $success = Insurance::updateInsuranceInfo($id, $criteria);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 保险出单
     */
    public function insuranceIssuingAction()
    {
        $data = $this->request->getPut('data');
        $id = $data['id'];
        $issuing_time = $data['issuing_time'] ? $data['issuing_time'] : date('Y-m-d H:i:s');
        $actul_amount = $data['actul_amount'];
        $preference_items = $data['preference_items'];

        $user_id = $data['user_id'];

        $success = Insurance::updateInsuranceInfo($id, array(
            'issuing_time' => $issuing_time,
            'actul_amount' => $actul_amount,
            'preference_items' => $preference_items,
            'state_id' => '5'
        ));

        //添加推送数据
        if($success)
        {
            $push_title = '您的车险保单已出单，请登录本地惠保险巨惠进行查看！';
            PushNotifications::addPushMessage(2, $user_id, null, null, $push_title, 0);
        }

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 交易完成
     */
    public function insuranceCompleteAction()
    {
        $data = $this->request->getPut('data');

        $id = $data['id'];

        $success = Insurance::updateInsuranceInfo($id, array(
            'state_id' => '6'
        ));

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新保险结果
     */
    public function updateInsuranceResultAction()
    {
        $data = $this->request->getPut('data');
        $info_id = $data['info_id'];
        $user_id = $data['user_id'];

        $pid = Insurance::addInsuranceFinalParam($data);

        $rid = Insurance::addInsuranceFinalResult($data);

        $success = false;

        if(($pid or $pid === 0 or $pid === '0') and ($rid or $rid === 0 or $rid === '0'))
        {
            $success = Insurance::updateInsuranceInfo($info_id, array(
                'state_id' => '3',
                'final_param_id' => $pid,
                'final_result_id' => $rid
            ));
        }

        if($success)
        {
            PushNotifications::addPushMessage(2, $user_id, null, null, '您的保单已精算完成，居然有那么多福利，快来本地惠-保险巨惠-我的订单看看吧！', 0);
        }

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 保险购买处理页面
     */
    public function insuranceBuyAction()
    {
        $info_id = $this->request->get('info_id');
        $param_id = $this->request->get('param_id');
        $result_id = $this->request->get('result_id');

        $info = Insurance::getInsuranceInfoById($info_id);
        $param = Insurance::getInsuranceFinalParamById($param_id);
        $result = Insurance::getInsuranceFinalResultById($result_id);

        $this->view->setVars(array(
            'info' => $info,
            'insurance_param' => $param,
            'insurance_result' => $result
        ));
    }

    /**
     * 生成保单
     */
    public function genInsuranceNoAction()
    {
        $data = $this->request->getPut('data');

        $info_id = $data['info_id'];
        $user_id = $data['user_id'];
        $insurance_no = $data['insurance_no'];

        $success = Insurance::updateInsuranceInfo($info_id, array(
            'insurance_no' => $insurance_no,
            'state_id' => '5'
        ));

        //添加推送数据
        if($success)
        {
            $push_title = '您的车险保单已出单，请登录本地惠保险巨惠进行查看！';
            PushNotifications::addPushMessage(2, $user_id, null, null, $push_title, 0);
        }

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 保险公司管理页面
     */
    public function companyAction()
    {

    }

    /**
     * 获取保险公司列表
     */
    public function getInsuranceCompanyListAction()
    {
        $page_num = $this->request->getPost('page', null, 1);
        $page_size = $this->request->getPost('rows', null, 10);

        $company_list = Insurance::getInsuranceCompanyList($page_num, $page_size);
        $company_total = Insurance::getInsuranceCompanyCount();

        $this->view->setVar('data', array(
            'total' => $company_total,
            'count' => count($company_list),
            'rows' => $company_list
        ));
    }

    /**
     * 添加保险公司
     */
    public function addInsuranceCompanyAction()
    {
        $creates = $this->request->getPost('creates');
        $data = $creates[0];

        $success = Insurance::addInsuranceCompany($data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新保险公司
     */
    public function updateInsuranceCompanyAction()
    {
        $updates = $this->request->getPut('updates');

        $data = $updates[0];

        $id = $data['id'];

        $success = Insurance::updateInsuranceCompany($id, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除保险公司
     * @param $id
     */
    public function delInsuranceCompanyAction($id)
    {
        $success = Insurance::delInsuranceCompany($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 保险预约页面
     */
    public function insuranceReservationAction()
    {

    }

}

