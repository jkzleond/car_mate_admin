<?php
class CarserviceController extends ControllerBase
{	
	public function indexAction()
	{

	}

	//汽车服务内容管理页面
	public function manageAction()
	{
		
	}

	/**
	 * 获取汽车服务列表数据
	 */
	public function getCarServiceListAction()
	{
		$page_num = $this->request->getPost('page');
		$page_size = $this->request->getPost('rows');
		$criteria = $this->request->getPost('criteria');
		$criteria['type'] = 'car_service';

		$car_service_list = SimpleMod::getSimpleModList($criteria, $page_num, $page_size);
		$car_service_total = SimpleMod::getSimpleModCount($criteria);

		$this->view->setVar('data', array(
			'total' => $car_service_total,
			'count' => count($car_service_list),
			'rows' => $car_service_list
		));
	}

	public function getCarServiceImgAction($id)
	{
		$this->view->disable();
		$img_data = SimpleMod::getImageDataById($id);
		$this->response->setContentType('image/png');
		echo base64_decode($img_data);
	}

	/**
	 * 添加汽车服务
	 */
	public function addCarServiceAction()
	{
		$creates = $this->request->getPost('creates');
		$data = $creates[0];
		$data['type'] = 'car_service';
		$success = SimpleMod::addSimpleMod($data);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}

	/**
	 * 更新汽车服务
	 * @param  int|string $id
	 */
	public function updateCarServiceAction($id)
	{
		$updates = $this->request->getPut('updates');
		$data = $updates[0];

		$success = SimpleMod::updateSimpleMod($id, $data);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}

	/**
	 * 删除汽车服务
	 */
	public function delCarServiceAction($id)
	{
		$success = SimpleMod::delSimpleMod($id);

		$this->view->setVar('data', array(
			'success' => $success
		));
	}


}