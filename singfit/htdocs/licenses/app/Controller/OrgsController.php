<?php
App::uses('AppController', 'Controller');
/**
 * Orgs Controller
 *
 * @property Org $Org
 */
class OrgsController extends AppController {

/**
 * index method
 *
 * @return void
 */
 
 
	public function index() {
		$this->Org->recursive = 0;
		$this->set('orgs', $this->paginate());
	}

public function login()
{
    $this->autoRender = false;
	if ($this->request->is('post')) 
	{
	    $valid = @$this->Org->checkUser($this->request->data('username'), $this->request->data('password'));
	 
	    $xmlstr = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">	    
	    <plist version="1.0">
        </plist>';
	    $response = new SimpleXMLElement($xmlstr);
        $response->addChild('dict');	    
        $responseBody = $response->dict;
        $responseBody->addChild('key', 'response');
        $responseBody->addChild('dict');
	    if (is_array($valid))
	    {    	    	    
    	   $responseBody->dict->addChild('key', 'status');
    	   $responseBody->dict->addChild('string', 'valid');    	
    	   
    	   $responseBody->dict->addChild('key', 'expires');
    	   $responseBody->dict->addChild('string', $valid['Org']['expiration']);   
    	   
    	   $responseBody->dict->addChild('key', 'company');
    	   $responseBody->dict->addChild('string', $valid['Org']['name']);   
    	   
    	   $responseBody->dict->addChild('key', 'community');
    	   $responseBody->dict->addChild('string', $valid['Org']['community']);   

    	   $responseBody->dict->addChild('key', 'licenses_remaining');
    	   $responseBody->dict->addChild('string', $valid['Org']['remaining']);       	           	        	    
	    }
	    else if ($valid == 'expired')
	    {
    	   $responseBody->dict->addChild('key', 'message');        
    	   $responseBody->dict->addChild('string', 'The license for your organization has expired');
    	   $responseBody->dict->addChild('key', 'status');
    	   $responseBody->dict->addChild('string', 'invalid');    	        	    
	    }
	    else if ($valid == 'exhausted')
	    {
    	   $responseBody->dict->addChild('key', 'status');
    	   $responseBody->dict->addChild('string', 'invalid');    	        	    
    	   $responseBody->dict->addChild('key', 'message');        
    	   $responseBody->dict->addChild('string', 'No more licenses are available for this organization');
	    }
	    else if ($valid == false)
	    {
    	   $responseBody->dict->addChild('key', 'status');
    	   $responseBody->dict->addChild('string', 'invalid');    	        	    
    	   $responseBody->dict->addChild('key', 'message');        
    	   $responseBody->dict->addChild('string', 'Invalid username or password');
	    }	    
	    header ("Content-Type:text/xml");  
	    print_r($response->asXML());    
	}
	else
	{
        throw new MethodNotAllowedException();
	}
}

public function test()
{

}
/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Org->create();
			if ($this->Org->save($this->request->data)) {
				$this->Session->setFlash(__('The org has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The org could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Org->id = $id;
		if (!$this->Org->exists()) {
			throw new NotFoundException(__('Invalid org'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Org->save($this->request->data)) {
				$this->Session->setFlash(__('The org has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The org could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Org->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Org->id = $id;
		if (!$this->Org->exists()) {
			throw new NotFoundException(__('Invalid org'));
		}
		if ($this->Org->delete()) {
			$this->Session->setFlash(__('Org deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Org was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Org->recursive = 0;
		$this->set('orgs', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Org->id = $id;
		if (!$this->Org->exists()) {
			throw new NotFoundException(__('Invalid org'));
		}
		$this->set('org', $this->Org->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Org->create();
			if ($this->Org->save($this->request->data)) {
				$this->Session->setFlash(__('The org has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The org could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Org->id = $id;
		if (!$this->Org->exists()) {
			throw new NotFoundException(__('Invalid org'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Org->save($this->request->data)) {
				$this->Session->setFlash(__('The org has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The org could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Org->read(null, $id);
		}
	}

/**
 * admin_delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Org->id = $id;
		if (!$this->Org->exists()) {
			throw new NotFoundException(__('Invalid org'));
		}
		if ($this->Org->delete()) {
			$this->Session->setFlash(__('Org deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Org was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
