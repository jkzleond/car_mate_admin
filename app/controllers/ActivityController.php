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
        $sign_start_date = isset($data['sign_start_date']) ? $data['sign_start_date'] : null;
        $sign_end_date = isset($data['sign_end_date']) ? $data['sign_end_date'] : null;
        $trip_line = isset($data['trip_line']) ? $data['trip_line'] : null;
        $auto_start = $data['auto_start'];
        $info_arr = isset($data['info']) ? $data['info'] : null;
        $data['info'] = isset($data['info']) ? implode(', ', $data['info']) : null;
        $option = isset($data['option']) ? $data['option'] : null;
        $type_id = $data['type_id'];
        $need_check_in = $data['need_check_in'];
        $need_notice = $data['need_notice'];
        $award_start = $data['award_start'];
        $award_end = $data['award_end'];
        $need_pay = $data['need_pay'];
        $pay_items = isset($data['pay_items']) ? $data['pay_items'] : null;

        $deposit = $data['deposit'];
        $pay_types = isset($data['pay_types']) ? implode(', ', $data['pay_types']) : null;
        $data['pay_types'] = $pay_types;
        $group_column = $data['group_column'];

        $pic_data = isset($data['pic_data']) ? $data['pic_data'] : null;
        $contents = isset($data['contents']) ? $data['contents'] : null;

        $success = Activity::addActivity($data);

        $aid = $success;

        if($need_pay and !empty($pay_items) and $aid)
        {
            Activity::addPayItems($aid, $pay_items);
        }

        if(!empty($info_arr) && in_array('select', $info_arr) && $success)
        {
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
            Activity::updateActivity($id, array('url' => $url));
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
            $pay_items = isset($data['pay_items']) ? $data['pay_items'] : null;
            $info_arr = isset($data['info']) ? $data['info'] : null;
            $data['info'] = isset($data['info']) ? implode(', ', $data['info']) : null;
            $data['pay_types'] = isset($data['pay_types']) ? implode(', ', $data['pay_types']) : null;
            $data['pay_items'] = isset($data['pay_items']) ? $data['pay_items'] : null;

            $rst = Activity::updateActivity($id, $data);

            //更新付款项目

            $aid = $id;

            if(empty($pay_items))
            {

                Activity::delPayItems($aid);
            }
            else
            {
                $pay_item_ids = array();
                $new_pay_items = array();
                foreach($pay_items as $pay_item)
                {
                    $pay_item_id = isset($pay_item['id']) ? $pay_item['id'] : null;
                    if($pay_item_id)
                    {
                        Goods::updateGoods($pay_item_id, array(
                            'name' => $pay_item['name'],
                            'price' => $pay_item['price']
                        ));
                        $pay_item_ids[] = $pay_item_id;
                    }
                    else
                    {
                       $new_pay_items[] = $pay_item;
                    }
                }

                if(!empty($pay_item_ids))
                {
                    Activity::delPayItemsButIds($aid, $pay_item_ids);
                }

                if(!empty($new_pay_items))
                {
                    Activity::addPayItems($aid, $new_pay_items);
                }
            }


            //更新ActivitySelect
            if(!empty($info_arr) && in_array('select', $info_arr))
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
     * 抽奖时段管理页面
     * $param int|string $aid
     */
    public function drawPeriodAction($aid)
    {
        $activity = Activity::getActivityById($aid);

        $this->view->setVars(array(
            'activity' => $activity
        ));
    }

    /**
     * 获取抽奖时段列表
     * @param  int|string $aid
     */
    public function getDrawPeriodListAction($aid)
    {
        $period_list = Activity::getDrawPeriodList($aid);
        $period_total = count($period_list);

        $this->view->setVar('data', array(
            'total' => $period_total,
            'count' => $period_total,
            'rows' => $period_list
        ));
    }

    /**
     * 添加抽奖时段
     * @param int|string $aid
     */
    public function addDrawPeriodAction($aid)
    {
        $creates = $this->request->getPost('creates');
        $data = $creates[0];
        $success = Activity::addDrawPeriod($aid, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新抽奖时段
     * @param  int|string $id
     */
    public function updateDrawPeriodAction($id)
    {
        $updates = $this->request->getPut('updates');
        $data = $updates[0];
        $success = Activity::updateDrawPeriod($id, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除抽奖时段
     * @param  int|string $id
     */
    public function delDrawPeriodAction($id)
    {
        $success = Activity::delDrawPeriod($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 抽奖时段奖品管理页面
     * @param  int|string $period_id
     */
    public function drawPeriodAwardAction($period_id)
    {
        $period = Activity::getDrawPeriodById($period_id);
        $activity = Activity::getActivityById($period->aid);

        $this->view->setVars(array(
            'period' => $period,
            'activity' => $activity
        ));
    }

    /**
     * 获取指定ID抽奖时段奖品列表
     * @param  int|string $period_id
     */
    public function getDrawPeriodAwardListAction($period_id)
    {
        $award_list = Award::getDrawPeriodAwardList($period_id);
        $award_total = Award::getDrawPeriodAwardCount($period_id);

        $this->view->setVar('data', array(
            'total' => $award_total,
            'count' => count($award_list),
            'rows' => $award_list
        ));
    }

    /**
     * 为指定时段添加奖品
     * @param int|string $period_id
     */
    public function addDrawPeriodAwardAction($period_id)
    {
        $creates = $this->request->getPost('creates');
        $data = $creates[0];
        $success = Award::addDrawPeriodAward($period_id, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除时段的某个奖品
     * @param int|string $id
     */
    public function delDrawPeriodAwardAction($id)
    {
        $success = Award::delDrawPeriodAward($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 抽奖奖品管理页面
     */
    public function awardManageAction($aid)
    {
        $period_list = Activity::getDrawPeriodList($aid);

        $this->view->setVars(array(
            'period_list' => $period_list
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

    /**
     * 活动参与用户订单明细
     * @param $order_id 活动ID
     */
    public function orderDetailAction($order_id)
    {
        $order_info = Order::getActivityOrderInfoById($order_id);
        $order_items = Order::getActivityOrderItems($order_id);
        $order_info->items = $order_items;

        $this->view->setVar('order', $order_info);
    }

}

