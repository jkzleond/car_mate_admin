<?php

use Palm\Utils\HttpConnect;

class IndexController extends ControllerBase
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

    public function statusAction()
    {
        $http = new HttpConnect();
        $main_status = $http->get("http://116.55.248.76/cyh/index.php?Mode=Welcome&ClientType=%s&Action=getIndex&oldPic=4E0D5EB16F4B7F8E428839E0548C4831&userId=jkzleond@163.com&ClientType=Android")->getResponseStatus();
        $code_status = $http->get("http://116.55.248.76/vehIllegalQuery/index.php?mod=FXCQuery&act=GetVerificationCodeImage&userId=jkzleond@163.com&ClientType=Android")->getResponseStatus();

        $violations = Statistics::getViolationCount();

        $interaction_list = Interaction::getInteractionListById();

        $one_hour_before = strtotime('-1 hour');

        $interactions = array();

        if(!empty($interaction_list))
        {
            foreach($interaction_list as $interaction)
            {
                $publish_time = strtotime($interaction->publish_time);
                if($publish_time < $one_hour_before)
                {
                    $interactions[] = $interaction;
                }
            }
        }

        $this->view->setVars(array(
            'main_status' => $main_status,
            'code_status' => $code_status,
            'violations' => $violations,
            'interactions' => $interactions
        ));
    }

    public function ranksAction()
    {
        $latest_up_time = Itunes::getLateUpTime();
        $this->view->setVar('latest_up_time', date('Y-m-d H:i:s', strtotime($latest_up_time)));
    }

    public function getRanksAction()
    {
        //获取easyui-datagrid传递的分页参数
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $ranks = Itunes::getItunes($page_num, $page_size);
        $this->view->setVar('data', $ranks);
    }

}

