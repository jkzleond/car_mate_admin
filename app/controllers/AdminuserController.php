<?php

class AdminuserController extends ControllerBase
{

    public function loginAction()
    {

        if( $this->request->isGet() ){
            if( $this->cookies->has('remember') )
            {
                $user_id = $this->cookies->get('user_id')->getValue('trim');
                $password = $this->cookies->get('password')->getValue('trim');
                $this->view->setVar('user_id', $user_id);
                $this->view->setVar('password', $password);
                $this->view->setVar('remember', 1);
            }
            return;
        }
        $user_id = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $validate_code = $this->request->getPost('code');
        $remember = $this->request->getPost('remember');
        $error_msg = null;
        if(strtoupper($validate_code) !== $this->session->get('validate_code'))
        {
            $error_msg = $this->trans->_('validate code error');
        }
        else
        {
            $user = AdminUser::getAdminUserByUserId($user_id);
            if(!empty($user))
            {
                if(substr(md5($password), 5, 16) == $user['password'])
                {
                    $session_user = new Phalcon\Session\Bag('user');
                    $auth = AdminUser::getAdminAuthorities($user_id);
                    $city_auth = AdminUser::getAdminCityAuthorities($user_id);
                    $session_user->set('auth', $auth);
                    $session_user->set('city_auth', $city_auth);

                    $province_id = -1;
                    if(count($city_auth) > 0)
                    {
                        $province_id = in_array('0', $city_auth)?0:intval($city_auth[0]);
                    }

                    $session_user->set('province_id', $province_id);

                    $session_user->set('user_id', $user_id);
                    $session_user->set('password', $password);
                    $session_user->set('status', $user['status']);
                    $session_user->set('code_name', $user['code_name']);
                    $session_user->set('create_date', $user['create_date']);
                    $session_user->set('password_expire_date', $user['password_expire_date']);
                    $session_user->set('account_expire_date', $user['account_expire_date']);
                    $session_user->set('nick_name', $user['nick_name']);
                    $session_user->set('u_name', $user['u_name']);
                    $session_user->set('last_login_time', $user['last_login_time']);
                    if( $remember )
                    {
                        $this->cookies->set('remember', '1', strtotime('+1 month'));
                        $this->cookies->set('user_id', $user_id, strtotime('+1 month'));
                        $this->cookies->set('password', $password, strtotime('+1 month'));
                    }
                    else
                    {
                        $this->cookies->get('remember')->delete();
                    }

                    if ($user_id == 'stadium@gygd.com' or $user_id == 'museum@gygd.com')
                    {
                        if (strpos($user_id, 'stadium') !== false)
                        {
                            $session_user->set('auth', array('gygd', 'stadium'));
                        }
                        elseif (strpos($user_id, 'museum') !== false)
                        {
                            $session_user->set('auth', array('gygd', 'museum'));
                        }
                        return $this->response->redirect('/gygd/');
                    }

                    return $this->response->redirect('/');
                }
                else
                {
                    $error_msg = $this->trans->_('password error');
                }
            }
            else
            {
                $error_msg = $this->trans->_('user is not exists');
            }
        }

        if($error_msg)
        {
            if($this->request->isAjax())
            {
                $this->view->disable();
                $this->response->setJsonContent(json_encode(array(
                    'status' => 'failed',
                    'message' => $error_msg
                )));
                return;
            }
            $this->flashSession->error($error_msg);
        }
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect('/login');
    }

    public function changeProvinceAction()
    {
        $province_id = $this->request->getPost('province_id');
        $user = $this->session->get('user');
        $user['province_id'] = $province_id;
        $this->session->set('user', $user);
    }
}

