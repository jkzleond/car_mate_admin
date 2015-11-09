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

        $success = false;
        $msg_type = isset($criteria['msg_type']) ? $criteria['msg_type'] : 3; //默认系统消息

        if($msg_type == 1 or $msg_type == 2)
        {
            //热门活动 或者 订单信息
            
            $criteria['user_id'] = $msg_type == 1 ? 'activity' : ($msg_type == 2 ? 'order' : null);
            
            $add_push_msg_success = PushNotifications::addPushMessageBatchWithCondition($criteria, $user_criteria);
            $add_private_msg_success = PrivateMessage::addMessageBatchWithCondition($criteria, $user_criteria);
            $success = $add_push_msg_success && $add_private_msg_success;
        }
        else
        {
        	$add_push_msg_success = PushNotifications::addPushMessageBatchWithCondition($criteria, $user_criteria);
        	$add_sys_msg_success = PushNotifications::addSysMessageBatchWithCondition($criteria, $user_criteria);
            $success = $add_push_msg_success && $add_sys_msg_success;
        }

    	$this->view->setVar('data', array(
    		'success' => $success
    	));
    }

}

