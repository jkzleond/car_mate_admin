<?php

class PushmessageController extends ControllerBase
{

	/**
	 * 消息推送页面
	 */
    public function indexAction()
    {
    	$client_type_list = User::getUserClientTypeList();

    	$this->view->setVar('client_type_list', $client_type_list);
    }

    /**
     * 消息推送
     */
    public function pushMessageAction()
    {
    	$criteria = $this->request->getPost('criteria');
    	$user_criteria = $this->request->getPost('user_criteria');

    	$add_push_msg_success = PushNotifications::addPushMessageBatchWithCondition($criteria, $user_criteria);
    	$add_sys_msg_success = PushNotifications::addSysMessageBatchWithCondition($criteria, $user_criteria);

    	$this->view->setVar('data', array(
    		'success' => $add_sys_msg_success && $add_push_msg_success
    	));
    }

}

