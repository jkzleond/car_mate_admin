<?php

class TalkinfoController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 车友互动管理页面
     */
    public function manageAction()
    {

    }

    /**
     * 获取车友互动列表
     */
    public function talkListAction()
    {
    	$page_num = $this->request->getPost('page');
    	$page_size = $this->request->getPost('rows');
    	$criteria = $this->request->getPost('criteria');
    	$order_by = $this->request->getPost('order', null, 'publishTime').' desc';
    	$talk_list = Talk::getTalkList($criteria, $page_num, $page_size, $order_by);
    	$talk_total = Talk::getTalkCount($criteria);

    	$this->view->setVar('data', array(
    		'rows' => $talk_list,
    		'count' => count($talk_list),
    		'total' => $talk_total
    	));
    }

    /**
     * 获取车友互动图片
     * @param  int|string $id
     */
    public function getTalkPicAction($id)
    {
    	$this->view->disable();
    	$this->response->setHeader('content-type', 'image/png');
    	$pic_data = Talk::getTalkPicById($id);
    	echo base64_decode($pic_data);
    }

    /**
     * 指定ID车友互动的回复列表
     * @param int|string $id
     */
    public function talkReplyListAction($id)
    {
    	$page_num = $this->request->get('page');
    	$page_size = $this->request->get('rows');
    	$criteria = $this->request->get('criteria');
    	$reply_list = Talk::getTalkReplyByPid($id, $page_num, $page_size);
    	$reply_total = Talk::getTalkReplyCountByPid($id);

    	$this->view->setVar('data', array(
    		'rows' => $reply_list,
    		'count' => count($reply_list),
    		'total' => $reply_total
    	));
    }

    /**
     * 回复指定ID车友互动
     * @param  [type] $id
     * @return [type]
     */
    public function talkReplyAction($id)
    {
    	$user = AdminUser::getCurrentUser();
    	$comment = $this->request->getPost('comment');
    	$success = Talk::addReply($id, array(
    		'user_id' => $user->user_id,
    		'title' => $user->nick_name,
    		'comment' => $comment
    	));

    	$this->view->setVar('data', array(
    		'success' => $success
    	));
    }

    /**
     * 改变车友互动发布状态
     * @param  int|string $id
     * @param  int|string $state
     */
    public function talkStateChangeAction($id, $state)
    {
    	$success = Talk::updateTalkById($id, array('state' => $state));

    	$this->view->setVar('data',array(
    		'success' => $success
    	));
    }

    /**
     * 改变车友互动的可回复状态
     * @param  int|string $id
     * @param  int|string $no_reply
     */
    public function talkNoReplyChangeAction($id, $no_reply)
    {
    	$success = Talk::updateTalkById($id, array('no_reply' => $no_reply));
    	$this->view->setVar('data', array(
    		'success' => $success
    	));
    }

    /**
     * 删除指定ID车友互动
     * @param  int|string $id
     */
    public function deleteTalkAction($id)
    {
    	$success = Talk::deleteTalkById($id);
    	$this->view->setVar('data', array(
    		'success' => $success
    	));
    }

    /**
     * 删除指定ID回复
     * @param  int|string $reply_id
     */
    public function deleteTalkReplyAction($reply_id)
    {
    	$success = Talk::deleteTalkReplyById($reply_id);
    	$this->view->setVar('data', array(
    		'success' => $success
    	));
    }
}

