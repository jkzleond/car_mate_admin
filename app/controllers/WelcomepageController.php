<?php

class WelcomepageController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 开屏广告管理页面
     */
    public function welcomeAdvListAction()
    {
        $province_list = Province::getProvinceList();
        $this->view->setVar('province_list', $province_list);
    }

    /**
     * 获取开屏广告列表
     */
    public function getWelcomeAdvListAction()
    {
        $page_num = $this->request->get('page');
        $page_size = $this->request->get('rows');
        $province_id = AdminUser::getCurrentProvinceId();

        $welcome_adv_list = WelcomePage::getWelcomeAdvList($province_id, $page_num, $page_size);
        $welcome_adv_total = WelcomePage::getWelcomeAdvCount($province_id);

        $this->view->setVar('data', array(
            'total' => $welcome_adv_total,
            'count' => count($welcome_adv_list),
            'rows' => $welcome_adv_list
        ));
    }

    /**
     * 获取广告图片
     * @param $id
     */
    public function getAdvPicAction($id)
    {
        $this->view->disable();
        $this->response->setContentType('image/png');

        $controller_name = $this->dispatcher->getControllerName();

        $img_cache_path = __DIR__.'/../../public/images/cache/'.$controller_name.'/advpics';

        //缓存不存在则写入缓存
        if(!file_exists($img_cache_path.'/'.$id.'.png'))
        {
            if(!is_dir($img_cache_path))
            {
                mkdir($img_cache_path, 0777, true);
            }
            $pic_data = WelcomePage::getAdvPicDataById($id);
            file_put_contents($img_cache_path.'/'.$id.'.png', base64_decode($pic_data));
        }
        $this->response->redirect($this->url->get('/images/cache/'.$controller_name.'/advpics/'.$id.'.png'));
    }

    /**
     * 添加开屏广告
     */
    public function addWelcomeAdvAction()
    {
        $creates = $this->request->getPost('creates');

        $adv = new Criteria($creates[0]);

        $success = WelcomePage::addWelcomeAdv($adv->province_id, $adv->url, $adv->pic_data);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除开屏广告
     * @param $id
     */
    public function delWelcomeAdvAction($id)
    {
        $success = WelcomePage::delWelcomeAdv($id);

        //如果成功要删除图片缓存
        if($success)
        {
            $controller_name = $this->dispatcher->getControllerName();
            $img_cache_path = __DIR__.'/../../public/images/cache/'.$controller_name.'/advpics';
            $img_file_name = $img_cache_path.'/'.$id.'.png';
            //缓存存在则删除
            if(file_exists($img_file_name))
            {
                unlink($img_file_name);
            }
        }

        $this->view->setVar('data', array(
           'success' => $success
        ));
    }

    /**
     * 更新开屏广告
     */
    public function updateWelcomeAdvAction()
    {
        $updates = $this->request->getPut('updates');

        $adv = $updates[0];
        $id = $adv['id'];

        $success = WelcomePage::updateWelcomeAdv($id, $adv);

        //如果更新了图片数据就删除旧的缓存
        if($success and isset($adv['pic_data']))
        {
            $controller_name = $this->dispatcher->getControllerName();
            $img_cache_path = __DIR__.'/../../public/images/cache/'.$controller_name.'/advpics';
            $img_file_name = $img_cache_path.'/'.$id.'.png';
            //缓存存在则删除
            if(file_exists($img_file_name))
            {
                unlink($img_file_name);
            }
        }

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 开屏广告弃用
     * @param $id
     */
    public function welcomeAdvDisableAction($id)
    {
        $success = WelcomePage::updateWelcomeAdv($id, array('is_state' => 0));

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 开屏广告使用
     * @param $id
     */
    public function welcomeAdvEnableAction($id)
    {
        $success = WelcomePage::updateWelcomeAdv($id, array('is_state' => 1));

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

}

