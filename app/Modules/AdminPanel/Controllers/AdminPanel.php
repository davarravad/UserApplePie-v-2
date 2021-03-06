<?php
/**
 * Admin Panel Controller
 *
 * @author DaVaR - davar@userapplepie.com
 * @version 1.0
 * @date Jan 17 2016
 * @updated Mar 4 2016
 */

namespace Modules\AdminPanel\Controllers;

use Core\Controller;
use Core\View;
use Core\Router;
use Helpers\Auth\Auth;
use Helpers\Csrf;
use Helpers\Request;

define('USERS_PAGEINATOR_LIMIT', '10');  // Sets up users listing page limit

class AdminPanel extends Controller{

  private $model;
  private $forum;
  private $pages;

  public function __construct(){
    parent::__construct();
    $this->model = new \Modules\AdminPanel\Models\AdminPanel();
    $this->forum = new \Modules\AdminPanel\Models\ForumAdmin();
    $this->pages = new \Helpers\Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page
  }

  public function routes(){
    Router::any('AdminPanel', 'Modules\AdminPanel\Controllers\AdminPanel@dashboard');
    Router::any('AdminPanel-Users', 'Modules\AdminPanel\Controllers\AdminPanel@users');
    Router::any('AdminPanel-Users/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@users');
    Router::any('AdminPanel-Users/(:any)/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@users');
    Router::any('AdminPanel-User/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@user');
    Router::any('AdminPanel-Groups', 'Modules\AdminPanel\Controllers\AdminPanel@groups');
    Router::any('AdminPanel-Group/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@group');
    Router::any('AdminPanel-Forum-Settings', 'Modules\AdminPanel\Controllers\AdminPanel@forum_settings');
    Router::any('AdminPanel-Forum-Categories', 'Modules\AdminPanel\Controllers\AdminPanel@forum_categories');
    Router::any('AdminPanel-Forum-Categories/(:any)/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@forum_categories');
    Router::any('AdminPanel-Forum-Categories/(:any)/(:any)/(:any)', 'Modules\AdminPanel\Controllers\AdminPanel@forum_categories');
    Router::any('AdminPanel-Forum-Blocked-Content', 'Modules\AdminPanel\Controllers\AdminPanel@forum_blocked');
  }

