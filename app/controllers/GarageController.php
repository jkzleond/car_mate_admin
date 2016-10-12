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
        print_r($services);
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

}