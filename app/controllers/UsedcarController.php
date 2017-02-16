<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 17-2-8
 * Time: 下午3:24
 */
class UsedcarController extends ControllerBase
{
    /**
     * 估价请求管理页面
     */
    public function evalPriceRecordManageAction()
    {

    }

    /**
     * 获取估价记录数据列表
     */
    public function getEvalPriceRecordListAction()
    {
        $criteria = $this->request->getPost('criteria');
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $record_list = EvalPrice::getEvalPriceRecordList($criteria, $page_num, $page_size);
        $record_total = EvalPrice::getEvalPriceRecordCount($criteria);

        $this->view->setVar('data', array(
            'rows' => $record_list,
            'count' => count($record_list),
            'total' => $record_total
        ));
    }

    /**
     * 更新估价记录
     * 用于标记处理等
     * @param $record_id
     */
    public function updateEvalPriceRecordAction($record_id)
    {
        $data = $this->request->getPut('data');
        $criteria = array('id' => $record_id);
        $success = EvalPrice::updateEvalPriceRecord($data, $criteria);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }
}