  public function dashboard(){
    // Get data for dashboard
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Dashboard";
    $data['welcome_message'] = "Welcom to the Admin Panel Dashboard - Stuff Coming Soon!";

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-dashboard'></i> ".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/adminpanel', $data,$error,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  public function users($set_order_by = 'ID-ASC', $current_page = '1'){

    // Check for orderby selection
    $data['orderby'] = $set_order_by;

    // Set total number of rows for paginator
    $total_num_users = $this->model->getTotalUsers();
    $this->pages->setTotal($total_num_users);

    // Send page links to view
    $pageFormat = DIR."AdminPanel-Users/$set_order_by/"; // URL page where pages are
    $data['pageLinks'] = $this->pages->pageLinks($pageFormat, null, $current_page);
    $data['current_page_num'] = $current_page;

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Users";
    $data['welcome_message'] = "Welcome to the Users Admin Panel";
    $data['users_list'] = $this->model->getUsers($data['orderby'], $this->pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/users', $data,$error,$success);
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
            \Helpers\SuccessHelper::push('You Have Successfully Updated User Profile', 'AdminPanel-User/'.$au_id);
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
            \Helpers\SuccessHelper::push('You Have Successfully Removed User From Group', 'AdminPanel-User/'.$au_userID);
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
            \Helpers\SuccessHelper::push('You Have Successfully Added User to Group', 'AdminPanel-User/'.$au_userID);
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
            \Helpers\SuccessHelper::push('You Have Successfully Activated User', 'AdminPanel-User/'.$au_id);
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
            \Helpers\SuccessHelper::push('You Have Successfully Deactivated User', 'AdminPanel-User/'.$au_id);
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
    View::renderModule('AdminPanel/views/user', $data,$error,$success);
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

          // Run the update group script
          $new_group_id = $this->model->createGroup($ag_groupName);
          if($new_group_id){
            // Success
            \Helpers\SuccessHelper::push('You Have Successfully Added Group', 'AdminPanel-Group/'.$new_group_id);
          }else{
            // Fail
            $error[] = "Group Add Failed";
          }
        }
      }
    }

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/groups', $data,$error, $success);
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
            \Helpers\SuccessHelper::push('You Have Successfully Updated a Group', 'AdminPanel-Group/'.$ag_groupID);
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
            \Helpers\SuccessHelper::push('You Have Successfully Deleted a Group', 'AdminPanel-Groups');
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
    View::renderModule('AdminPanel/views/group', $data,$error,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  /**
  * forum_settings
  *
  * Function that handles all the Admin Functions for Forum Settings
  */
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
          // Run Forum Settings Update
          if($this->forum->updateGlobalSettings($forum_on_off,$forum_title,$forum_description,$forum_topic_limit,$forum_topic_reply_limit)){
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
          if($this->forum->editForumGroup($forum_edit_group, $forum_edit_group_action, $groupID)){
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
    $data['forum_on_off'] = $this->forum->globalForumSetting('forum_on_off');
    $data['forum_title'] = $this->forum->globalForumSetting('forum_title');
    $data['forum_description'] = $this->forum->globalForumSetting('forum_description');
    $data['forum_topic_limit'] = $this->forum->globalForumSetting('forum_topic_limit');
    $data['forum_topic_reply_limit'] = $this->forum->globalForumSetting('forum_topic_reply_limit');

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    ////////////////////////////////////////////////////////////////////////////
    // Forum Users
    // Get groups forum user is and is not member of
    foreach ($data_groups as $value) {
      $data_forum_users_groups = $this->forum->checkGroupForum('users', $value->groupID);
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
      $data_forum_mods_groups = $this->forum->checkGroupForum('mods', $value->groupID);
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
      $data_forum_admins_groups = $this->forum->checkGroupForum('admins', $value->groupID);
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
      <li class='active'><i class='glyphicon glyphicon-cog'></i> ".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/forum_settings', $data, $error, $success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  /**
  * forum_categories
  *
  * Function that handles all the Admin Functions for Forum Categories
  *
  * @param string $action - action to take within function
  * @param int/string
  * @param int/string
  *
  */
  public function forum_categories($action = null, $id = null, $id2 = null){
    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Forum Categories";

    // Check to see if there is an action
    if($action != null && $id != null){
      // Check to see if action is edit
      if($action == 'CatMainEdit'){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            if($_POST['action'] == "update_cat_main_title"){
              // Catch password inputs using the Request helper
              $new_forum_title = Request::post('forum_title');
              $prev_forum_title = Request::post('prev_forum_title');
              if($this->forum->updateCatMainTitle($prev_forum_title,$new_forum_title)){
                // Success
                \Helpers\SuccessHelper::push('You Have Successfully Updated Forum Main Category Title to <b>'.$new_forum_title.'</b>', 'AdminPanel-Forum-Categories');
              }else{
                // Fail
                $error[] = "Edit Forum Main Category Failed";
              }
            }
          }
        }else{
          // Get data for CatMainEdit Form
          $data['edit_cat_main'] = true;
          $data['data_cat_main'] = $this->forum->getCatMain($id);

          $data['welcome_message'] = "You are about to Edit Selected Forum Main Category.";

          // Setup Breadcrumbs
          $data['breadcrumbs'] = "
            <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</a></li>
            <li class='active'><i class='glyphicon glyphicon-pencil'></i> Edit Main Category</li>
          ";
        }
      }else if($action == "CatMainUp"){
        if($this->forum->moveUpCatMain($id)){
          // Success
          \Helpers\SuccessHelper::push('You Have Successfully Moved Up Forum Main Category', 'AdminPanel-Forum-Categories');
        }else{
          // Fail
          $error[] = "Move Up Forum Main Category Failed";
        }
      }else if($action == "CatMainDown"){
        if($this->forum->moveDownCatMain($id)){
          // Success
          \Helpers\SuccessHelper::push('You Have Successfully Moved Down Forum Main Category', 'AdminPanel-Forum-Categories');
        }else{
          // Fail
          $error[] = "Move Down Forum Main Category Failed";
        }
      }else if($action == 'CatMainNew'){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            // Add new cate main title to database
            if($_POST['action'] == "new_cat_main_title"){
              // Catch inputs using the Request helper
              $forum_title = Request::post('forum_title');
              // Get last order title number from db
              $last_order_num = $this->forum->getLastCatMain();
              // Attempt to add new Main Category Title to DB
              if($this->forum->newCatMainTitle($forum_title,'forum',$last_order_num)){
                // Success
                \Helpers\SuccessHelper::push('You Have Successfully Created New Forum Main Category Title <b>'.$new_forum_title.'</b>', 'AdminPanel-Forum-Categories');
              }else{
                // Fail
                $error[] = "New Forum Main Category Failed";
              }
            }
          }
        }
      }else if($action == "CatSubList"){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            // Add new cate main title to database
            if($_POST['action'] == "new_cat_sub"){
              // Catch inputs using the Request helper
              $forum_title = Request::post('forum_title');
              $forum_cat = Request::post('forum_cat');
              $forum_des = Request::post('forum_des');
              // Check to see if we are adding to a new main cat
              if($this->forum->checkSubCat($forum_title)){
                // Get last cat sub order id
                $last_cat_order_id = $this->forum->getLastCatSub($forum_title);
                // Get forum order title id
                $forum_order_title = $this->forum->getForumOrderTitle($forum_title);
                // Run insert for new sub cat
                $run_sub_cat = $this->forum->newSubCat($forum_title,$forum_cat,$forum_des,$last_cat_order_id,$forum_order_title);
              }else{
                // Run update for new main cat
                $run_sub_cat = $this->forum->updateSubCat($id,$forum_cat,$forum_des);
              }
              // Attempt to update/insert sub cat in db
              if($run_sub_cat){
                // Success
                \Helpers\SuccessHelper::push('You Have Successfully Created Forum Sub Category', 'AdminPanel-Forum-Categories/CatSubList/'.$id);
              }else{
                // Fail
                $error[] = "Create Forum Sub Category Failed";
              }
            }
          }
        }else{
          // Set goods for Forum Sub Categories Listing
          $data['cat_sub_list'] = true;
          $data['cat_main_title'] = $this->forum->getCatMain($id);
          $data['cat_sub_titles'] = $this->forum->getCatSubs($data['cat_main_title']);
          $data['fourm_cat_sub_last'] = $this->forum->getLastCatSub($data['cat_main_title']);

          $data['welcome_message'] = "You are viewing a complete list of sub categories for requeted main category.";

          // Setup Breadcrumbs
          $data['breadcrumbs'] = "
            <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</a></li>
            <li class='active'><i class='glyphicon glyphicon-pencil'></i> Sub Categories List</li>
          ";
        }
      }else if($action == "CatSubEdit"){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            // Add new cate main title to database
            if($_POST['action'] == "edit_cat_sub"){
              // Catch inputs using the Request helper
              $forum_cat = Request::post('forum_cat');
              $forum_des = Request::post('forum_des');
              // Attempt to update sub cat in db
              if($this->forum->updateSubCat($id,$forum_cat,$forum_des)){
                // Success
                \Helpers\SuccessHelper::push('You Have Successfully Updated Forum Sub Category', 'AdminPanel-Forum-Categories/CatSubList/'.$id);
              }else{
                // Fail
                $error[] = "Update Forum Sub Category Failed";
              }
            }
          }
        }else{
          // Display Edit Forum for Selected Sub Cat
          $data['cat_sub_edit'] = true;
          $data['cat_sub_data'] = $this->forum->getCatSubData($id);

          $data['welcome_message'] = "You are about to edit requeted sub category.";

          // Setup Breadcrumbs
          $data['breadcrumbs'] = "
            <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories/CatSubList/$id'><i class='glyphicon glyphicon-list'></i> Sub Categories List</a></li>
            <li class='active'><i class='glyphicon glyphicon-pencil'></i> Edit Sub Category</li>
          ";
        }
      }else if($action == "DeleteSubCat"){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            // Add new cate main title to database
            if($_POST['action'] == "delete_cat_sub"){
              // Catch inputs using the Request helper
              $delete_cat_sub_action = Request::post('delete_cat_sub_action');

              // Get title basted on forum_id
              $forum_title = $this->forum->getCatMain($id);

              // Get title basted on forum_cat
              $forum_cat = $this->forum->getCatSub($id);

              // Check to see what delete function admin has selected
              if($delete_cat_sub_action == "delete_all"){
                // Admin wants to delete Sub Cat and Everything Within it
                // First we delete all related topic Replies
                if($this->forum->deleteTopicsForumID($id)){
                  $success_count = $success_count + 1;
                }
                // Second we delete all topics
                if($this->forum->deleteTopicRepliesForumID($id)){
                  $success_count = $success_count + 1;
                }
                // Finally we delete the main cat and all related sub cats
                if($this->forum->deleteCatForumID($id)){
                  $success_count = $success_count + 1;
                }
                // Check to see if everything was deleted Successfully
                if($success_count > 0){
                  // Success
                  \Helpers\SuccessHelper::push('You Have Successfully Deleted Sub Category: <b>'.$forum_title.' > '.$forum_cat.'</b> and Everything Within it!', 'AdminPanel-Forum-Categories');
                }
              }else{
                // Extract forum_id from move_to_# string
                $forum_id = str_replace("move_to_", "", $delete_cat_sub_action);
                if(!empty($forum_id)){
                  // First Update Topic Replies forum_id
                  if($this->forum->updateTopicRepliesForumID($id, $forum_id)){
                    $success_count = $success_count + 1;
                  }
                  // Second Update Topics forum_id
                  if($this->forum->updateTopicsForumID($id, $forum_id)){
                    $success_count = $success_count + 1;
                  }
                  // Last delete the sub Category
                  if($this->forum->deleteCatForumID($id)){
                    $success_count = $success_count + 1;
                  }
                  // Check to see if anything was done
                  if($success_count > 0){
                    // Success
                    \Helpers\SuccessHelper::push('You Have Successfully Moved Main Category From <b>'.$old_forum_title.'</b> to <b>'.$new_forum_title.'</b>', 'AdminPanel-Forum-Categories/CatSubList/'.$forum_id);
                  }
                }else{
                  // User has not selected to delete or move main cat
                  \Helpers\ErrorHelper::push('No Action Selected.  No actions executed.', 'AdminPanel-Forum-Categories/DeleteSubCat/'.$id);
                }
              }

            }
          }
        }else{
          // Display Delete Cat Sub Form
          $data['delete_cat_sub'] = true;

          // Get list of all sub cats except current
          $data['list_all_cat_sub'] = $this->forum->catSubListExceptSel($id);

          // Setup Breadcrumbs
          $data['breadcrumbs'] = "
            <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories/CatSubList/".$id."'><i class='glyphicon glyphicon-list'></i> Sub Categories List</a></li>
            <li class='active'><i class='glyphicon glyphicon-pencil'></i> Delete Sub Category</li>
          ";
        }
      }else if($action == "CatSubUp"){
        // Get forum_title for cat
        $data['cat_main_title'] = $this->forum->getCatMain($id);
        // Try to move up
        if($this->forum->moveUpCatSub($data['cat_main_title'],$id2)){
          // Success
          \Helpers\SuccessHelper::push('You Have Successfully Moved Up Forum Sub Category', 'AdminPanel-Forum-Categories/CatSubList/'.$id);
        }else{
          // Fail
          $error[] = "Move Up Forum Main Category Failed";
        }
      }else if($action == "CatSubDown"){
        // Get forum_title for cat
        $data['cat_main_title'] = $this->forum->getCatMain($id);
        // Try to move down
        if($this->forum->moveDownCatSub($data['cat_main_title'],$id2)){
          // Success
          \Helpers\SuccessHelper::push('You Have Successfully Moved Down Forum Sub Category', 'AdminPanel-Forum-Categories/CatSubList/'.$id);
        }else{
          // Fail
          $error[] = "Move Down Forum Main Category Failed";
        }
      }else if($action == "DeleteMainCat"){
        // Check to make sure admin is trying to update
        if(isset($_POST['submit'])){
          // Check to make sure the csrf token is good
          if (Csrf::isTokenValid()) {
            // Add new cate main title to database
            if($_POST['action'] == "delete_cat_main"){
              // Catch inputs using the Request helper
              $delete_cat_main_action = Request::post('delete_cat_main_action');

              // Get title basted on forum_id
              $forum_title = $this->forum->getCatMain($id);

              // Check to see what delete function admin has selected
              if($delete_cat_main_action == "delete_all"){
                // Admin wants to delete Main Cat and Everything Within it
                // Get list of all forum_id's for this Main Cat
                $forum_id_all = $this->forum->getAllForumTitleIDs($forum_title);
                $success_count = "0";
                if(isset($forum_id_all)){
                  foreach ($forum_id_all as $row) {
                    // First we delete all related topic Replies
                    if($this->forum->deleteTopicsForumID($row->forum_id)){
                      $success_count = $success_count + 1;
                    }
                    // Second we delete all topics
                    if($this->forum->deleteTopicRepliesForumID($row->forum_id)){
                      $success_count = $success_count + 1;
                    }
                    // Finally we delete the main cat and all related sub cats
                    if($this->forum->deleteCatForumID($row->forum_id)){
                      $success_count = $success_count + 1;
                    }
                  }
                }
                if($success_count > 0){
                  // Success
                  \Helpers\SuccessHelper::push('You Have Successfully Deleted Main Category: <b>'.$forum_title.'</b> and Everything Within it!', 'AdminPanel-Forum-Categories');
                }
              }else{
                // Extract forum_id from move_to_# string
                $forum_id = str_replace("move_to_", "", $delete_cat_main_action);
                if(!empty($forum_id)){
                  // Get new and old forum titles
                  $new_forum_title = $this->forum->getCatMain($forum_id);
                  $old_forum_title = $this->forum->getCatMain($id);
                  // Get forum_order_title id for forum_title we are moving to
                  $new_forum_order_title = $this->forum->getForumOrderTitle($new_forum_title);
                  // Get last order id for new forum_title we are moving to
                  $new_forum_order_cat = $this->forum->getLastCatSub($new_forum_title);
                  // Update with the new forum title from the old one
                  if($this->forum->moveForumSubCat($old_forum_title,$new_forum_title,$new_forum_order_title,$new_forum_order_cat)){
                    // Success
                    \Helpers\SuccessHelper::push('You Have Successfully Moved Main Category From <b>'.$old_forum_title.'</b> to <b>'.$new_forum_title.'</b>', 'AdminPanel-Forum-Categories/CatSubList/'.$forum_id);
                  }
                }else{
                  // User has not selected to delete or move main cat
                  \Helpers\ErrorHelper::push('No Action Selected.  No actions executed.', 'AdminPanel-Forum-Categories/DeleteMainCat/'.$id);
                }
              }

            }
          }
        }else{
          // Show delete options for main cat
          $data['delete_cat_main'] = true;
          $data['welcome_message'] = "You are about to delete requested main category.  Please proceed with caution.";
          // Get title for main cat admin is about to delete
          $data['delete_cat_main_title'] = $this->forum->getCatMain($id);
          // Get all other main cat titles
          $data['list_all_cat_main'] = $this->forum->catMainListExceptSel($data['delete_cat_main_title']);

          // Setup Breadcrumbs
          $data['breadcrumbs'] = "
            <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
            <li><a href='".DIR."AdminPanel-Forum-Categories'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</a></li>
            <li class='active'><i class='glyphicon glyphicon-pencil'></i> Delete Main Category</li>
          ";
        }
      }
    }else{
      // Get data for main categories
      $data['cat_main'] = $this->forum->catMainList();

      // Welcome message
      $data['welcome_message'] = "You are viewing a complete list of main categories.";

      // Setup Breadcrumbs
      $data['breadcrumbs'] = "
        <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
        <li class='active'><i class='glyphicon glyphicon-list'></i> ".$data['title']."</li>
      ";
    }

    // Get Last main cat order number
    $data['fourm_cat_main_last'] = $this->forum->getLastCatMain();

    // Setup CSRF token
    $data['csrf_token'] = Csrf::makeToken();

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/forum_categories', $data, $error, $success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

  public function forum_blocked(){
    // Get data for dashboard
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Forum Blocked Content";
    $data['welcome_message'] = "Welcom to the Admin Panel Blocked Content Listing!";

    // Get list of blocked topics
    $data['blocked_topics'] = $this->forum->getBlockedTopics();

    // Get list of blocked topic replies
    $data['blocked_replies'] = $this->forum->getBlockedReplies();

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li><a href='".DIR."AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a></li>
      <li class='active'><i class='glyphicon glyphicon-remove-sign'></i> ".$data['title']."</li>
    ";

    View::renderModule('AdminPanel/views/header', $data);
    View::renderModule('AdminPanel/views/forum_blocked', $data,$error,$success);
    View::renderModule('AdminPanel/views/footer', $data);
  }

}
