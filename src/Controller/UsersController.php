<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController {

    //Loading Components
    //public $components = ['Auth'];
     var $layout = 'home';
     var $Users = '';
     //public $uses = ['Users'];
     public $components = ['UserCommon','Common'];

    public function initialize() {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'loginRedirect' => [
                'controller' => 'Users',
                'action' => 'view'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email']
                ]]
        ]);
        
      //  $this->Users =TableRegistry::get('Users');
    }

    //services/serviceQuery
    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['add', 'logout', 'login', 'view']);
    }

    /**
     * 
     * @return JSON/boolean
     * @throws NotFoundException When the view file could not be found
     * 	or MissingViewException in debug mode.
     *  Function is basically used for user login functionality
     */
    public function login() {
        try {

            if (isset($_POST['email']) && $_POST['email'] != '')
                $this->request->data[_USER_EMAIL] = $_POST['email'];

            if (isset($_POST['password']) && $_POST['password'] != '')
                $this->request->data[_USER_PASSWORD] = $_POST['password'];

            $user = $this->Auth->identify();

            $returnData = array();
            $returnData['isAuthenticated'] = false;

            if ($user) {

                $this->Auth->setUser($user);
                $returnData['success'] = true;
                $returnData['data']['id'] = session_id();
                $returnData['data']['user'][_USER_ID]    = $this->Auth->user('id');
                $returnData['data']['user'][_USER_NAME]  = $this->Auth->user('name');
                $fieldsArray =  array();
                $updatelogindata[_USER_ID]           = $this->Auth->user('id');
                $this->UserCommon->updateLastLoggedIn($updatelogindata);      

                if ($this->Auth->user('role_id') == _SUPERADMINROLEID){
					// $rdt = $this->Common->getRoleDetails($role_id);
                    // $dataUsrUserRole[] = $rdt[1];
					$returnData['isSuperAdmin'] = true;
					$returnData['data']['user']['role'][] = _SUPERADMINNAME;
            
				} else{
					$returnData['data']['user']['role'][] = '';

				}
                    

                if ($this->Auth->user('id')) {
                    $returnData['isAuthenticated'] = true;
                }
                echo json_encode($returnData);
                exit;
            } else {

                $returnData['success'] = false;
                echo json_encode($returnData);
                exit;
            }
			
        } catch (MissingTemplateException $e) {

            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
		//return $this->service_response($returnData, $convertJson);
        //return $this->returnData($returnData, $convertJson);
    }

    /**
     * 
     * @return JSON/boolean
     * @throws NotFoundException When the view file could not be found
     * 	or MissingViewException in debug mode.
     *  Function is basically used for user logout functionality 
     */
    public function logout() {
        $returnData = array();
        $returnData['isAuthenticated'] = false;

        if ($this->Auth->logout()) {
            $returnData['success'] = true;
        }
        echo json_encode($returnData);
        exit;
    }

    /**
     * 
     * @return JSON/boolean
     * @throws NotFoundException When the view file could not be found
     * 	or MissingViewException in debug mode.
     *  Function is basically used for user logout functionality 
     */
    public function view() {
        
    }

    // service query ends here 
    // - METHOD TO GET RETURN DATA
    
    public function service_response($response, $convertJson = _YES, $dbId) {
        
        // Initialize Result
		//pr($response);
        $success = false;
        $isAuthenticated = false;
        $isSuperAdmin = false;
        $errCode = '';
        $errMsg = '';
        $dataUsrId = '';
        $dataUsrUserId = '';
        $dataUsrUserName = '';
        $dataUsrUserRole = [];
        $dataDbDetail = '';
        $dataUsrDbRoles = [];
        
        if ($this->Auth->user('id')) {
            
            $isAuthenticated = true;
            $dataUsrId = session_id();
            $dataUsrUserId = $this->Auth->user('id');
            $dataUsrUserName = $this->Auth->user('name');
            $role_id = $this->Auth->user('role_id');
            //$userDetails = $this->Auth->user();
            
            if ($role_id == _SUPERADMINROLEID):
                $isSuperAdmin = true;
                $rdt = $this->Common->getRoleDetails($role_id);
                $dataUsrUserRole[] = $rdt[1];
            endif;

            if ($dbId):
                $returnSpecificDbDetails = $this->Common->getDbNameByID($dbId);
                $dataDbDetail = $returnSpecificDbDetails;

                if ($role_id != _SUPERADMINROLEID):				
                    $dataUsrDbRoles = $this->UserCommon->findUserDatabasesRoles($dataUsrUserId, $dbId);
                endif;
            endif;
        }
        
        if(isset($response['status']) && $response['status'] == _SUCCESS):
            $success = true;
            $responseData = isset($response['data']) ? $response['data'] : [];
        else:
            $errCode = isset($response['errCode']) ? $response['errCode']:'';
            $errMsg = isset($response['errMsg']) ? $response['errMsg'] : '';
        endif;
        
        // Set Result
        $returnData['success'] = $success;
        $returnData['isAuthenticated'] = $isAuthenticated;
        $returnData['isSuperAdmin'] = $isSuperAdmin;
        $returnData['err']['code'] = $errCode;
        $returnData['err']['msg'] = $errMsg;
        $returnData['data']['usr']['id'] = $dataUsrId;
        $returnData['data']['usr']['user']['id'] = $dataUsrUserId;
        $returnData['data']['usr']['user']['name'] = $dataUsrUserName;
        $returnData['data']['usr']['user']['role'] = $dataUsrUserRole;
        $returnData['data']['dbDetail'] = $dataDbDetail;
        $returnData['data']['usrDbRoles'] = $dataUsrDbRoles;
       // $returnData['data']['dataUsrUserId'] = $dataUsrUserId;
       // $returnData['data']['db_id'] = $dbId;
		//$dataUsrUserId, $dbId
        
        if($success == true){
			$responseKey='';
			if(isset($response['responseKey']) && !empty($response['responseKey']))
			$responseKey = $response['responseKey'];
			if(isset($responseKey) && !empty($responseKey))
            $returnData['data'][$responseKey] = $responseData;
		    //else
			//$returnData['data'][] = $responseData;
		   	
        }
        
        if ($convertJson == _YES) {
            $returnData = json_encode($returnData);
        }
        
        // Return Result
        if (!$this->request->is('requested')) {
            $this->response->body($returnData);
            return $this->response;
        } else {
            return $returnData;
        }
        
    }

}
