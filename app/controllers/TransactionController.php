<?php

class TransactionController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 流水记录页面
     */
    public function listAction()
    {
        $distribute_type_list = Transaction::getDistributeTypeList();

        $this->view->setVars(array(
            'distribute_types' => $distribute_type_list
        ));
    }

    /**
     * 获取流水记录列表
     */
    public function getTransactionListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $criteria = $this->request->getPost('criteria');

        $transaction_list = Transaction::getTransactionList($criteria, $page_num, $page_size);
        $transaction_total = Transaction::getTransactionCount($criteria);

        $this->view->setVar('data', array(
            'total' => $transaction_total,
            'count' => count($transaction_list),
            'rows' => $transaction_list
        ));
    }

}

