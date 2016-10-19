<?php

/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-7-27
 * Time: 下午10:13
 */
class CommonController extends ControllerBase
{
    public function uploadAction($sub_dir='')
    {
        $files = $this->request->getUploadedFiles();
        $file_base_url = 'http://'.$this->request->getHttpHost().$this->url->get('/uploads/');
        $upload_dir = __DIR__.'/../../public/uploads/';
        if (!empty($sub_dir))
        {
            $file_base_url .= $sub_dir.'/';
            $upload_dir .= $sub_dir.'/';
        }
        if (!is_dir($upload_dir))
        {
            mkdir($upload_dir);
        }
        $result = array();
        foreach($files as $file)
        {
            $temp_name = $file->getTempName();
            if (empty($temp_name)) continue;
            $file_name = md5_file($file->getTempName());
            $upload_file_name = $file->getName();
            $dot_index = strrpos($upload_file_name, '.');
            $file_ext = $dot_index !== false ? substr($upload_file_name, $dot_index + 1, strlen($upload_file_name) - $dot_index + 1) : '';
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