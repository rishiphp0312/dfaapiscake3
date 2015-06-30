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

    //public $uses = ['Users'];


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
                $this->request->data['email'] = $_POST['email'];

            if (isset($_POST['password']) && $_POST['password'] != '')
                $this->request->data['password'] = $_POST['password'];

            $user = $this->Auth->identify();

            $returnData = array();
            $returnData['isAuthenticated'] = false;

            if ($user) {

                $this->Auth->setUser($user);
                $returnData['success'] = true;
                $returnData['data']['id'] = session_id();
                $returnData['data']['user']['id'] = $this->Auth->user('id');
                $returnData['data']['user']['name'] = $this->Auth->user('name');

                if ($this->Auth->user('role_id') == '1')
                    $returnData['data']['user']['role'][] = 'Super Admin';
                else
                    $returnData['data']['user']['role'][] = 'Admin';


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

    // - METHOD TO GET RETURN DATA
    public function returnData($data, $convertJson = '_YES') {

        $data['IsLoggedIn'] = false;
        if ($this->Auth->user('id')) {
            $data['IsLoggedIn'] = true;
        }
        if ($convertJson == '_YES') {
            $data = json_encode($data);
        }
        return $data;
    }

}
