<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-7-27
 * Time: 下午10:13
 */
class CommonController extends ControllerBase
{
    public function uploadAction()
    {
        $files = $this->request->getUploadedFiles();
        $file_base_url = 'http://'.$this->request->getHttpHost().$this->url->get('/uploads/');
        $result = array();
        foreach($files as $file)
        {
            $upload_dir = __DIR__.'/../../public/uploads/';
            $file_name = md5_file($file->getRealPath());
            $file_ext = $file->getExtension();
            $move_to_path = $upload_dir.$file_name.'.'.$file_ext;
            if( file_exists($move_to_path) or (!file_exists($move_to_path) and $file->moveTo($move_to_path)) )
            {
                $url = $file_base_url.$file_name.'.'.$file_ext;
                $result[$file->getKey()] = $url;
            }
        }
        $this->view->setVar('data', array(
            'success' => !empty($result),
            'data' => $result
        ));
    }
}