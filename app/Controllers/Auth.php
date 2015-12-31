<?php namespace controllers;
use Core\View,
	Core\Controller,
	Helpers\Url,
	Helpers\Csrf,
	Core\Language,
	Helpers\Request as Request;

class Auth extends Controller {
	
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->language->load('Auth');
		
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
	
	// Setup the Change Email Page
	public function ChangeEmail(){
		// Check to make sure user is logged in
		if(!$this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Get Current User's userID
		$u_id = $this->auth->user_info();
		$data['email'] = $this->UserData->getUserEmail($u_id);
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch password inputs using the Request helper
				$password = Request::post('password');
				$newemail = Request::post('newemail');
				
				// Run the Activation script
				if($this->auth->changeEmail($u_id, $password, $newemail)){
					// Success
					$success[] = "You Have Successfully Changed Your Email";
				}else{
					// Fail
					$error[] = "Email Change Failed";
				}
			}
		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = 'Change Email';
		$data['csrf_token'] = Csrf::makeToken();
		View::rendertemplate('header',$data);
		View::render('auth/ChangeEmail',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
	// Setup the Forgot Password Page
	public function ForgotPassword(){
		// Check to make sure user is NOT logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch email input using the Request helper
				$email = Request::post('email');
				
				// Run the Activation script
				if($this->auth->resetPass($email)){
					// Success
					$success[] = Language::show('success_msg_forgot_pass', 'Auth');
				}else{
					// Fail
					$error[] = Language::show('error_msg_forgot_pass', 'Auth');
				}
			}
		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = Language::show('title_forgot_password', 'Auth');
		$data['csrf_token'] = Csrf::makeToken();
		View::rendertemplate('header',$data);
		View::render('auth/ForgotPassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
	// Setup the Forgot Password Page
	public function ResetPassword(){
		// Check to make sure user is NOT logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
		// Catch username and resetkey inputs using the Request helper
		$username = Request::query('username');
		$resetkey = Request::query('key');
		// Check to make sure user is trying to login
		if(isset($username) && isset($resetkey)){
			// Check to make sure UserName and ResetKey are good
			if($this->auth->checkResetKey($username, $resetkey)){
				// If good we show the form then check to see if new password has been submitted
				if(isset($_POST['submit'])){
					// Check to make sure the csrf token is good
					if (Csrf::isTokenValid()) {
						// Catch password inputs using the Request helper
						$password = Request::post('password');
						$confirm_password = Request::post('confirm_password');
						// Everything looks good, lets go ahead and reset the password
						if($this->auth->resetPass('', $username, $resetkey, $password, $confirm_password)){
							$success = "Your Password has ben reset!";
						}else{
							$error = "Password Reset Fail!";
						}   
					}
				}
			}else{
				$error[] = "Password Reset Fail!";
			}
		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = Language::show('title_forgot_password', 'Auth');
		$data['csrf_token'] = Csrf::makeToken();
		View::rendertemplate('header',$data);
		View::render('auth/ResetPassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
	// Setup the Resend Activation Page
	public function ResendActivation(){
		// Check to make sure user is NOT logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}
			
		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch email input using the Request helper
				$email = Request::post('email');
				
				// Run the Activation script
				if($this->auth->resendActivation($email)){
					// Success
					$success[] = Language::show('success_msg_resend_activation', 'Auth');
				}else{
					// Fail
					$error[] = Language::show('error_msg_resend_activation', 'Auth');
				}
			}
		}else{
			// No GET information - Send User to index
			//Url::redirect();
		}
		
		$data['title'] = Language::show('title_resend_activation', 'Auth');
		$data['csrf_token'] = Csrf::makeToken();
		View::rendertemplate('header',$data);
		View::render('auth/ResendActivation',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
	
}