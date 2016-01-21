<?php namespace controllers;
use Core\View,
	Core\Controller,
	Helpers\Hooks,
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
			// Define Current User's ID for header
			$u_id = $this->auth->user_info();
			define('CUR_LOGGED_USERID', $u_id);
			$this->OnlineUsers->update($u_id);
		}else{
			define('ISLOGGEDIN', 'false');
		}
		// Run OnLine Status Checker
		$this->OnlineUsers->check();

    }
//----------------------------------------------------------------------------//
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
				$rememberme = Request::post('rememberme');

				// Check to see if user is trying to login with their email
				if($this->auth->checkIfEmail($username)){
					// User is trying to use email to login
					// Output the user's username
					$username = $this->auth->getUserNameFromEmail($username);
				}

				// Check to see if remember me is not true, then make it false
				if($rememberme != "true"){ $rememberme = "false"; }

				// Login Validation
				if($this->auth->login($username, $password, $rememberme)){
					// User Passed Validation - Let them in
					// User is good to go
					$data = array('LastLogin' => date('Y-m-d G:i:s'));
					$where = array('userID' => $this->auth->getID($username));
					$this->auth->updateUser($data,$where);

					// Make sure user is not already in users_online table
					$usr_id = $this->UserData->getUserID($username);
					$this->OnlineUsers->remove($usr_id);
					$this->OnlineUsers->add($usr_id);

					//Login Success
					//Redirect to user
					//Check to see if user came from another page within the site
					if(isset($_SESSION['login_prev_page'])){ $login_prev_page = $_SESSION['login_prev_page']; }else{ $login_prev_page = ""; }
					// Checking to see if user user was viewing anything before login
					// If they were viewing a page on this site, then after login
					// send them to that page they were on.
					if(!empty($login_prev_page)){
						//Send member to previous page
						//echo " ${login_prev_page} "; // Debug

						//Clear the prev page session if set
						if(isset($_SESSION['login_prev_page'])){
							unset($_SESSION['login_prev_page']);
						}

						// Set the redir page
						$redir_link_url = "$login_prev_page";

						// Redirect member to their post
						header("Location: $redir_link_url");
						exit;
					}else{
						//No previous page, send member to home page
						//echo " send user to home page "; // Debug

						//Clear the prev page session if set
						if(isset($_SESSION['login_prev_page'])){
							unset($_SESSION['login_prev_page']);
						}

						// Redirect member to home page
						Url::redirect();
					}
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

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		View::rendertemplate('header',$data);
		View::render('auth/Login',$data,$error);
		View::rendertemplate('footer',$data);

	}

	// Logs the user out of the system
	public function Logout(){
		// Get userID then remove user from online status
		$usr_id = $this->auth->user_info();
		$this->OnlineUsers->remove($usr_id);
		// Log the user out
		$this->auth->logout();
		Url::redirect();
	}
//----------------------------------------------------------------------------//
	// Setup the Register Page
	public function register(){
		// Check to make sure user is not already logged in
		if($this->auth->isLoggedIn()){
			Url::redirect();
		}

		// Check to make sure user is trying to login
		if(isset($_POST['submit'])){
			//Check to see if admin has added recaptcha keys to Config
			if(RECAP_PUBLIC_KEY && RECAP_PRIVATE_KEY){
				// Google Captcha Check
				if(isset($_POST['g-recaptcha-response'])){
					$captcha=$_POST['g-recaptcha-response'];
				}
				if(!$captcha){
					//What happens when the CAPTCHA was entered incorrectly
					//err_message("Sorry, The reCAPTCHA was not entered correctly! Please try again!");
					//die;
					$capcha_fail = "true";
				}
				$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".RECAP_PRIVATE_KEY."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
				if($response.'success'==false)
				{
					$capcha_fail = "true";
				}
			} else {
				$capcha_fail = "false";
			}
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				if($capcha_fail !== "true"){
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
					$error[] = "reCAPTCHA Fail.";
				}
			}else{
				// Tokens do not match
				$error[] = "Tokens Do Not Match.  Please Try Again.";
			}
		}

		$data['title'] = 'Register';
		$data['csrf_token'] = Csrf::makeToken();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		// Add JS Files requried for live checks
		$data['js'] = "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>";
		$data['js'] .= "<script src='".Url::templatePath()."js/live_email.js'></script>";
		$data['js'] .= "<script src='".Url::templatePath()."js/live_username_check.js'></script>";
		$data['js'] .= "<script src='".Url::templatePath()."js/password_strength_match.js'></script>";

		View::rendertemplate('header',$data);
		View::render('auth/Register',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		View::rendertemplate('header',$data);
		View::render('welcome/info',$data,$error);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
	// Setup the Account Settings Page
	public function AccountSettings(){
		// Check to make sure user is logged in
		if(!$this->auth->isLoggedIn()){
			Url::redirect();
		}

		// ToDo: Put information regarding what needs attention
		// For example, let user know they need to add first name
		// Or that their password has been the same for way too long

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>Account Settings</li>
		";

		$data['left_sidebar'] = $this->LeftLinks->AccountLinks();
		$data['title'] = 'Account Settings';
		$data['welcome_message'] = "Welcome to your Account Settings.  Everything related to your Account should be here. <br><br>
									Use the links on your left to select what you would like to do.  More to come!";
		View::rendertemplate('header',$data);
		View::render('auth/AccountSettings',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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
				$newpass = Request::post('password');
				$verifynewpass = Request::post('passwordc');

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

		$data['left_sidebar'] = $this->LeftLinks->AccountLinks();
		$data['title'] = 'Change Password';
		$data['csrf_token'] = Csrf::makeToken();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."AccountSettings'>Account Settings</a></li>
			<li class='active'>".$data['title']."</li>
		";

		// Add JS Files requried for live checks
		$data['js'] = "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>";
		$data['js'] .= "<script src='".Url::templatePath()."js/password_strength_match.js'></script>";

		View::rendertemplate('header',$data);
		View::render('auth/ChangePassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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
				$password = Request::post('passwordemail');
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

		$data['left_sidebar'] = $this->LeftLinks->AccountLinks();
		$data['title'] = 'Change Email';
		$data['csrf_token'] = Csrf::makeToken();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."AccountSettings'>Account Settings</a></li>
			<li class='active'>".$data['title']."</li>
		";

		// Add JS Files requried for live checks
		$data['js'] = "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>";
		$data['js'] .= "<script src='".Url::templatePath()."js/live_email.js'></script>";

		View::rendertemplate('header',$data);
		View::render('auth/ChangeEmail',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		View::rendertemplate('header',$data);
		View::render('auth/ForgotPassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		View::rendertemplate('header',$data);
		View::render('auth/ResetPassword',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}
//----------------------------------------------------------------------------//
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

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

		View::rendertemplate('header',$data);
		View::render('auth/ResendActivation',$data,$error,$success);
		View::rendertemplate('footer',$data);
	}

}
