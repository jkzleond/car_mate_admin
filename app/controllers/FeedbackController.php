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



}

