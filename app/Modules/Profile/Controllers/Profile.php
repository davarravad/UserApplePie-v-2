<?php
namespace Modules\Profile\Controllers;

use Core\Controller;
use Core\View;
use Core\Router;
use Helpers\Auth\Auth;
use Helpers\Csrf;
use Helpers\Request;

class Profile extends Controller{

	private $model;

	public function __construct(){
		parent::__construct();
		$this->model = new \Modules\Profile\Models\Profile();

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

	public function routes(){
		Router::any('Profile/(:any)', 'Modules\Profile\Controllers\Profile@viewprofile');
		Router::any('Profile', 'Modules\Profile\Controllers\Profile@viewprofile_error');
		Router::any('Profile_Error', 'Modules\Profile\Controllers\Profile@viewprofile_error');
		Router::any('Profile_Error/(:any)', 'Modules\Profile\Controllers\Profile@viewprofile_error');
		Router::any('EditProfile', 'Modules\Profile\Controllers\Profile@profile_edit');
		Router::any('PrivacySettings', 'Modules\Profile\Controllers\Profile@privacy_settings');
	}

	// Setup data for view profile display
	public function viewprofile($id){
		// Gets user's id if they are using username to view profile
		if(!ctype_digit($id)){
			$u_id = $this->UserData->getUserID($id);
		}else{
			$u_id = $id;
		}

		// Check to see if profile exist in database
		if($this->model->profile_exist($u_id)){
			// Profile exist so display it
			$data['title'] = "Member Profile";
			$data['content'] = $id;
			$data['user_data'] = $this->model->user_data($u_id);
			$data['user_group'] = $this->UserData->getUserGroupName($u_id);
			View::renderTemplate('header', $data);
			View::renderModule('Profile/views/profile', $data);
			View::renderTemplate('footer', $data);
		}else{
			// No profile exist so show error
			$this->viewprofile_error();
		}
	}

	// Setup view profile error page
	public function viewprofile_error(){
		$data['title'] = "Member Profile Error";
		$data['error_details'] = "Ooops! The profile you are trying to view does not exist.";
		View::renderTemplate('header', $data);
		View::renderModule('Profile/views/profile_error', $data);
		View::renderTemplate('footer', $data);
	}

	// Setup view privacy settings page
	public function privacy_settings(){
		$data['csrf_token'] = Csrf::makeToken();
		$data['title'] = "Privacy Settings";
		$data['profile_content'] = "Privacy Settings Coming Soon!";
		$data['left_sidebar'] = $this->LeftLinks->AccountLinks();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."AccountSettings'>Account Settings</a></li>
			<li class='active'>".$data['title']."</li>
		";
		View::renderTemplate('header', $data);
		View::renderModule('Profile/views/privacy_settings', $data,$error,$success);
		View::renderTemplate('footer', $data);
	}

	// Setup view privacy settings page
	public function profile_edit(){
		$data['csrf_token'] = Csrf::makeToken();
		$data['title'] = "Edit Profile";
		$data['profile_content'] = "Use the following fields to update your User Profile.";
		$data['left_sidebar'] = $this->LeftLinks->AccountLinks();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."AccountSettings'>Account Settings</a></li>
			<li class='active'>".$data['title']."</li>
		";

		// Get Current User's userID
		$u_id = $this->auth->user_info();

		// Check to make sure user is trying to update profile
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Catch password inputs using the Request helper
				$firstName = Request::post('firstName');
				$gender = Request::post('gender');
				$website = Request::post('website');
				$userImage = Request::post('userImage');
				$aboutme = Request::post('aboutme');

				// Run the Activation script
				if($this->model->updateProfile($u_id, $firstName, $gender, $website, $userImage, $aboutme)){
					// Success
					$success[] = "You Have Successfully Updated Your Profile";
				}else{
					// Fail
					$error[] = "Profile Update Failed";
				}
			}
		}

		// Setup Current User data
		// Get user data from user's database
		$current_user_data = $this->model->user_data($u_id);
		foreach($current_user_data as $user_data){
			$data['u_username'] = $user_data->username;
			$data['u_firstName'] = $user_data->firstName;
			$data['u_gender'] = $user_data->gender;
			$data['u_userImage'] = $user_data->userImage;
			$data['u_aboutme'] = str_replace("<br />", "", $user_data->aboutme);
			$data['u_website'] = $user_data->website;
		}

		View::renderTemplate('header', $data);
		View::renderModule('Profile/views/profile_edit', $data,$error,$success);
		View::renderTemplate('footer', $data);
	}

}
