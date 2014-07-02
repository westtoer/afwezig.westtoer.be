<?php
class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('add', 'logout');
    }

    public function login() {
        $this->layout = 'login';
        if($this->Auth->loggedIn()){
            $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
        }
        else {if ($this->request->is('post')) {
            if ($this->Auth->login()) {
               return $this->redirect(array('controller' => 'verlofs'));
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
        $this->set('users', $this->User->find('all', array('fields' => array('id', 'email', 'name', 'surname', 'group'), 'order' => 'User.id ASC', 'group' => 'User.id')));

    }

    public function add() {
        $userrole = $this->Session->read('Auth.User.role');
        if($userrole !== 'admin'){
            $this->Session->setFlash(__('You are not allowed to create new users!'));
            return $this->redirect(array('controller' => 'users', 'action' => 'login'));
        } else {
            if ($this->request->is('post')) {
                $this->User->create();
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('The user has been saved'));
                    return $this->redirect(array('action' => 'index'));
                }
                $this->Session->setFlash(
                    __('The user could not be saved. Please, try again.')
                );
            }
       }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->request->onlyAllow('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }



}