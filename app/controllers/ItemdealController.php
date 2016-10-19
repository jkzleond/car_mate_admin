<?php

class ItemdealController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 订单管理页面
     */
    public function manageAction()
    {
        $logistics_company_list = LogisticsCompany::getCompanyList();

        $this->view->setVars(array(
            'company_list' => $logistics_company_list
        ));
    }

    /**
     * 获取订单列表
     */
    public function getDealListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria');

        $deal_list = Deal::getDealList($criteria, $page_num, $page_size);
        $deal_total = Deal::getDealCount($criteria);

        $this->view->setVar('data', array(
            'total' => $deal_total,
            'count' => count($deal_list),
            'rows' => $deal_list
        ));
    }

    /**
     * 订单发货
     */
    public function deliverAction($id)
    {
        $comp_id = $this->request->getPut('comp_id');
        $order_no = $this->request->getPut('order_no');

        $success = Deal::deliver($id, $comp_id, $order_no);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

}

