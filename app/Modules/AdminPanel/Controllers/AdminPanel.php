<?php
namespace Modules\AdminPanel\Controllers;

use Core\Controller;
use Core\View;
use Core\Router;
use Helpers\Auth\Auth;
use Helpers\Csrf;
use Helpers\Request;

class AdminPanel extends Controller{

  private $model;

  public function __construct(){
    parent::__construct();
    $this->model = new \Modules\AdminPanel\Models\AdminPanel();
  }

  public function routes(){
    Router::any('AdminPanel', 'Modules\AdminPanel\Controllers\AdminPanel@dashboard');
    Router::any('AdminPanel-Users', 'Modules\AdminPanel\Controllers\AdminPanel@users');
    Router::any('AdminPanel-User/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@user');
    Router::any('AdminPanel-Groups', 'Modules\AdminPanel\Controllers\AdminPanel@groups');
    Router::any('AdminPanel-Group/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@group');
    Router::any('AdminPanel-Forum-Settings', 'Modules\AdminPanel\Controllers\AdminPanel@forum_settings');
    Router::any('AdminPanel-Forum-Categories', 'Modules\AdminPanel\Controllers\AdminPanel@forum_categories');
  }

  public function dashboard(){
    // Get data for dashboard
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Dashboard";
    $data['welcome_message'] = "Welcom to the Admin Panel Dashboard - Stuff Coming Soon!";

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-dashboard'></i>".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/adminpanel', $data,$errors,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  public function users(){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Users";
    $data['welcome_message'] = "Welcome to the Users Admin Panel";
    $data['users_list'] = $this->model->getUsers($data['orderby']);

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/users', $data,$errors,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  public function user($id){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "User";
    $data['welcome_message'] = "Welcome to the User Admin Panel";
    $data['csrf_token'] = Csrf::makeToken();

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    // Get groups user is and is not member of
    foreach ($data_groups as $value) {
      $data_user_groups = $this->model->checkUserGroup($id, $value->groupID);
      if($data_user_groups){
        $group_member[] = $value->groupID;
      }else{
        $group_not_member[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($group_member)){
      foreach ($group_member as $value) {
        $group_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_member_groups'] = $group_member_data;
    // Gether group data for group user is not member of
    if(isset($group_not_member)){
      foreach ($group_not_member as $value) {
        $group_notmember_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_notmember_groups'] = $group_notmember_data;

    // Check to make sure admin is trying to update user profile
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
        if($_POST['update_profile'] == "true"){
  				// Catch password inputs using the Request helper
          $au_id = Request::post('au_id');
          $au_username = Request::post('au_username');
          $au_email = Request::post('au_email');
  				$au_firstName = Request::post('au_firstName');
  				$au_gender = Request::post('au_gender');
  				$au_website = Request::post('au_website');
  				$au_userImage = Request::post('au_userImage');
  				$au_aboutme = Request::post('au_aboutme');

  				// Run the update profile script
  				if($this->model->updateProfile($au_id, $au_username, $au_firstName, $au_email, $au_gender, $au_website, $au_userImage, $au_aboutme)){
  					// Success
  					$success[] = "You Have Successfully Updated User Profile";
  				}else{
  					// Fail
  					$error[] = "Profile Update Failed";
  				}
        }

        // Check to see if admin is removing user from group
        if($_POST['remove_group'] == "true"){
          // Get data from post
          $au_userID = Request::post('au_userID');
          $au_groupID = Request::post('au_groupID');
          // Updates current user's group
  				if($this->model->removeFromGroup($au_userID, $au_groupID)){
  					// Success
  					$success[] = "You Have Successfully Removed User From Group";
            \Helpers\Url::previous();
  				}else{
  					// Fail
  					$error[] = "Remove From Group Failed";
  				}
        }

        // Check to see if admin is adding user to group
        if($_POST['add_group'] == "true"){
          // Get data from post
          $au_userID = Request::post('au_userID');
          $au_groupID = Request::post('au_groupID');
          // Updates current user's group
  				if($this->model->addToGroup($au_userID, $au_groupID)){
  					// Success
  					$success[] = "You Have Successfully Added User to Group";
            \Helpers\Url::previous();
  				}else{
  					// Fail
  					$error[] = "Add to Group Failed";
  				}
        }

        // Check to see if admin wants to activate user
        if($_POST['activate_user'] == "true"){
          $au_id = Request::post('au_id');
          // Run the Activation script
  				if($this->model->activateUser($au_id)){
  					// Success
  					$success[] = "You Have Successfully Activated User";
            \Helpers\Url::previous();
  				}else{
  					// Fail
  					$error[] = "Activate User Failed";
  				}
        }

        // Check to see if admin wants to deactivate user
        if($_POST['deactivate_user'] == "true"){
          $au_id = Request::post('au_id');
          // Run the Activation script
  				if($this->model->deactivateUser($au_id)){
  					// Success
  					$success[] = "You Have Successfully Deactivated User";
            \Helpers\Url::previous();
  				}else{
  					// Fail
  					$error[] = "Deactivate User Failed";
  				}
        }

      }
		}

    // Setup Current User data
		// Get user data from user's database
		$current_user_data = $this->model->getUser($id);
		foreach($current_user_data as $user_data){
      $data['u_id'] = $id;
			$data['u_username'] = $user_data->username;
			$data['u_firstName'] = $user_data->firstName;
			$data['u_gender'] = $user_data->gender;
			$data['u_userImage'] = $user_data->userImage;
			$data['u_aboutme'] = str_replace("<br />", "", $user_data->aboutme);
			$data['u_website'] = $user_data->website;
      $data['u_email'] = $user_data->email;
      $data['u_lastlogin'] = $user_data->LastLogin;
      $data['u_signup'] = $user_data->SignUp;
      $data['u_isactive'] = $user_data->isactive;
		}

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li><a href='".DIR."AdminPanel-Users'><i class='fa fa-fw fa-user'></i> Users </a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>User - ".$data['u_username']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/user', $data,$errors,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  // Setup Groups Page
  public function groups(){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Groups";
    $data['welcome_message'] = "Welcome to the Groups Admin Panel";
    $data['groups_list'] = $this->model->getGroups($data['orderby']);
    $data['csrf_token'] = Csrf::makeToken();

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    // Check to make sure admin is trying to create group
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
        //Check for create group
        if($_POST['create_group'] == "true"){
          // Catch password inputs using the Request helper
          $ag_groupName = Request::post('ag_groupName');
//echo "$ag_groupName";
          // Run the update group script
          if($this->model->createGroup($ag_groupName)){
            // Success
            $success[] = "You Have Successfully Added Group";
            //\Helpers\Url::redirect('AdminPanel-Group/');
          }else{
            // Fail
            $error[] = "Group Add Failed";
          }
        }
      }
    }

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/groups', $data,$errors, $success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  // Setup Group Page
  public function group($id){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Group";
    $data['welcome_message'] = "Welcome to the Group Admin Panel";
    $data['csrf_token'] = Csrf::makeToken();

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    // Get groups user is and is not member of
    foreach ($data_groups as $value) {
      $data_user_groups = $this->model->checkUserGroup($id, $value->groupID);
      if($data_user_groups){
        $group_member[] = $value->groupID;
      }else{
        $group_not_member[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($group_member)){
      foreach ($group_member as $value) {
        $group_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_member_groups'] = $group_member_data;
    // Gether group data for group user is not member of
    if(isset($group_not_member)){
      foreach ($group_not_member as $value) {
        $group_notmember_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_notmember_groups'] = $group_notmember_data;

    // Check to make sure admin is trying to update group data
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
        // Check for update group
        if($_POST['update_group'] == "true"){
  				// Catch password inputs using the Request helper
          $ag_groupID = Request::post('ag_groupID');
          $ag_groupName = Request::post('ag_groupName');
          $ag_groupDescription = Request::post('ag_groupDescription');
  				$ag_groupFontColor = Request::post('ag_groupFontColor');
  				$ag_groupFontWeight = Request::post('ag_groupFontWeight');

  				// Run the update group script
  				if($this->model->updateGroup($ag_groupID, $ag_groupName, $ag_groupDescription, $ag_groupFontColor, $ag_groupFontWeight)){
  					// Success
  					$success[] = "You Have Successfully Updated Group";
  				}else{
  					// Fail
  					$error[] = "Group Update Failed";
  				}
        }
        //Check for delete group
        if($_POST['delete_group'] == "true"){
          // Catch password inputs using the Request helper
          $ag_groupID = Request::post('ag_groupID');

          // Run the update group script
          if($this->model->deleteGroup($ag_groupID)){
            // Success
            $success[] = "You Have Successfully Deleted Group";
            \Helpers\Url::redirect('AdminPanel-Groups');
          }else{
            // Fail
            $error[] = "Group Delete Failed";
          }
        }
      }
		}

    // Setup Current User data
		// Get user data from user's database
		$current_group_data = $this->model->getGroup($id);
		foreach($current_group_data as $group_data){
      $data['g_groupID'] = $group_data->groupID;
			$data['g_groupName'] = $group_data->groupName;
			$data['g_groupDescription'] = $group_data->groupDescription;
			$data['g_groupFontColor'] = $group_data->groupFontColor;
			$data['g_groupFontWeight'] = $group_data->groupFontWeight;
		}

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li><a href='".DIR."AdminPanel-Groups'><i class='fa fa-fw fa-user'></i> Groups </a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>Group - ".$data['g_groupName']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/group', $data,$errors,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  // Forum Settings Admin Panel
  public function forum_settings(){

    // Check to make sure admin is trying to update user profile
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
        // Check to see if admin is editing forum global settings
        if($_POST['update_global_settings'] == "true"){
          // Get data from post
          $forum_on_off = Request::post('forum_on_off');
          $forum_title = Request::post('forum_title');
          $forum_description = Request::post('forum_description');
          $forum_topic_limit = Request::post('forum_topic_limit');
          $forum_topic_reply_limit = Request::post('forum_topic_reply_limit');
          if($this->model->updateGlobalSettings($forum_on_off,$forum_title,$forum_description,$forum_topic_limit,$forum_topic_reply_limit)){
            // Success
            \Helpers\SuccessHelper::push('You Have Successfully Updated Forum Global Settings', 'AdminPanel-Forum-Settings');
          }else{
            $errors[] = "There was an Error Updating Forum Global Settings";
          }
        }
        // Check to see if admin is editing forum groups
        if($_POST['remove_group_user'] == "true"){
          $forum_edit_group = "users";
          $forum_edit_group_action = "remove";
        }else if($_POST['add_group_user'] == "true"){
          $forum_edit_group = "users";
          $forum_edit_group_action = "add";
        }else if($_POST['remove_group_mod'] == "true"){
          $forum_edit_group = "mods";
          $forum_edit_group_action = "remove";
        }else if($_POST['add_group_mod'] == "true"){
          $forum_edit_group = "mods";
          $forum_edit_group_action = "add";
        }else if($_POST['remove_group_admin'] == "true"){
          $forum_edit_group = "admins";
          $forum_edit_group_action = "remove";
        }else if($_POST['add_group_admin'] == "true"){
          $forum_edit_group = "admins";
          $forum_edit_group_action = "add";
        }
        if(isset($forum_edit_group) && isset($forum_edit_group_action)){
          // Get data from post
          $groupID = Request::post('groupID');
          // Updates current user's group
          if($this->model->editForumGroup($forum_edit_group, $forum_edit_group_action, $groupID)){
            // Success
            \Helpers\SuccessHelper::push('You Have Successfully Updated Forum Group ('.$forum_edit_group.')', 'AdminPanel-Forum-Settings');
          }else{
            // Fail
            $error[] = "Edit Forum Group Failed";
          }
        }
      }
    }


    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Forum Global Settings";
    $data['welcome_message'] = "Welcome to the Forum Settings Admin Panel";

    // Get data for global forum settings
    $data['forum_on_off'] = $this->model->globalForumSetting('forum_on_off');
    $data['forum_title'] = $this->model->globalForumSetting('forum_title');
    $data['forum_description'] = $this->model->globalForumSetting('forum_description');
    $data['forum_topic_limit'] = $this->model->globalForumSetting('forum_topic_limit');
    $data['forum_topic_reply_limit'] = $this->model->globalForumSetting('forum_topic_reply_limit');

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    ////////////////////////////////////////////////////////////////////////////
    // Forum Users
    // Get groups forum user is and is not member of
    foreach ($data_groups as $value) {
      $data_forum_users_groups = $this->model->checkGroupForum('users', $value->groupID);
      if($data_forum_users_groups){
        $f_users_member[] = $value->groupID;
      }else{
        $f_users_notmember[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($f_users_member)){
      foreach ($f_users_member as $value) {
        $f_users_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_users_member_groups'] = $f_users_member_data;
    // Gether group data for group user is not member of
    if(isset($f_users_notmember)){
      foreach ($f_users_notmember as $value) {
        $f_users_notmember_groups[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_users_notmember_groups'] = $f_users_notmember_groups;
    ////////////////////////////////////////////////////////////////////////////
    // Forum Mods
    // Get groups forum user is and is not member of
    foreach ($data_groups as $value) {
      $data_forum_mods_groups = $this->model->checkGroupForum('mods', $value->groupID);
      if($data_forum_mods_groups){
        $f_mods_member[] = $value->groupID;
      }else{
        $f_mods_notmember[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($f_mods_member)){
      foreach ($f_mods_member as $value) {
        $f_mods_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_mods_member_groups'] = $f_mods_member_data;
    // Gether group data for group user is not member of
    if(isset($f_mods_notmember)){
      foreach ($f_mods_notmember as $value) {
        $f_mods_notmember_groups[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_mods_notmember_groups'] = $f_mods_notmember_groups;
    ////////////////////////////////////////////////////////////////////////////
    // Forum Admins
    // Get groups forum user is and is not member of
    foreach ($data_groups as $value) {
      $data_forum_admins_groups = $this->model->checkGroupForum('admins', $value->groupID);
      if($data_forum_admins_groups){
        $f_admins_member[] = $value->groupID;
      }else{
        $f_admins_notmember[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($f_admins_member)){
      foreach ($f_admins_member as $value) {
        $f_admins_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_admins_member_groups'] = $f_admins_member_data;
    // Gether group data for group user is not member of
    if(isset($f_admins_notmember)){
      foreach ($f_admins_notmember as $value) {
        $f_admins_notmember_groups[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['f_admins_notmember_groups'] = $f_admins_notmember_groups;
    ////////////////////////////////////////////////////////////////////////////

    // Setup CSRF token
    $data['csrf_token'] = Csrf::makeToken();

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/forum_settings', $data, $errors, $success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  // Forum Categories Admin Panel
  public function forum_categories(){

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Forum Categories";
    $data['welcome_message'] = "Welcome to the Forum Categories Admin Panel";

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/forum_categories', $data, $errors, $success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

}
