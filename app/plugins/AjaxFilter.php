<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-23
 * Time: 上午9:49
 */

use Phalcon\Mvc\User\Plugin;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

class AjaxFilter extends Plugin {

    protected function _requestDataType()
    {
        $uri = $this->router->getRewriteUri();
        $base_name = basename($uri);
        $ext_name = null;
        if(($pos = strpos($base_name, '.')) !== false)
        {
            $ext_name = substr($base_name, $pos + 1);
        }
        return $ext_name?$ext_name:'html';
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        if ($this->request->isAjax())
        {
            $data_type = $this->_requestDataType();
            if($data_type !== 'html')
            {
                $this->view->disable();
            }

            if($data_type === 'json')
            {
                $this->response->setContentType('text/json');
            }
        }
    }

    public function afterDispatchLoop(Event $event, Dispatcher $dispatcher)
    {
        if($this->response->isSent()) return;

        if($this->request->isAjax() || 1)
        {
            $data_type = $this->_requestDataType();
            if($data_type === 'json')
            {
                $data = $this->view->getVar('data');
                $this->response->setContent(json_encode($data));
            }
        }

        $this->response->send();

    }



}