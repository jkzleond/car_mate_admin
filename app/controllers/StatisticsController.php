<?php

class StatisticsController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function queryStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    public function getQueryStatisticsAction($start_date, $end_date, $group_type)
    {
        $user = $this->session->get('user');
        $province_id = $user['province_id'];
        $query_statistics = Statistics::getQueryCount($start_date, $end_date, $group_type, $province_id);
        $this->view->setVar('data', $query_statistics);
    }

    public function getQueryTotalStatisticsAction($start_date, $end_date)
    {
        $user = $this->session->get('user');
        $province_id = $user['province_id'];
        $start_date = $start_date == 'origin' ? null : $start_date;
        $end_date = $end_date == 'now' ? null : $end_date;
        $query_total_statistics = Statistics::getQueryTotalCount($start_date, $end_date, $province_id);
        $this->view->setVar('data', $query_total_statistics);
    }

    public function userStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    public function getUserStatisticsAction($start_date, $end_date, $group_type)
    {
        $user = $this->session->get('user');
        $province_id = $user['province_id'];
        $user_statistics = Statistics::getUserCount($start_date, $end_date, $group_type, $province_id);
        $this->view->setVar('data', $user_statistics);
    }

    public function getUserTotalStatisticsAction($start_date, $end_date)
    {
        $user = $this->session->get('user');
        $province_id = $user['province_id'];
        $start_date = $start_date == 'origin' ? null : $start_date;
        $end_date = $end_date == 'now' ? null : $end_date;
        $user_total_statistics = Statistics::getUserTotalCount($start_date, $end_date, $province_id);
        $this->view->setVar('data', $user_total_statistics);
    }

    public function actStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    public function getActStatisticsAction($start_date, $end_date, $group_type, $statistics_type)
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $user_act_count = null;
        switch($statistics_type)
        {
            case 'Member':
                $user_act_count = Statistics::getMemberCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'FXCQuery':
                $user_act_count = Statistics::getFXCQueryCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'LocalFavour':
                $user_act_count = Statistics::getLocalFavourCount($start_date, $end_date, $group_type,$province_id);
                break;
            case 'Interaction':
                $user_act_count = Statistics::getInteractionCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'Talk':
                $user_act_count = Statistics::getTalkCount($start_date, $end_date,$group_type, $province_id);
                break;
            case 'Location':
                $user_act_count = Statistics::getLocationCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'NewIndex':
                $user_act_count = Statistics::getNewIndexCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'Insurance':
                $user_act_count = Statistics::getInsuranceCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'Friend':
                $user_act_count = Statistics::getFriendCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'HotList':
                $user_act_count = Statistics::getHotListCount($start_date, $end_date, $group_type, $province_id);
                break;
            case 'Activity':
                $user_act_count = Statistics::getActivityCount($start_date, $end_date, $group_type, $province_id);
                break;
            default:
                $user_act_count = Statistics::getActCount($start_date, $end_date, $group_type, $province_id);
                break;
        }
        $this->view->setVar('data', $user_act_count);
    }

    public function getActTotalStatisticsAction($start_date, $end_date, $statistics_type)
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $act_total_count = null;
        switch($statistics_type)
        {
            case 'Member':
                $act_total_count = Statistics::getMemberTotalCount($start_date, $end_date, $province_id);
                break;
            case 'FXCQuery':
                $act_total_count = Statistics::getFXCQueryTotalCount($start_date, $end_date, $province_id);
                break;
            case 'LocalFavour':
                $act_total_count = Statistics::getLocalFavourTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Interaction':
                $act_total_count = Statistics::getInteractionTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Talk':
                $act_total_count = Statistics::getTalkTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Location':
                $act_total_count = Statistics::getLocationTotalCount($start_date, $end_date, $province_id);
                break;
            case 'NewIndex':
                $act_total_count = Statistics::getNewIndexTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Insurance':
                $act_total_count = Statistics::getInsuranceTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Friend':
                $act_total_count = Statistics::getFriendTotalCount($start_date, $end_date, $province_id);
                break;
            case 'HotList':
                $act_total_count = Statistics::getHotListTotalCount($start_date, $end_date, $province_id);
                break;
            case 'Activity':
                $act_total_count = Statistics::getActivityTotalCount($start_date, $end_date, $province_id);
                break;
            default:
                $act_total_count = Statistics::getActTotalCount($start_date, $end_date, $province_id);
                break;
        }
        $this->view->setVar('data', $act_total_count);
    }

    /**
     * 总访问/注册统计页面
     */
    public function quTotalStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    /**
     * 用户活跃页面
     */
    public function userActivityStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    /**
     * 用户活跃度统计
     * @param $start_date
     * @param $end_date
     * @param $group_type
     */
    public function getUserActivityStatisticsAction($start_date, $end_date, $group_type)
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $user_activity_statistics = Statistics::getUserActivityCount($start_date, $end_date, $group_type, $province_id);
        $this->view->setVar('data', $user_activity_statistics);
    }

    /**
     * 各省份用户量排名
     */
    public function getProvinceUserStatisticsAction()
    {
        $province_user_statistics = Statistics::getProvinceUserTotalCount();
        $this->view->setVar('data', $province_user_statistics);
    }

    /**
     * 客户端版本统计
     */
    public function getClientVersionStatisticsAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $client_version_statistics = Statistics::getUserClientVersion($province_id);
        $this->view->setVar('data', $client_version_statistics);
    }

    /**
     * 保险行为(每日新增用户)统计页面
     */
    public function firstPreliminaryCalculationStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    /**
     * *保险行为(每日新增用户数)
     * 第一次使用出算功能的用户
     * @param $start_date
     * @param $end_date
     * @param $group_type
     */
    public function getFpcStatisticsAction($start_date, $end_date, $group_type)
    {
        $first_preliminary_count = Statistics::getFirstPreliminaryCount($start_date, $end_date, $group_type);
        $this->view->setVar('data', $first_preliminary_count);
    }

    /**
     * 保险行为(每日新增用户总数)
     * @param $start_date
     * @param $end_date
     */
    public function getFpcTotalStatisticsAction($start_date, $end_date)
    {
        $fpc_statistics = Statistics::getFirstPreliminaryTotalCount($start_date, $end_date);
        $this->view->setVar('data', $fpc_statistics);
    }

    /**
     * 保险行为统计页面
     */
    public function insuranceActStatisticsAction()
    {
        $this->view->setVars(array(
            'default_end_time' => date('Y-m-d'),
            'default_start_time' => date('Y-m-d', strtotime('-11 day'))
        ));
    }

    /**
     * 保险行为
     * @param $start_date
     * @param $end_date
     * @param $group_type
     */
    public function getInsuranceActStatisticsAction($start_date, $end_date, $group_type)
    {
        $insurance_act_statistics = Statistics::getInsuranceActCount($start_date, $end_date, $group_type);
        $this->view->setVar('data', $insurance_act_statistics);
    }

    /**
     * 总保险行为
     * @param $start_date
     * @param $end_date
     */
    public function getInsuranceActTotalStatisticsAction($start_date, $end_date)
    {
        $insurance_act_total_statistics = Statistics::getInsuranceActTotalCount($start_date, $end_date);
        $this->view->setVar('data', $insurance_act_total_statistics);
    }

}

