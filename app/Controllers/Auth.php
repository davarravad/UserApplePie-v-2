<?php namespace controllers;
use Core\View,
	Core\Controller,
	Helpers\Url,
	Helpers\Csrf,
	Helpers\Request as Request;

class Auth extends Controller {
	
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->language->load('Welcome');
		
		// Define if user is logged in
		if($this->auth->isLoggedIn()){ 
			// Define if user is logged in
			define('ISLOGGEDIN', 'true'); 
			// Define Current User's UserName and ID for header
			$u_id = $this->auth->user_info();
			$u_username = $this->UserData->getUserName($u_id);
			define('CUR_USERID', $u_username);
			define('CUR_USERNAME', $u_username);
		}

    }

	// Logs the user into the system
	public function Login(){
		
		// Check to make sure user is not already logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {	
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
				}else{
					$error[] = "Incorrect UserName or Password!";
				}
			}else{
				// Tokens do not match
				$error[] = "Tokens Do Not Match.  Please Try Again.";
			}
		}
		
		$data['title'] = 'Login';
		$data['csrf_token'] = Csrf::makeToken();
		
		View::rendertemplate('header',$data);
		View::render('auth/Login',$data,$error);
		View::rendertemplate('footer',$data);
		
	}
	
	// Logs the user out of the system
	public function Logout(){
		$this->auth->logout();
		Url::redirect();
	}
	
	// Setup the Register Page
	public function register(){
		// Check to make sure user is not already logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch username an password inputs using the Request helper
				$username = Request::post('username');
				$password = Request::post('password');
				$verifypassword = Request::post('passwordc');
				$email = Request::post('email');
				
				// Run the register script
				if($this->auth->register($username, $password, $verifypassword, $email)){
					// Register ok
					if(NEW_USER_ACTIVATION == "true"){
						$success[] = "Registration Successful! Check Your Email For Activation Instructions.";
					}
					if(NEW_USER_ACTIVATION == "false"){
						$success[] = "Registration Successful! <a href='".DIR."Login'>Login</a>.";
					}					
					// Url::redirect();
				}else{
					// Register fail
					$error[] = "Registration Error!";
				}
				
			}else{
				// Tokens do not match
				$error[] = "Tokens Do Not Match.  Please Try Again.";
			}
		}
		
		$data['title'] = 'Register';
		$data['csrf_token'] = Csrf::makeToken();
		
		View::rendertemplate('header',$data);
		View::render('auth/Register',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
	// Setup the Activation Page
	public function activate(){
		// Check to make sure user is not already logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_GET['username']) && isset($_GET['key'])){
				
			// Catch username an password inputs using the Request helper
			$username = Request::get('username');
			$activekey = Request::get('key');
			
			// Run the Activation script
			if($this->auth->activateAccount($username, $activekey)){
				// Success
				$data['welcome_message'] = "Your Account Has Been Activated!  <a href='".DIR."Login'>Login</a>";
			}else{
				// Fail
				$data['welcome_message'] = "Activation Failed - Please Contact Administrator";
			}

		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = 'New Member Activation';
		View::rendertemplate('header',$data);
		View::render('welcome/info',$data,$error);
		View::rendertemplate('footer',$data);
	}
	
	// Setup the Change Password Page
	public function ChangePassword(){
		// Check to make sure user is logged in
		if(!$this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch password inputs using the Request helper
				$currpassword = Request::post('currpassword');
				$newpass = Request::post('newpass');
				$verifynewpass = Request::post('verifynewpass');
				
				// Get Current User's UserName
				$u_id = $this->auth->user_info();
				$u_username = $this->UserData->getUserName($u_id);
				
				echo "($u_username)";
				
				// Run the Activation script
				if($this->auth->changePass($u_username, $currpassword, $newpass, $verifynewpass)){
					// Success
					$success[] = "You Have Successfully Changed Your Password";
				}else{
					// Fail
					$error[] = "Password Change Failed";
				}
			}
		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = 'Change Password';
		$data['csrf_token'] = Csrf::makeToken();
		View::rendertemplate('header',$data);
		View::render('auth/ChangePassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
}