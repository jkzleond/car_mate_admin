<?php

class ActivityController extends ControllerBase
{
    /**
     * 活动详情
     * @param $id
     */
    public function detailAction($id)
    {
        $activity = Activity::getActivityById($id);

        $activity->infos = isset($activity->infos) ? explode(', ', $activity->infos) : array();
        $activity->optionList = isset($activity->optionList) ? explode(', ', $activity->optionList) : array();
        $activity->shortNames = isset($activity->shortNames) ? explode(', ', $activity->shortNames) : array();
        $activity->depositList = isset($activity->depositList) ? explode(', ', $activity->depositList) : array();

        $this->view->setVars(array(
            'activity' => $activity
        ));
    }

    /**
     * 活动管理页面
     */
    public function activityListAction()
    {
        $type_list = Activity::getActivityTypeList();

        $this->view->setVars(array(
            'type_list' => $type_list
        ));
    }

    /**
     * 获取活动列表
     */
    public function getActivityListAction()
    {
        $page = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $activity_list = Activity::getActivityList($page, $page_size);

        $activity_total = Activity::getActivityCount();

        $this->view->setVar('data', array(
            'total' => $activity_total,
            'count' => count($activity_list),
            'rows' => $activity_list
        ));
    }

    /**
     * 添加活动
     */
    public function addActivityAction()
    {

        $creates = $this->request->getPost('creates');
        $data = $creates[0];

        $name = $data['name'];
        $place = isset($data['place']) ? $data['place'] : null;
        $url = $data['url'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $auto_start = $data['auto_start'];
        $info = isset($data['info']) ? implode(', ', $data['info']) : null;
        $option = isset($data['option']) ? $data['option'] : null;
        $type_id = $data['type_id'];
        $need_check_in = $data['need_check_in'];
        $need_notice = $data['need_notice'];
        $award_start = $data['award_start'];
        $award_end = $data['award_end'];
        $need_pay = $data['need_pay'];
        $deposit = $data['deposit'];
        $pay_types = isset($data['pay_types']) ? implode(', ', $data['pay_types']) : null;
        $group_column = $data['group_column'];

        $success = Activity::addActivity($name, $place, $url, $start_date, $end_date, $auto_start, $info, $option, $type_id, $need_check_in, $need_notice, $award_start, $award_end, $need_pay, $deposit, $pay_types, $group_column);

        if(isset($data['info']) && in_array('select', $data['info']) && $success)
        {
            $aid = $success;
            $sel_name = $data['sel_name'];
            $option_list = isset($data['sel_options']) ? implode(', ', $data['sel_options']) : '';
            $short_names = isset($data['sel_short_names']) ? implode(', ', $data['sel_short_names']) : '';
            $deposit_list = isset($data['sel_deposits']) ? implode(', ', $data['sel_deposits']) : '';
            Activity::addActivitySelect($aid, $sel_name, $option_list, $short_names, $deposit_list);
        }

        if($auto_start && $success)
        {
            $id = $success;
            $url = 'http://116.55.248.76:8080/car/userInfo.do?userid={loginname}&aid='.$id;
            Activity::updateActivity($id, null, null, $url);
        }

        $success = $success !== false ? true : false;

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除活动
     * @param $id
     */
    public function delActivityAction($id)
    {
        $success = Activity::delActivity($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新活动
     */
    public function updateActivityAction()
    {
        $updates = $this->request->getPut('updates');

        $success = false;
        $updated = array();

        foreach($updates as $data)
        {
            $id = isset($data['id']) ? $data['id'] : null;
            $name = isset($data['name']) ? $data['name'] : null;
            $place = isset($data['place']) ? $data['place'] : null;
            $url = isset($data['url']) ? $data['url'] : null;
            $auto_start = isset($data['auto_start']) ? $data['auto_start'] : null;
            $start_date = isset($data['start_date']) ? $data['start_date'] : null;
            $end_date = isset($data['end_date']) ? $data['end_date'] : null;
            $state = isset($data['state']) ? $data['state'] : null;
            $info = isset($data['info']) ? implode(', ', $data['info']) : null;
            $option = isset($data['option']) ? $data['option'] : null;
            $type_id = isset($data['type_id']) ? $data['type_id'] : null;
            $need_check_in = isset($data['need_check_in']) ? $data['need_check_in'] : null;
            $award_start = isset($data['award_start']) ? $data['award_start'] : null;
            $award_end = isset($data['award_end']) ? $data['award_end'] : null;
            $award_state = isset($data['award_state']) ? $data['award_state'] : null;
            $need_pay = isset($data['need_pay']) ? $data['need_pay'] : null;
            $deposit = isset($data['deposit']) ? $data['deposit'] : null;
            $need_notice = isset($data['need_notice']) ? $data['need_notice'] : null;
            $pay_types = isset($data['pay_types']) ? implode(', ',$data['pay_types']) : null;
            $group_column = isset($data['group_column']) ? $data['group_column'] : null;

            $rst = Activity::updateActivity($id, $name, $place, $url, $auto_start, $start_date, $end_date, $state, $info, $option, $type_id, $need_check_in, $award_start, $award_end, $award_state, $need_pay, $deposit, $need_notice, $pay_types, $group_column);

            //更新ActivitySelect
            if(isset($data['info']) && in_array('select', $data['info']))
            {
                $aid = $id;
                $sel_name = $data['sel_name'];
                $option_list = isset($data['sel_options']) ? implode(', ', $data['sel_options']) : null;
                $short_names = isset($data['sel_short_names']) ? implode(', ', $data['sel_short_names']) : null;
                $deposit_list = isset($data['sel_deposits']) ? implode(', ', $data['sel_deposits']) : null;
                $rst2 = Activity::updateActivitySelect($aid, $sel_name, $option_list, $short_names, $deposit_list);
                $rst = $rst  || $rst2;
            }

            $success = $success || $rst;

            if($rst) array_push($updated, $data);
        }

        $this->view->setVar('data', array(
            'success' => $success,
            'update' => $updated
        ));
    }

    /**
     * 获取签到信息列表
     */
    public function getCheckListAction()
    {
        $aid = $this->request->getPost('aid');
        $user_id = $this->request->getPost('user_id');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $check_list = Activity::getCheckList($aid, $user_id, $page_num, $page_size);
        $check_total = Activity::getCheckCount($aid, $user_id);

        $this->view->setVar('data', array(
            'total' => $check_total,
            'count' => count($check_list),
            'rows' => $check_list
        ));
    }

    /**
     * 获取支付信息列表
     */
    public function getPayListAction()
    {
        $aid = $this->request->getPost('aid');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $pay_list = Activity::getPayList($aid, $page_num, $page_size);
        $pay_total = Activity::getPayCount($aid);

        $this->view->setVar('data', array(
            'total' => $pay_total,
            'count' => count($pay_list),
            'rows' => $pay_list
        ));
    }

    /**
     * 参与用户页面
     */
    public function involvedUserAction()
    {

    }

    /**
     * 获取活动参与用户列表
     */
    public function getInvolvedUserListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria');
        $criteria = $criteria ? new Criteria($criteria) : new Criteria();

        $involved_user = ActivityUser::getActivityUserList($criteria, $page_num, $page_size);
        $total = ActivityUser::getActivityUserCount($criteria);

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($involved_user),
            'rows' => $involved_user
        ));
    }

    /**
     * 通知用户
     * @param $ids
     */
    public function noticeUserAction($ids)
    {
        $ids_arr = explode(',', $ids);
        $success = ActivityUser::noticeUser($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 用户领取
     * @param $ids
     */
    public function userGainAction($ids)
    {
        $ids_arr = explode(',', $ids);

        $success = ActivityUser::userGain($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 用户付款
     * @param $ids
     */
    public function userPayAction($ids)
    {
        $ids_arr = explode(',', $ids);
        $success = ActivityUser::userPay($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }


}

