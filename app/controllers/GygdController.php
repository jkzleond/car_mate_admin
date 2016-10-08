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
     * 导出体育馆用户参与数据(csv)
     */
    public function exportStadiumActivityUserDataAction()
    {
        $this->view->disable();
        $criteria_json = $this->request->get('criteria');
        $criteria = json_decode($criteria_json, true);
        $activity_users = Gygd::getStadiumActivityUserList($criteria);
        $this->response->setHeader('Content-type', 'application/octet-stream');
        $this->response->setHeader('Accept-Ranges', 'bytes');
        $this->response->setHeader('Content-Disposition', '体育馆活动参与用户数据.csv');
        //输出头部
        echo "手机号, 身份证, 抽奖时间, 状态\r\n";
        foreach ($activity_users as $activity_user)
        {
            $state = '未中奖';
            if ($activity_user['is_win'] == 1 and empty($activity_user['exchange_date']))
            {
                $state = '中奖';
            }
            elseif (!empty($activity_user['exchange_date']))
            {
                $state = '已领取';
            }

            echo $activity_user['phone'].', '.$activity_user['id_no'].', '.$activity_user['draw_date'].', '.$state."\r\n";
        }
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
        $users = Gygd::getMuseumActivityUserList($criteria, $page_num, $page_size);
        $total = Gygd::getMuseumActivityUserCount($criteria);

        $this->view->setVar('data', array(
            'success'=> true,
            'total' => $total,
            'count' => count($users),
            'rows' => $users,
        ));
    }

    /**
     * 删除博物馆活动参与用户
     * @param $activity_id
     * @param $user_id
     */
    public function delMuseumActivityUserAction($activity_id, $user_id)
    {
        $success = Gygd::deleteStadiumActivityUser($activity_id, $user_id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 博物馆活动领取
     */
    public function gainMuseumActivityAction()
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
     * 获取博物馆活动奖品列表
     * @param  $draw_type
     */
    public function getMuseumActivityAwardListAction($draw_type='HALF_YEAR')
    {
        $activity_id = 2;
        if ($draw_type == 'FULL_YEAR')
        {
            $activity_id = 3;
        }
        $award_list = Gygd::getMuseumActivityAwardList($activity_id);
        $this->view->setVar('data', array(
            'success' => true,
            'rows' => $award_list,
            'total' => count($award_list),
            'count' => count($award_list)
        ));
    }

    /**
     * 博物馆活动随机摇号
     * @param  string $draw_type
     * @param  int $people_num
     */
    public function getMuseumActivityRandomUserAction($draw_type='HALF_YEAR', $people_num=2)
    {
        $activity_user_list = Gygd::getMuseumActivityUserList(array(
            'state' => 'NO_WIN'
        ));

        //随机种子
        $success = shuffle($activity_user_list);

        $random_user_list = array_slice($activity_user_list, 0, $people_num);

        $this->view->setVar('data', array(
            'success' => $success,
            'list' => $random_user_list
        ));
    }

    /**
     * 博物馆活动用户中奖
     * @param  string $draw_type
     */
    public function museumActivityWinUserAction($draw_type='HALF_YEAR')
    {
        $award_id = $this->request->getPost('award_id');
        $users = $this->request->getPost('users');
        $success = true;
        $this->db->begin();
        try
        {
            if ($draw_type == 'FULL_YEAR')
            {
                $activity_id = 3;

                $del_activity_user_success = Gygd::deleteActivityUser($activity_id, $users);

                if (!$del_activity_user_success)
                {
                    throw new Exception('delete activity user failed');
                }

                $add_activity_user_success = Gygd::addActivityUser($activity_id, $users, true);
                if (!$add_activity_user_success)
                {
                    throw new Exception('add activity user failed');
                }
            }
            else
            {
                $activity_id = 2;
                $update_activity_user_success = Gygd::updateActivityUsers($users, array('is_win' => true));
                if (!$update_activity_user_success)
                {
                    throw new Exception('update activity user failed');
                }
            }
            $del_award_user_success = Gygd::deleteAwardToUser($award_id, $users);
            if (!$del_award_user_success)
            {
                throw new Exception('delete activity user failed');
            }
            $add_award_user_success = Gygd::addAwardToUser($award_id, $users);
            if (!$add_award_user_success)
            {
                throw new Exception('add award user failed');
            }
            $success = $this->db->commit();
        }
        catch(Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
            $this->db->rollBack();
        }

        $this->view->setVar('data', array(
            'success' => $success,
            'msg' => !empty($msg) ? $msg : null
        ));
    }
}