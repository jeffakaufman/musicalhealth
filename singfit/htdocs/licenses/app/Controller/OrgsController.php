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
	    $valid = $this->Org->checkUser($this->request->data('username'), $this->request->data('password'));
	    $xmlstr = "<?xml version='1.0' standalone='yes'?>
	    <response>
	    </response>";
	    $response = new SimpleXMLElement($xmlstr);
	    if (is_array($valid))
	    {
    	    $response->addChild('status', 'valid');
    	    $response->addChild('metadata');
    	    $response->metadata->addChild('company', $valid['Org']['name']);
    	    $response->metadata->addChild('community', $valid['Org']['community']);
    	    $response->metadata->addChild('licenses_remaining', $valid['Org']['remaining']);    	    
    	    $response->metadata->addChild('expires', $valid['Org']['expiration']);    	    
    	    
	    }
	    else if ($valid == 'expired')
	    {
    	    $response->addChild('status', 'invalid');
    	    $response->addChild('message', 'The license for your organization has expired');
	    }
	    else if ($valid == 'exhausted')
	    {
    	    $response->addChild('status', 'invalid');
    	    $response->addChild('message', 'No more licenses are available for this organization');
	    }
	    else if ($valid == false)
	    {
    	    $response->addChild('status', 'invalid');
    	    $response->addChild('message', 'Invalid username or password');
	    }	    
	    header ("Content-Type:text/xml");  
	    print_r($response->asXML());
        //echo $this->Xml->header(array('version'=>'1.1'));
        //echo $this->Xml->serialize($valid, array('format' => 'tags'));	    
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
