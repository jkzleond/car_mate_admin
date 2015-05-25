<?php

class ItemController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 商品管理页面
     */
    public function itemManageAction()
    {

    }

    /**
     * 获取商品列表
     */
    public function getItemListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $criteria = $this->request->getPost('criteria');

        $item_list = Item::getItemDetailList($criteria, $page_num, $page_size);
        $item_total = Item::getItemCount($criteria);

        $this->view->setVar('data', array(
            'total' => $item_total,
            'count' => count($item_list),
            'rows' => $item_list
        ));
    }

    /**
     * 获取商品分类列表
     */
    public function getTypeListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $type_list = Item::getTypeList($page_num, $page_size);
        $type_total = Item::getTypeCount();

        $this->view->setVar('data', array(
            'total' => $type_total,
            'count' => count($type_list),
            'rows' => $type_list
        ));
    }

    /**
     * 添加商品
     */
    public function addItemAction()
    {
        $creates = $this->request->getPost('creates');
        $item = new Criteria($creates[0]);
        $success = Item::addItem($item->name, $item->real_price, $item->gold_price, $item->num, $item->pic_data, $item->contents, $item->type_id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新商品
     */
    public function updateItemAction()
    {
        $updates = $this->request->getPut('updates');
        $item = $updates[0];
        $success = Item::updateItem($item['id'], $item);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 商品上架
     */
    public function onShelfItemAction($ids)
    {
        $ids_arr = explode('-', $ids);

        $success = Item::onShelfItemBatchById($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success,
            'msg' => '商品上架成功'
        ));
    }

    /**
     * 商品下架
     */
    public function offShelfItemAction($ids)
    {
        $ids_arr = explode('-', $ids);

        $success = Item::offShelfItemBatchById($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success,
            'msg' => '商品下架成功'
        ));
    }

    /**
     * 商品删除
     * @param $ids
     */
    public function delItemAction($ids)
    {
        $ids_arr = explode('-', $ids);

        $success = Item::delItem($ids_arr);

        $this->view->setVar('data', array(
            'success' => $success,
            'msg' => '商品删除成功'
        ));
    }

    /**
     * 商品类目添加
     */
    public function addItemTypeAction()
    {
        $creates = $this->request->getPost('creates');
        $item_type = $creates[0];
        $name = isset($item_type['name']) ? $item_type['name'] : null;
        $visual = isset($item_type['visual']) ? $item_type['visual'] : null;

        $success = Item::addItemType($name, $visual);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 商品类目删除
     * @param $id
     */
    public function delItemTypeAction($id)
    {
        $success = Item::delItemType($id);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 编辑商品类目
     */
    public function updateItemTypeAction()
    {
        $updates = $this->request->getPut('updates');

        $item_type = $updates[0];
        $id = isset($item_type['id']) ? $item_type['id'] : null;
        $name = isset($item_type['name']) ? $item_type['name'] : null;
        $visual = isset($item_type['visual']) ? $item_type['visual'] : null;

        $success = Item::updateItemType($id, $name, $visual);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 获取商品对换信息列表
     */
    public function getExchangeListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $criteria = $this->request->getPost('criteria');

        $exchange_list = Item::getExchangeList($criteria, $page_num, $page_size);
        $exchange_total = Item::getExchangeCount($criteria);

        $this->view->setVar('data', array(
            'total' => $exchange_total,
            'count' => count($exchange_list),
            'rows' => $exchange_list
        ));
    }
}

