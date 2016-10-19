<?php

class NoticeController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 公告管理页面
     */
    public function noticeManageAction()
    {
        $notice_type_list = Notice::getNoticeTypeList();
        $province_list = Province::getProvinceList();

        $this->view->setVars(array(
            'notice_types' => $notice_type_list,
            'province_list' => $province_list
        ));
    }

    /**
     * 获取公告列表
     */
    public function getNoticeListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();

        $page_size = $this->request->getPost('rows');
        $page_num = $this->request->getPost('page');

        $notice_list = Notice::getNoticeList($province_id, $page_num, $page_size);

        $notice_total = Notice::getNoticeCount($province_id);

        $this->view->setVar('data', array(
            'total' => $notice_total,
            'count' => count($notice_list),
            'rows' => $notice_list
        ));
    }

    /**
     * 公告添加
     */
    public function addNoticeAction()
    {
        $creates = $this->request->getPost('creates');
        $notice = new Criteria($creates[0]);

        $success = Notice::addNotice($notice->title, $notice->contents, $notice->type_id, $notice->province_id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除公告
     * @param $id
     */
    public function delNoticeAction($id)
    {
        $success = Notice::delNotice($id);

        $this->view->setVar('data', array(
           'success' => $success
        ));
    }

    /**
     * 更新公告
     */
    public function updateNoticeAction()
    {
        $updates = $this->request->getPut('updates');

        $notice = $updates[0];
        $id = $notice['id'];
        $success = Notice::updateNotice($id, $notice);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 使用公告
     * @param $id
     */
    public function noticeEnableAction($id)
    {
        $success = Notice::updateNotice($id, array('is_state' => 1));

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 弃用公告
     * @param $id
     */
    public function noticeDisableAction($id)
    {
        $success = Notice::updateNotice($id, array('is_state' => 0));

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 公告首页推广
     * @param $id
     */
    public function noticeExtendAction($id)
    {

    }

    /**
     * 公告取消推广
     * @param $id
     */
    public function noticeUnextendAction($id)
    {

    }






}

