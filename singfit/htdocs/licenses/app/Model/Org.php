<?php
App::uses('AppModel', 'Model');
/**
 * Org Model
 *
 */
class Org extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	
	public function beforeSave($options = array())
	{
    	$this->data['Org']['hash'] = md5($this->data['Org']['password']);
    	//if (!array_key_exists('id', $this->data['Org']))
    	//{
            //$this->data['Org']['remaining'] = $this->data['Org']['licenses'];
    	//}
    	return true;
	}
	public function checkUser($username, $password)
	{
    	$org = $this->find('first', array(
    	    'conditions' => array(
    	        'username' => strtolower($username),  
    	        'hash' => md5($password)
    	        ),
    	   'fields' => array(
    	        'Org.id', 'Org.name', 'Org.community', 'Org.licenses', 'Org.remaining', 'Org.expiration'
                )));
        if ($org == null)
        {
            return false;
        }
        
        if ($org['Org']['remaining'] == 0 )
        {
            return 'exhausted';
        } 
        
        $expirationDate = new DateTime($org['Org']['expiration']);
        if (new DateTime() > $expirationDate)
        {
            return 'expired';
        }
        
        $this->id = $org['Org']['id'];
        $this->saveField('remaining', --$org['Org']['remaining'], false);
        return $org;
	}
}
