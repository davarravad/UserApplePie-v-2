<?php
namespace Modules\Profile\Controllers;

use Core\Controller;
use Core\View;
use Core\Router;
use Helpers\Auth\Auth;

class Profile extends Controller{
	
	private $model;
	
	public function __construct(){
		parent::__construct();
		$this->model = new \Modules\Profile\Models\Profile();
		
		// Define if user is logged in
		if($this->auth->isLoggedIn()){ 
			// Define if user is logged in
			define('ISLOGGEDIN', 'true'); 
			// Define Current User's UserName and ID for header
			$u_id = $this->auth->user_info();
			$u_username = $this->UserData->getUserName($u_id);
			define('CUR_USERID', $u_username);
			define('CUR_USERNAME', $u_username);
			$this->OnlineUsers->update($u_id);
		}
		// Run OnLine Status Checker
		$this->OnlineUsers->check();
	}
	
	public function routes(){
		Router::any('Profile/(:any)', 'Modules\Profile\Controllers\Profile@viewprofile');
		Router::any('Profile', 'Modules\Profile\Controllers\Profile@viewprofile_error');
		Router::any('Profile_Error', 'Modules\Profile\Controllers\Profile@viewprofile_error');
		Router::any('Profile_Error/(:any)', 'Modules\Profile\Controllers\Profile@viewprofile_error');
	}
	
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
			$data['user_data'] = $this->model->user_data($id);
			$data['user_group'] = $this->UserData->getUserGroupName($u_id);
			$data['user_details'] = "Lots of information about this user and all that good stuff.";
			View::renderTemplate('header', $data);
			View::renderModule('Profile/views/profile', $data);
			View::renderTemplate('footer', $data);
		}else{
			// No profile exist so show error
			$this->viewprofile_error();
		}
	}
	
	public function viewprofile_error(){
		$data['title'] = "Member Profile Error";
		$data['error_details'] = "Ooops! The profile you are trying to view does not exist.";
		View::renderTemplate('header', $data);
		View::renderModule('Profile/views/profile_error', $data);
		View::renderTemplate('footer', $data);
	}
}