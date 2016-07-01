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
        $car_owners = MoveCar::getCarOwnerList(array(
            'hphm' => $order['record']['hphm']
        ));
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

    /**
     * 挪车订单申诉处理
     * @param $order_id
     */
    public function appealProcessAction($order_id)
    {
        $data = $this->request->getPut('criteria');
        $process_des = isset($data['process_des']) ? $data['process_des'] : null;
        $success = MoveCar::processAppeal($order_id, $process_des);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 车主管理页面
     */
    public function carOwnerManageAction()
    {

    }

    /**
     * 获取车主列表数据
     */
    public function getCarOwnerListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $car_owner_list = MoveCar::getCarOwnerList($criteria, $page_num, $page_size);
        $car_owner_total = MoveCar::getCarOwnerTotal($criteria);
        $this->view->setVar('data', array(
            'rows' => $car_owner_list,
            'count' => count($car_owner_list),
            'total' => $car_owner_total
        ));
    }

    /**
     * 车主话单页面
     * @param $car_owner_source
     * @param $car_owner_id
     */
    public function carOwnerCallRecordAction($car_owner_source, $car_owner_id)
    {
        $call_records = MoveCar::getCarOwnerCallRecord($car_owner_source, $car_owner_id);
        $this->view->setVars(array(
            'call_records' => $call_records
        ));
    }

    /**
     * 更新指定ID车主信息
     * @param $car_owner_source
     * @param $car_owner_id
     */
    public function updateCarOwnerAction($car_owner_source, $car_owner_id)
    {
        $data = $this->request->getPut('criteria');
        $success = MoveCar::updateCarOwner($car_owner_id, $car_owner_source, $data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }
}