<?php

class AwardController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 抽奖奖品页面
     */
    public function awardListAction()
    {

    }

    public function awardManage()
    {
        
    }

    /**
     * 获取抽奖类型活动列表
     */
    public function getAwardActivityListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $activity = Activity::getAwardActivityList($page_num, $page_size);
        $total = Activity::getAwardActivityCount();

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($activity),
            'rows' => $activity
        ));
    }

    /**
     * 获取奖品列表
     */
    public function getAwardListAction()
    {
        $aid = $this->request->getPost('aid');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $award_list = Award::getAwardList($aid, $page_num, $page_size);
        $total = Award::getAwardCount($aid);

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($award_list),
            'rows' => $award_list
        ));
    }

    /**
     * 添加奖品
     */
    public function addAwardAction()
    {
        $creates = $this->request->getPost('creates');

        $criteria = new Criteria($creates[0]);

        $aid = $criteria->aid;
        $name = $criteria->name;
        $des = $criteria->des;
        $num = $criteria->num;
        $rate = $criteria->rate;
        $day_limit = $criteria->day_limit;
        $pic_data = $criteria->pic_data;

        $success = Award::addAward($aid, $name, $des, $num, $rate, $day_limit, $pic_data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除奖品
     * @param id
     */
    public function delAwardAction($id)
    {
        $success = Award::delAward($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新奖品
     */
    public function updateAwardAction()
    {
        $updates = $this->request->getPut('updates');

        $data = $updates[0];

        $id = $data['id'];
        $criteria = new Criteria($data);

        $success = Award::updateAward($id, $criteria);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 获取指定ID奖品的图片
     * @param  int|string $award_id
     */
    public function getAwardPicAction($award_id)
    {
        $this->view->disable();
        $this->response->setContentType('image/jpeg');
        echo base64_decode(Award::getAwardPic($award_id));
    }

    /**
     * 中奖管理页面
     */
    public function awardGainManageAction()
    {

    }

    /**
     * 获取中奖列表(用户中奖总数)
     */
    public function getGainListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');


        $gain_list = Award::getGainList($criteria, $page_num, $page_size);
        $gain_total = Award::getGainCount($criteria);

        $this->view->setVar('data', array(
            'total' => $gain_total,
            'count' => count($gain_list),
            'rows' => $gain_list
        ));
    }

    /**
     * 添加用户中奖记录
     */
    public function addUserGainAction()
    {
        $award_id = $this->request->getPost('award_id');
        $user_id = $this->request->getPost('user_id');
        $aid = $this->request->getPost('aid');
        $win_date = $this->request->getPost('win_date');

        $award = Award::getAwardById($award_id);

        $success = true;
        $msg = '';
        //判断奖品是否被领取完
        if($award->state != 0)
        {
            $success = false;
            $msg = '奖品已被领完';
        }
        else
        {
            $criteria = array();
            if($award->num - $award->winnum <= 1)
            {
                $criteria['state'] = 1;
            }

            $criteria['win_num'] = $award->winnum + 1;
            $criteria['last_win_date'] = date('Y-m-d H:i:s');

            Award::updateAward($award_id, new Criteria($criteria));

            $success = Award::addAwardGain($user_id, $aid, $award_id, $win_date);

        }

        $this->view->setVar('data', array(
            'success' => $success,
            'msg' => $msg
        ));
    }

    /**
     * 获取用户中奖列表
     */
    public function getUserGainListAction()
    {
        $user_id = $this->request->getPost('user_id');
        $criteria = $this->request->getPost('criteria');

        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $user_gain_list = Award::getUserGainList($user_id, $criteria, $page_num, $page_size);
        $total = Award::getUserGainCount($user_id);

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($user_gain_list),
            'rows' => $user_gain_list
        ));
    }

    /**
     * 设置领取
     * @param $id
     */
    public function awardGainAction($id)
    {
        $success = Award::awardGain($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

}

