<?php
class FieldController extends ControllerBase
{
	public function indexAction()
	{

	}

	/**
	 * 获取字段数据列表
	 */
	public function getFieldListAction()
	{
		$page_num = $this->request->getPost('page');
		$page_size = $this->request->getPost('rows');
		$criteria = $this->request->getPost('criteria');

		$field_list = Field::getFieldList($criteria, $page_num, $page_size);
		$field_total = Field::getFieldCount($criteria);

		$this->view->setVar('data', array(
			'total' => $field_total,
			'count' => count($field_list),
			'rows' => $field_list
		));
	}

	/**
	 * 添加字段
	 */
	public function addFieldAction()
	{
		$creates = $this->request->getPost('creates');

		$data = $creates[0];

		$success = Field::addField($data);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}

	/**
	 * 更新字段
	 * @param string $id
	 */
	public function updateFieldAction($id)
	{
		$updates = $this->request->getPut('updates');
		$data = $updates[0];

		$success = Field::updateField($id, $data);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}

	/**
	 * 删除字段
	 * @param  string $id
	 */
	public function delFieldAction($id)
	{
		$success = Field::delField($id);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}
}