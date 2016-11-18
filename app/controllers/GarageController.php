<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-10-10
 * Time: 下午2:30
 */
class GarageController extends ControllerBase
{
    /**
     * 修理厂管理页面
     */
    public function manageAction()
    {
        $services = Garage::getServiceList();
        $this->view->setVar('services', $services);
    }

    /**
     * 获取修理厂列表数据
     */
    public function getGarageListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $garage_list = Garage::getGarageList($criteria, $page_num, $page_size);
        $garage_total = Garage::getGarageCount($criteria);

        $this->view->setVar('data', array(
            'rows' => $garage_list,
            'count' => count($garage_list),
            'total' => $garage_total
        ));
    }

    /**
     * 获取指定ID修理厂信息
     * @param $id
     */
    public function getGarageAction($id)
    {
        $garage = Garage::getGarageById($id);
        $this->view->setVar('data', array(
            'success' => true,
            'data' => $garage
        ));
    }

    /**
     * 添加修理厂
     */
    public function addGarageAction()
    {
        $data = $this->request->getPost('data');
        $location = $data['lat'].','.$data['lng'];
        $map_data_json = file_get_contents('http://apis.map.qq.com/ws/geocoder/v1/?location='.$location.'&key=LFBBZ-RFJHP-67ID5-LMXLQ-T6HMF-TWB5P');
        $map_data = json_decode($map_data_json, true);

        if (!empty($map_data['result']['ad_info']['adcode']))
        {
            $data['ad_code'] = $map_data['result']['ad_info']['adcode'];
        }

        $success = Garage::addGarage($data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除修理厂
     * @param $garage_id
     */
    public function delGarageAction($garage_id)
    {
        $success = Garage::deleteGarage($garage_id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新修理厂
     * @param $garage_id
     */
    public function updateGarageAction($garage_id)
    {
        $data = $this->request->getPut('data');
        $location = $data['lat'].','.$data['lng'];
        $map_data_json = file_get_contents('http://apis.map.qq.com/ws/geocoder/v1/?location='.$location.'&key=LFBBZ-RFJHP-67ID5-LMXLQ-T6HMF-TWB5P');
        $map_data = json_decode($map_data_json, true);

        if (!empty($map_data['result']['ad_info']['adcode']))
        {
            $data['ad_code'] = $map_data['result']['ad_info']['adcode'];
        }

        $success = Garage::updateGarage($garage_id, $data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 商家管理页面
     */
    public function merchantManageAction()
    {

    }

    /**
     * 获取修理厂商家数据
     */
    public function getMerchantListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $merchant_list = Garage::getMerchantList($criteria, $page_num, $page_size);
        $merchant_total = Garage::getMerchantCount($criteria);
        $this->view->setVar('data', array(
            'rows' => $merchant_list,
            'count' => count($merchant_list),
            'total' => $merchant_total
        ));
    }

    /**
     * 添加商家
     */
    public function addMerchantAction()
    {
        $data = $this->request->getPost('data');
        $success = Garage::addMerchant($data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除商家
     * @param $mc_id
     */
    public function delMerchantAction($mc_id)
    {
        $success = Garage::deleteMerchant($mc_id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 修改商家
     * @param $mc_id
     */
    public function updateMerchantAction($mc_id)
    {
        $data = $this->request->getPut('data');
        $success = Garage::updateMerchant($mc_id, $data);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }


}