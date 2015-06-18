<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-20
 * Time: 下午11:56
 */

class EmuController extends ControllerBase
{
    public function indexAction()
    {
        $this->response->setContentType('text/html');
        $this->view->disable();
        //$url = 'http://www.ynjtcx.gov.cn/indexController/getSurList?';
        //$data = "hpzl=02&clsbdh=7419&hphm=云A352xx";
        $url = 'http://car0.autoimg.cn/car/upload/2015/3/2/t_2015030208111799526410.jpg';
        echo base64_encode($this->_doGet($url, array()));
    }

    public function validatecodeAction()
    {
        $this->view->disable();
        $code_url = 'http://localhost:8080/index.php?mod=HeBei&act=checkCode&hphm=冀A9L399&hpzl=小型汽车';
        $res = file_get_contents($code_url);
        $xml = simplexml_load_string($res);
        $client = $xml->xpath('param/client')[0];
        $validate_code = $xml->xpath('param/validateCode')[0];
        $this->cookies->set('client', $client);
        $this->response->setContentType('image/jpeg');
        echo base64_decode($validate_code);
    }

    public function fileUploadAction()
    {

    }

    public function changeUserAvatarAction()
    {
        $files = $this->request->getUploadedFiles();

        $avatar = $files[0];
        $tmp_path =  $avatar->getTempName();
        $avatar_data = base64_encode(file_get_contents($tmp_path));
        $user_id = $this->request->getPost('user_id');


        $sql = 'insert into dbo.IAM_USERPHOTO ( USERID, PHOTO ) values ( :user_id, :photo  )';

        $bind = array(
            'user_id' => $user_id,
            'photo' => $avatar_data
        );

        $this->db->execute($sql, $bind);

        $referer = $this->request->getHTTPReferer();

        $this->response->redirect($referer);
    }

    public function queryAction()
    {
        $this->view->disable();
        $code_url = 'http://localhost:8080/index.php?mod=HeBei&act=query';
        $hphm = urlencode('冀A9L399');//$this->request->getPost('hphm');
        //$hpzl = //$this->request->getPost('hpzl');
        $captcha = $this->request->getPost('captcha');
        $client = $this->cookies->get('client');
        $data = 'captcha='.$captcha.'&hphm='.$hphm.'&client='.$client;
        $res = $this->_doPost($code_url, $data);
        echo $res;
    }

    public function gzwfAction()
    {

        if($this->request->isGet())
        {
//            $this->view->disable();
            $code_xml_str = $this->_doGet('http://localhost:8090/index.php?mod=GuiZhou&act=checkCode');
//            echo $code_xml_str;
            $code_xml = simplexml_load_string($code_xml_str);
            $return_code = $code_xml->return;
            if($return_code == '00')
            {
                $client = $code_xml->param->client;
                $code_img = $code_xml->param->validateCode;
                $this->view->setVars(array(
                    'client' => $client,
                    'img_data' => $code_img
                ));
            }
        }
        else if($this->request->isPost())
        {
            $this->view->disable();

            $post = $this->request->getPost();

            $data = '';
            foreach($post as $key => $value)
            {
                $data .= $key.'='.$value.'&';
            }

            $res = $this->_doPost('http://localhost:8090/index.php?mod=GuiZhou&act=query', $data);
            echo $res;
        }

    }

    //安徽
    public function anhuiAction()
    {
        if($this->request->isGet())
        {
//            $this->view->disable();
            $code_xml_str = $this->_doGet('http://116.55.248.76:8090/weifa/index.php?mod=AnHui&act=checkCode');

            $code_xml = simplexml_load_string($code_xml_str);
            $return_code = $code_xml->return;
            if($return_code == '00')
            {
                $client = $code_xml->param->client;
                $code_img = $code_xml->param->validateCode;
                $this->view->setVars(array(
                    'client' => $client,
                    'img_data' => $code_img
                ));
            }
        }
        else if($this->request->isPost())
        {
            $this->view->disable();

            $post = $this->request->getPost();

            $data = '';
            foreach($post as $key => $value)
            {
                $data .= $key.'='.$value.'&';
            }

            $res = $this->_doPost('http://116.55.248.76:8090/weifa/index.php?mod=AnHui&act=query', $data);
            echo $res;
        }
    }

    public function cwwdAction()
    {
        if($this->request->isGet())
        {
//            $this->view->disable();
            $code_xml_str = $this->_doGet('http://116.55.248.76:8090/weifa/index.php?mod=CWDDD&act=checkCode');

            $code_xml = simplexml_load_string($code_xml_str);
            $return_code = $code_xml->return;
            if($return_code == '00')
            {
                $client = $code_xml->param->client;
                $code_img = $code_xml->param->validateCode;
                $this->view->setVars(array(
                    'client' => $client,
                    'img_data' => $code_img
                ));
            }
        }
        else if($this->request->isPost())
        {
            $this->view->disable();

            $post = $this->request->getPost();

            $data = '';
            foreach($post as $key => $value)
            {
                $data .= $key.'='.$value.'&';
            }

            $res = $this->_doPost('http://116.55.248.76:8090/weifa/index.php?mod=CWDDD&act=query', $data);
            echo $res;
        }
    }

    public function hlzAction()
    {
        if($this->request->isGet())
        {
//            $this->view->disable();
            $code_xml_str = $this->_doGet('http://localhost:8090/index.php?mod=HeiLongJiang&act=checkCode');

            $code_xml = simplexml_load_string($code_xml_str);
            $return_code = $code_xml->return;
            if($return_code == '00')
            {
                $client = $code_xml->param->client;
                $code_img = $code_xml->param->validateCode;
                $this->view->setVars(array(
                    'client' => $client,
                    'img_data' => $code_img
                ));
            }
        }
        else if($this->request->isPost())
        {
            $this->view->disable();

            $post = $this->request->getPost();

            $data = '';
            foreach($post as $key => $value)
            {
                $data .= $key.'='.$value.'&';
            }

            $res = $this->_doPost('http://localhost:8090/index.php?mod=HeiLongJiang&act=query', $data);
            echo $res;
        }
    }

    private function _doGet($url, $headers=array(), $cookies='')
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_COOKIE => $cookies
        ));
        $result =  curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function _doPost($url, $data, $headers=array(), $cookies='')
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_COOKIE => $cookies
        ));
        $result =  curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}