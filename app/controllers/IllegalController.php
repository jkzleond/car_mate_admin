<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-7-29
 * Time: 下午4:07
 * 违章代缴
 */

class IllegalController extends ControllerBase
{
    /**
     * 订单管理页面
     */
    public function orderManageAction()
    {

    }

    /**
     * 获取订单列表
     */
    public function getOrderListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $order_list = Order::getIllegalOrderList($criteria, $page_num, $page_size);
        $order_total = Order::getIllegalOrderCount($criteria);

        $this->view->setVar('data', array(
            'rows' => $order_list,
            'count' => count($order_list),
            'total' => $order_total
        ));
    }

    /**
     * 订单详情页面
     * @param $order_id
     */
    public function orderDetailAction($order_id)
    {
        $order_info = Order::getIllegalOrderInfoById($order_id);
        $order_items = Order::getIllegalOrderItems($order_id);
        $order_info->items = $order_items;

        $this->view->setVar('order', $order_info);
    }

    /**
     * 订单处理(处理违章)
     * @param $order_id
     */
    public function orderProcessAction($order_id)
    {
        $criteria = $this->request->getPut('criteria');
        $success = Order::updateOrder($order_id, $criteria);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 驾驶员信息管理页面
     */
    public function driverInfoManageAction()
    {

    }

    /**
     * 获取驾驶员信息列表
     */
    public function getDriverInfoListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria');
        $driver_info_list = DriverInfo::getDriverInfoList($criteria, $page_num, $page_size);
        $driver_info_total = DriverInfo::getDriverInfoCount($criteria);

        $this->view->setVar('data', array(
            'rows' => $driver_info_list,
            'count' => count($driver_info_list),
            'total' => $driver_info_total
        ));
    }

    /**
     * 更新驾驶员信息
     * @param $info_id
     */
    public function updateDriverInfoAction($info_id)
    {
        $data = $this->request->getPut('data');
        $criteria = $this->request->getPut('criteria');
        //var_dump($data);
        //var_dump($criteria);
        $success = DriverInfo::updateDriverInfo($info_id, $data);
        CarInfo::updateCarInfoWithCondition($criteria, $data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }
}