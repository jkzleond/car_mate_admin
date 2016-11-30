<?php

class LocalfavourController extends ControllerBase
{

    public function indexAction()
    {

    }

    /**
     * 本地惠列表
     * @param $offset
     * @param $page_num
     * @param $page_size
     */
    public function localFavourListAction($offset, $page_num, $page_size)
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $local_favour_list = LocalFavour::getLocalFavourList($province_id, $offset, $page_size, $page_num);

        $local_favour_types = LocalFavour::getLocalFavourTypeList();

        $province_list = Province::getProvinceList();
    }

    /**
     * 本地惠发布页面
     */
    public function localFavourPushAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();


        $local_favour_types = LocalFavour::getLocalFavourTypeList();

        $province_list = Province::getProvinceList();

        $city_list = Province::getCityListByPid($province_id);

        $this->view->setVars(array(
            'province_list' => $province_list,
            'city_list' => $city_list,
            'local_favour_types' => $local_favour_types,
            'current_province_id' => $province_id
        ));
    }

    /**
     * 本地惠发布
     */
    public function addLocalFavourAction()
    {
        $creates = $this->request->getPost('creates');
        $local_favour = $creates[0];
        $type_id = $local_favour['type_id'];
        $province_id = $local_favour['province_id'];
        $city_id = $local_favour['city_id'];
        $title = $local_favour['title'];
        $contents = $local_favour['contents'];
        $publish_time = date('Y-m-d H:i:s');
        $des = $local_favour['des'];
        $order_time = date('Y-m-d H:i:s', strtotime('-1 day'));
        $tmp_name = isset($local_favour['tmp_name']) ? $local_favour['tmp_name'] : null;

        $local_favour_id = LocalFavour::addLocalFavour($type_id, $province_id, $city_id, $title, $contents, $publish_time, $des, $order_time);




        if( ( $local_favour_id || $local_favour_id === 0 ) && $tmp_name ){
            $tmp_dir = __DIR__.'/../../public/temp/';

            $tmp_path = $tmp_dir.$tmp_name;

            $pic_data_str = file_get_contents($tmp_path);

            $pic_data = base64_encode($pic_data_str);

            LocalFavour::addLocalFavourPic($local_favour_id, '', $pic_data);

            unlink($tmp_path);
        }

        $this->view->setVar('data', array(
            'success' => $local_favour_id || $local_favour_id === 0,
            'id' => $local_favour_id
        ));
    }

    /**
     * 删除本地惠
     * @param $id
     */
    public function delLocalFavourAction($id)
    {
        $success = LocalFavour::delLocalFavour($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 获取本地惠内容
     * @param $id
     */
    public function getLocalFavourContentAction($id)
    {
        $this->view->disable();
        $content = LocalFavour::getLocalFavourContent($id);
        echo $content;
    }

    /**
     * 上传临时图片(本地惠发布中选择的图片,但还未发布)
     */
    public function uploadTempPicAction()
    {
        $files = $this->request->getUploadedFiles();

        $pic = $files[0];
        $tmp_path = __DIR__.'/../../public/temp/'.basename($pic->getTempName()).'.'.$pic->getExtension();
        $success = $pic->moveTo($tmp_path);

        $this->view->setVar('data', array(
            'success' => $success,
            'tmp_name' => basename($tmp_path)
        ));
    }

    /**
     * 获取本地惠列表
     */
    public function getLocalFavourListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();

        $page_size = $this->request->getPost('rows');
        $page_num = $this->request->getPost('page');

        $local_favour_list = LocalFavour::getLocalFavourList($province_id, $page_num, $page_size);
        foreach($local_favour_list as &$local_favour)
        {
            $local_favour['des'] = preg_replace('@[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s,\.<>\/\?？\\\|;\':\"；‘：“~!\@#$%^&\*\(\)\-=_\+[\]\{\}\`，。！￥%]@u', '', $local_favour['des']);
        }

        $local_favour_total = LocalFavour::getLocalFavourCount();

        $this->view->setVar('data', array(
            'total' => $local_favour_total,
            'count' => count($local_favour_list),
            'rows' => $local_favour_list
        ));
    }

    /**
     *更新本地惠
     */
    public function updateLocalFavourAction()
    {
        $updates = $this->request->getPut('updates');

        $result = false;
        $updated = array();
        foreach($updates as $data)
        {
            $id = isset($data['id']) ? $data['id'] : null;
            $province_id = isset($data['province_id']) ? $data['province_id'] : null;
            $city_id = isset($data['city_id']) ? $data['city_id'] : null;
            $title = !empty($data['title']) ? $data['title'] : null;
            $contents = !empty($data['contents']) ? $data['contents'] : null;
            $type_id = !empty($data['type_id']) ? $data['type_id'] : null;
            $des = !empty($data['des']) ? $data['des'] : null;
            $is_state = isset($data['is_state']) ? $data['is_state'] : null;
            $order_time = !empty($data['order_time']) ? $data['order_time'] : null;

            $rst = LocalFavour::updateLocalFavour($id, $province_id, $city_id, $title, $contents, $type_id, $des, $is_state, $order_time);
            $result = $result || $rst;
            if($rst) array_push($updated, $data);

            //更新本地惠图片
            $tmp_name = isset($data['tmp_name']) ? $data['tmp_name'] : null;
            $pic_id = isset($data['pic_id']) ? $data['pic_id'] : null;

            if($tmp_name)
            {
                $tmp_dir = __DIR__.'/../../public/temp/';

                $tmp_path = $tmp_dir.$tmp_name;

                $pic_data_str = file_get_contents($tmp_path);

                $pic_data = base64_encode($pic_data_str);
                if($pic_id)
                {
                    LocalFavour::updateLocalFavourPic($pic_id, $id, null, $pic_data);
                }
                else
                {
                    LocalFavour::addLocalFavourPic($id, null, $pic_data);
                }
            }

        }

        $this->view->setVar('data', array(
            'success' => $result,
            'updates' => $updated
        ));
    }

    /**
     * 获取本地惠图片
     * @param $id
     */
    public function getLocalFavourPicAction($id)
    {
        $this->view->disable();
        $pic = LocalFavour::getLocalFavourPic($id);
        $this->response->setContentType('image/png');
        echo base64_decode($pic['picData']);
    }

    /**
     * 本地惠评论窗口内容
     */
    public function localFavourCommentAction($pid)
    {
        $comments = LocalFavour::getLocalFavourComment($pid);
        $this->view->setVar('comments', $comments);
    }

    /**
     * 回复本地惠评论
     * @param $id
     */
    public function localFavourCommentReplyAction($id)
    {
        $content = $this->request->getPut('content');
        $result = LocalFavour::replyComment($id, $content);
        $this->view->setVar('data', array(
            'success' => $result,
            'id' => $id,
            'content' => $content
        ));
    }

    /**
     * 首页推广页面
     */
    public function localFavourAdvListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $province_list = Province::getProvinceList();

        $adv_criteria = array(
            'province_id' => $province_id,
            'is_state' => 1
        );
        $adv_use_list = LocalFavour::getLocalFavourAdvList($adv_criteria);

        $this->view->setVars(array(
            'province_list' => $province_list,
            'current_province_id' => $province_id,
            'adv_use_list' => $adv_use_list
        ));
    }

    /**
     * 获取首页推广列表
     */
    public function getLocalFavourAdvListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();

        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $adv_criteria = array(
            'province_id' => $province_id
        );
        $adv_list = LocalFavour::getLocalFavourAdvList($adv_criteria, $page_num, $page_size);
        foreach($adv_list as &$adv)
        {
            $adv['contents'] = preg_replace('@[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s,\.<>\/\?？\\\|;\':\"；‘：“~!\@#$%^&\*\(\)\-=_\+[\]\{\}\`，。！￥%]@u', '', $adv['contents']);
        }
        $adv_total = LocalFavour::getLocalFavourAdvCount($adv_criteria);

        $this->view->setVar('data', array(
            'total' => $adv_total,
            'count' => count($adv_list),
            'rows' => $adv_list
        ));
    }

    /**
     * 获取指定ID首页推广信息
     * @param $id
     */
    public function getLocalFavourAdvAction($id)
    {
        $adv_list = LocalFavour::getLocalFavourAdvList(array('id' => $id));
        $this->view->setVar('data', array(
            'success' => !empty($adv_list),
            'data' => !empty($adv_list) ? $adv_list[0] : null
        ));
    }

    /**
     * 添加推广
     */
    public function addLocalFavourAdvAction()
    {
        $creates = $this->request->getPost('creates');
        $local_favour_adv = $creates[0];

        $rele_id = !empty($local_favour_adv['rele_id']) ? $local_favour_adv['rele_id'] : null;
        $adv = isset($local_favour_adv['adv_src']) ? $local_favour_adv['adv_src'] : null;
        $adv3 = isset($local_favour_adv['adv_src']) ? $local_favour_adv['adv_src'] : null;
        $is_state = isset($local_favour_adv['is_state']) ? $local_favour_adv['is_state'] : 0;
        $type = $local_favour_adv['type'];
        $province_id = $local_favour_adv['province_id'];
        $contents = !empty($local_favour_adv['contents']) ? $local_favour_adv['contents'] : null;


        $success = LocalFavour::addLocalFavourAdv($rele_id, $adv, $adv3, $is_state, $type, $province_id, $contents);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新推广
     */
    public function updateLocalFavourAdvAction()
    {
        $updates = $this->request->getPut('updates');

        $result = false;
        $updated = array();

        foreach($updates as $local_favour_adv)
        {
            $id = $local_favour_adv['id'];
            $rele_id = !empty($local_favour_adv['rele_id']) ? $local_favour_adv['rele_id'] : null;
            $adv = isset($local_favour_adv['adv_src']) ? $local_favour_adv['adv_src'] : null;
            $is_state = isset($local_favour_adv['is_state']) ? $local_favour_adv['is_state'] : null;
            $is_order = isset($local_favour_adv['is_order']) ? $local_favour_adv['is_order'] : null;
            $type = isset($local_favour_adv['type']) ? $local_favour_adv['type'] : null;
            $province_id = isset($local_favour_adv['province_id']) ? $local_favour_adv['province_id'] : null;
            $contents = !empty($local_favour_adv['contents']) ? $local_favour_adv['contents'] : null;
            $rst = LocalFavour::updateLocalFavourAdv($id, array(
                'rele_id' => $rele_id,
                'adv' => $adv,
                'is_state' => $is_state,
                'is_order' => $is_order,
                'type' => $type,
                'province_id' => $province_id,
                'contents' => $contents
             ));
            $result = $result || $rst;
            if($result) array_push($updated, $local_favour_adv);
        }

        $this->view->setVar('data', array(
            'success' => $result,
            'updates' => $updated
        ));
    }

    /**
     * 删除推广
     * @param $id
     */
    public function delLocalFavourAdvAction($id)
    {
        $success = LocalFavour::delLocalFavourAdv($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }


    /**
     * 本地惠稿件管理页面
     */
    public function localFavourSubListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $local_favour_types = LocalFavour::getLocalFavourTypeList();
        $province_list = Province::getProvinceList();
        $city_list = Province::getCityListByPid($province_id);

        $this->view->setVars(array(
            'province_list' => $province_list,
            'city_list' => $city_list,
            'local_favour_types' => $local_favour_types,
            'current_province_id' => $province_id
        ));
    }

    /**
     * 获取本地惠稿件列表
     */
    public function getLocalFavourSubListAction()
    {
        $province_id = AdminUser::getCurrentProvinceId();
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $local_favour_sub_list = LocalFavour::getLocalFavourSubList($province_id, $page_num, $page_size);
        $local_favour_sub_total = LocalFavour::getLocalFavourSubCount($province_id);

        $this->view->setVar('data', array(
            'total' => $local_favour_sub_total,
            'count' => count($local_favour_sub_list),
            'rows' => $local_favour_sub_list,
            'page_num' => $page_num,
            'page_size' => $page_size
        ));
    }

    /**
     * 获取本地惠稿件图片数据
     * @param $id
     */
    public function getLocalFavourSubPicAction($id)
    {
        $this->view->disable();
        $pic = LocalFavour::getLocalFavourSubPic($id);
        $this->response->setContentType('image/png');
        echo base64_decode($pic['pic']);
    }

    /**
     * 获取本地惠稿件
     * @param $id
     */
    public function getLocalFavourSubAction($id)
    {
        $local_favour_sub = LocalFavour::getLocalFavourSub($id);

        $this->view->setVar('data', $local_favour_sub);

    }

    /**
     * 删除本地惠稿件
     * @param $id
     */
    public function delLocalFavourSubAction($id)
    {
        $success = LocalFavour::delLocalFavourSub($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 滚动广告管理页面
     */
    public function localFavourScrollAdAction()
    {
        $use_list = LocalFavourScrollAd::getUseList();

        $this->view->setVars(array(
            'use_list' => $use_list
        ));
    }

    /**
     * 获取滚动广告列表
     */
    public function getLocalFavourScrollAdListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');
        $scroll_ad_list = LocalFavourScrollAd::getScrollAdList($page_num, $page_size);
        $scroll_ad_total = LocalFavourScrollAd::getScrollAdCount();

        $this->view->setVar('data', array(
            'total' => $scroll_ad_total,
            'count' => count($scroll_ad_list),
            'rows' => $scroll_ad_list
        ));
    }

    /**
     * 添加滚动广告
     */
    public function addLocalFavourScrollAdAction()
    {
        $creates = $this->request->getPost('creates');
        $scroll_ad = $creates[0];
        $pic_data = $scroll_ad['pic_data'];
        $redirect_url = $scroll_ad['redirect_url'];
        $success = LocalFavourScrollAd::addScrollAd($pic_data, $redirect_url);

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 更新滚动广告
     */
    public function updateLocalFavourScrollAdAction()
    {
        $updates = $this->request->getPut('updates');

        $step_success = null;

        $success = false;

        foreach($updates as $scroll_ad)
        {
            $id = $scroll_ad['id'];
            $pic_data = isset($scroll_ad['pic_data']) ? $scroll_ad['pic_data'] : null;
            $redirect_url = isset($scroll_ad['redirect_url']) ? $scroll_ad['redirect_url'] : null;
            $show_order = isset($scroll_ad['show_order']) ? $scroll_ad['show_order'] : null;
            $is_state = isset($scroll_ad['is_state']) ? $scroll_ad['is_state'] : null;

            $step_success = LocalFavourScrollAd::updateScrollAd($id, $pic_data, $redirect_url, $show_order, $is_state);
            $success = $success || $step_success;
        }

        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 删除滚动广告
     * @param $id
     */
    public function delLocalFavourScrollAdAction($id)
    {
        $success = LocalFavourScrollAd::delScrollAd($id);
        $this->view->setVar('data', array(
            'success' => $success
        ));
    }

    /**
     * 汽车信息校对页面
     */
    public function autoConfigAction()
    {

    }

    /**
     * 获取车辆信息列表
     */
    public function getAutoListAction()
    {
        $page_num = $this->request->getPost('page');
        $page_size = $this->request->getPost('rows');

        $page_num = is_int($page_num) ? $page_num : intval($page_num);
        $page_size = is_int($page_size) ? $page_size : intval($page_size);

        $auto_list = AutoTree::getAutoList($page_num, $page_size);
        $total = AutoTree::getCount();

        $this->view->setVar('data', array(
            'total' => $total,
            'count' => count($auto_list),
            'rows' => $auto_list
        ));
    }

    /**
     * 汽车图片
     * @param $id
     */
    public function getAutoPicAction($id)
    {
        $this->view->disable();
        $this->response->setContentType('image/jpeg');
        $pic_data = AutoTree::getPicData($id);
        echo base64_decode($pic_data);
    }

}

