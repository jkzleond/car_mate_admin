<?php

class UserController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 获取用户列表疏忽
     */
    public function getUserListAction()
    {
    	$page_num = $this->request->getPost('page');
    	$page_size = $this->request->getPost('rows');
    	$criteria = $this->request->getPost('criteria');

    	$user_list = User::getUserList($criteria, $page_num, $page_size);
    	$user_total = User::getUserCount($criteria);

    	$this->view->setVar('data', array(
    		'total' => $user_total,
    		'count' => count($user_list),
    		'rows' => $user_list
    	));
    }

	/**
	 * 用户禁言
	 * @param string $user_id
	 */
	public function setNoTalkAction($user_id)
	{
		$no_talk = $this->request->getPut('no_talk');
		$success = User::setNoTalk($user_id, $no_talk);
		$this->view->setVar('data', array(
			'success' => $success
		));
	}

}

