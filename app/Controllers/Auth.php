<?php namespace controllers;
use \Core\View,
	\Core\Controller,
	\Helpers\Url,
	\Helpers\Request as Request;

class Auth extends Controller {
	

	
	// Logs the user into the system
	public function Login(){
		
		// Check to make sure user is not already logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
		
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
				
			// Catch username an password inputs using the Request helper
			$username = Request::post('username');
			$password = Request::post('password');
			
			// Login Validation
			if($this->auth->login($username, $password)){		
				// User Passed Validation - Let them in
				// User is good to go
				$data = array('LastLogin' => date('Y-m-d G:i:s'));
				$where = array('userID' => $this->auth->getID($username));
				$this->auth->updateUser($data,$where);
				
				Url::redirect();
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