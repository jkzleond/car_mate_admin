<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-6-27
 * Time: 下午1:46
 */
class MoveCarController extends ControllerBase
{
    /**
     * 订单管理页面
     */
    public function orderManageAction()
    {
        $client_types = Order::getOrderClientTypeList();
        $this->view->setVar('client_types', $client_types);
    }

    /**
     * 获取挪车订单列表数据
     */
    public function getOrderListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $order_list = Order::getMoveCarOrderList($criteria, $page_num, $page_size);
        $order_total = Order::getMoveCarOrderTotal($criteria);

        $this->view->setVar('data', array(
            'rows' => $order_list,
            'count' => count($order_list),
            'total' => $order_total
        ));
    }

    /**
     * 挪车订单详情页
     * @param $order_id
     */
    public function orderDetailAction($order_id)
    {
        $order = Order::getMoveCarOrderById($order_id);
        $car_owners = MoveCar::getCarOwnerList($order['record']['hphm']);
        $call_records = MoveCar::getCallRecord($order_id);
        $feed_back = MoveCar::getFeedback($order_id);
        $appeal = MoveCar::getAppeal($order_id);

        $this->view->setVars(array(
            'order' => new Criteria($order),
            'car_owners' => $car_owners,
            'call_records' => $call_records,
            'feed_back' => $feed_back,
            'appeal' => $appeal
        ));
    }
}