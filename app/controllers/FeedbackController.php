<?php

class FeedbackController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 异常信息页面
     */
    public function appExceptionAction()
    {

    }

    /**
     * 获取异常信息报告
     */
    public function getAppExceptionListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria', array());
        $criteria['province_id'] = $province_id;
        $exception_list = AppException::getExceptionList($criteria, $page_num, $page_size);
        $total = AppException::getExceptionCount($criteria);

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($exception_list),
            'rows' => $exception_list
        ));
    }

    /**
     * 删除异常信息
     * @param $id
     */
    public function delAppExceptionAction($id)
    {
        $success = AppException::delException($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 意见反馈管理页面
     */
    public function feedBackManageAction()
    {

    }

    /**
     * 获取意见反馈列表数据
     */
    public function getFeedBackListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria', array());
        $criteria['province_id'] = $province_id;
        $feedback_list = FeedBack::getFeedBackList($criteria, $page_num, $page_size);
        $total = FeedBack::getFeedBackCount($criteria);

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($feedback_list),
            'rows' => $feedback_list
        ));
    }

    /**
     * 删除制定ID的意见反馈
     * @param $id
     */
    public function delFeedBackAction($id)
    {
        $success = FeedBack::deleteFeedBack($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 回复指定ID意见反馈
     * @param $id
     */
    public function replyFeedBackAction($id)
    {
        $user = AdminUser::getCurrentUser();
        $content = $this->request->getPost('content');
        $success = FeedBack::reply($id, $content, $user->user_id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }



}

