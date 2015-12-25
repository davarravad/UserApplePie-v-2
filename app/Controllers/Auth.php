<?php namespace controllers;
use \Core\View,
	\Core\Controller,
	\Helpers\Url,
	\Helpers\Request as Request;

class Auth extends Controller {
	
	private $auth;
	
	public function __construct(){
		parent::__construct();
		$this->auth = new \Helpers\Auth\Auth();
	}
	
	// Logs the user into the system
	public function Login(){
		
		if($this->auth->isLogged()){
			Url::redirect();
		}
		
		if(isset($_POST['submit'])){
				
			// Catch username an password inputs using the Request helper
			$username = Request::post('username');
			$password = Request::post('password');
			
			// Login Validation
			if(empty($username)){
				$error[] = 'UserName is Blank!';
			}else if(empty($password)){
				$error[] = 'Password is Blank!';
			}else if(!$this->auth->login($username, $password)){		
				$error[] = 'Incorrect UserName or Password!';
			}
			
			// User Passed Validation - Let them in
			if(!$error){
				// User is good to go
				$data = array('LastLogin' => date('Y-m-d G:i:s'));
				$where = array('id' => $this->auth->getID($username));
				$this->auth->updateUser($data,$where);
				
				//Url::redirect();
			}
		}
		
		$data['title'] = 'Login';
		View::rendertemplate('header',$data);
		View::render('auth/Login',$data,$error);
		View::rendertemplate('footer',$data);
		
	}
	
	// Logs the user out of the system
	public function Logout(){
		$this->auth->logout();
		Url::redirect();
	}
	
}