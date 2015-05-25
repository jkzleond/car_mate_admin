<?php

class NoticeController extends ControllerBase
{

    public function indexAction()
    {

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

}

