<?php

class VoteController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 投票管理页面
     */
    public function voteListAction()
    {

    }

    /**
     * 获取投票列表
     */
    public function getVoteListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $vote_list = Vote::getVoteList($page_num, $page_size);
        $total = Vote::getVoteCount();

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($vote_list),
            'rows' => $vote_list
        ));
    }

    /**
     * 添加投票
     */
    public function addVoteAction()
    {
        $creates = $this->request->getPost('creates');
        $create = $creates[0];

        $new_vote_id = Vote::addVote($create);

        if($new_vote_id)
        {
            if(isset($create['auto_url']) and $create['auto_url'] == 1)
            {
                $url = 'http://116.55.248.76:8080/car/open/voteInfo.do?userid={loginname}&vid='.$new_vote_id;
                Vote::updateVote($new_vote_id, array('url' => $url));
            }

            if( isset($create['options']) and !empty($create['options']) )
            {
                $options = $create['options'];
                foreach($options as &$option)
                {
                    $option['vid'] = $new_vote_id;
                }
                Vote::addVoteOptions($options);
            }
        }

        $this->view->setVar('data', array(
            'success' => $new_vote_id
        ));
    }

    /**
     * 删除投票
     * @param $id
     */
    public function delVoteAction($id)
    {
        $success = Vote::delVote($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新投票
     */
    public function updateVoteAction()
    {
        $updates = $this->request->getPut('updates');
        $update = $updates[0];
        $id = $update['id'];
        if(isset($update['auto_url']) and $update['auto_url'])
        {
            $update['url'] = 'http://116.55.248.76:8080/car/open/voteInfo.do?userid={loginname}&vid='.$id;
        }
        $success = Vote::updateVote($id, $update);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

}

