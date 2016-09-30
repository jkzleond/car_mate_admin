<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-9-29
 * Time: 上午10:09
 */
class GygdController extends ControllerBase
{
    public function indexAction()
    {
        $user = AdminUser::getCurrentUser();
        $province_list = Province::getProvinceList();
        $this->view->setVar('user', $user);
        $this->view->setVar('auth', $user->auth);
        $this->view->setVar('city_auth', $user->city_auth);
        $this->view->setVar('province_list', $province_list);
    }

    /**
     * 体育馆活动页面
     */
    public function stadiumAction()
    {
        $activity = Gygd::getActivityInfoById(1);
        $win_rule = json_decode($activity->win_rule);
        $activity->win_rule = !empty($win_rule) ? $win_rule : array();
        $this->view->setVars(array(
            'activity' => $activity
        ));
    }

    /**
     * 获取体育馆活动用户数据
     */
    public function getStadiumActivityUserListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $users = Gygd::getStadiumActivityUserList($criteria, $page_num, $page_size);
        $total = Gygd::getStadiumActivityUserCount($criteria);

        $this->view->setVar('data', array(
            'rows' => $users,
            'count' => count($users),
            'total' => $total,
        ));
    }

    /**
     * @param $activity_id
     * @param $user_id
     */
    public function delStadiumActivityUserAction($activity_id, $user_id)
    {
        $success = Gygd::deleteStadiumActivityUser($activity_id, $user_id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新体育馆活动
     */
    public function updateStadiumActivityAction()
    {
        $data = $this->request->getPut('data');
        $success = Gygd::updateActivity(1, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 体育馆活动领取
     */
    public function gainStadiumActivityAction()
    {
        $data = $this->request->getPut('data');
        $activity_id = $data['activity_id'];
        $user_id = $data['user_id'];
        $success = Gygd::gainStadiumActivity($activity_id, $user_id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 博物馆活动页面
     */
    public function museumAction()
    {

    }

    /**
     * 获取博物馆活动参与用户数据
     */
    public function getMuseumActivityUserListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $users = Gygd::getStadiumActivityUserList($criteria, $page_num, $page_size);
        $total = Gygd::getStadiumActivityUserCount($criteria);

        return array(
            'success'=> true,
            'total' => $total,
            'count' => count($users),
            'list' => $users,
        );
    }
}