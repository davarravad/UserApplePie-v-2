<?php
namespace Modules\Messages\Controllers;

use Core\Controller,
  Core\View,
  Core\Router,
  Helpers\Auth\Auth,
  Helpers\Csrf,
  Helpers\Request,
  Helpers\Url;

// Move this to Core\Config.php
/**
*  Define Message Limit for Inbox and Outbox
*  And Message Limit per page for Paginator
*/
define('MESSAGE_QUOTA_LIMIT','50');  // Inbox and Outbox total Limit
define('MESSAGE_PAGEINATOR_LIMIT','10');  // How many message to display per page

class Messages extends Controller{

	private $model;
  private $pages;

	public function __construct(){
		parent::__construct();
		$this->model = new \Modules\Messages\Models\Messages();
    $this->pages = new \Helpers\Paginator(MESSAGE_PAGEINATOR_LIMIT, 'p');
	}

	public function routes(){
    Router::any('Messages', 'Modules\Messages\Controllers\Messages@messages');
		Router::any('ViewMessage/(:any)', 'Modules\Messages\Controllers\Messages@view');
		Router::any('MessagesInbox', 'Modules\Messages\Controllers\Messages@inbox');
		Router::any('MessagesOutbox', 'Modules\Messages\Controllers\Messages@outbox');
		Router::any('NewMessage', 'Modules\Messages\Controllers\Messages@newmessage');
    Router::any('NewMessage/(:any)', 'Modules\Messages\Controllers\Messages@newmessage');
	}

  // Inbox - Displays all
	public function messages(){

    // Check if user is logged in
		if($this->auth->isLoggedIn()){
			// Get Current User's ID
			$u_id = $this->auth->user_info();
		}else{
      Url::redirect();
    }

		// Collect Data for view
		$data['title'] = "My Private Messages";
		$data['welcome_message'] = "Welcome to Your Private Messages";

    // Get total unread messages count
    $data['unread_messages'] = $this->model->getUnreadMessages($u_id);

    // Get total messages count
    $data['total_messages'] = $this->model->getTotalMessages($u_id);

    // Let view know inbox is in use
    $data['inbox'] = "true";

    // Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>Messages</li>
		";
    $data['csrf_token'] = Csrf::makeToken();

    // Send data to view
		View::renderTemplate('header', $data);
    View::renderModule('Messages/views/messages_sidebar', $data);
		View::renderModule('Messages/views/messages', $data,$error,$success);
		View::renderTemplate('footer', $data);

	}

	// Inbox - Displays all
	public function inbox($page = NULL){

    // Check if user is logged in
		if($this->auth->isLoggedIn()){
			// Get Current User's ID
			$u_id = $this->auth->user_info();
		}else{
      Url::redirect();
    }

    // Hidden Auto Check to make sure that messages that are marked
    // for delete by both TO and FROM users are removed from database
    $this->model->cleanUpMessages();

    // Check to make sure user is trying to delete messages
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Get Post Data
				$actions = Request::post('actions');
				$msg_id = Request::post('msg_id');

        // Check to see if user is deleteing messages
        if($actions == "delete"){
  				// Delete selected messages from Inbox
          foreach($msg_id as $del_msg_id){
    				if($this->model->deleteMessageInbox($u_id, $del_msg_id)){
    					// Success
    					$success = "You Have Successfully Deleted Messages";
    				}else{
    					// Fail
    					$error = "Messages Delete Failed";
    				}
          }
        }
        // Check to see if user is marking messages as read
        if($actions == "mark_read"){
  				// Mark messages as read for all requested messages
          foreach($msg_id as $del_msg_id){
    				if($this->model->markReadMessageInbox($u_id, $del_msg_id)){
    					// Success
    					$success = "You Have Successfully Marked Messages as Read";
    				}else{
    					// Fail
    					$error = "Mark Messages Read Failed";
    				}
          }
        }
			}
		}

		// Collect Data for view
		$data['title'] = "My Private Messages Inbox";
		$data['welcome_message'] = "Welcome to Your Private Messages Inbox";

    // Sets "by" username display
    $data['tofrom'] = " by ";

    // Get all message that are to current user
    $data['messages'] = $this->model->getInbox($u_id, $this->pages->getLimit());

    // Set total number of messages for paginator
    $total_num_messages = $this->model->getTotalMessages($u_id);
    $this->pages->setTotal($total_num_messages);
    // Send page links to view
    $data['pageLinks'] = $this->pages->pageLinks();

    // Message Quota Goods
    // Get total count of messages
    $data['quota_msg_ttl'] = $total_num_messages;
    $data['quota_msg_limit'] = MESSAGE_QUOTA_LIMIT;
    $data['quota_msg_percentage'] = $this->model->getPercentage($data['quota_msg_ttl'], $data['quota_msg_limit']);

    // Check to see if user has reached message limit, if so show warning
    if($data['quota_msg_percentage'] >= "100"){
      $error = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                  <b>Your Inbox is Full!</b>  Other Site Members Can NOT send you any messages!";
    }else if($data['quota_msg_percentage'] >= "80"){
      $error = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                  <b>Warning!</b> Your Inbox is Almost Full!";
    }

    // Let view know inbox is in use
    $data['inbox'] = "true";
    // What box are we showing
    $data['what_box'] = "Inbox";

    // Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."Messages'>Messages</a></li>
			<li class='active'>".$data['title']."</li>
		";
    $data['csrf_token'] = Csrf::makeToken();

    // Include Java Script for check all feature
    $data['js'] = "<script src='".Url::templatePath()."js/form_check_all.js'></script>";

    // Send data to view
		View::renderTemplate('header', $data);
    View::renderModule('Messages/views/messages_sidebar', $data);
		View::renderModule('Messages/views/messages_list', $data,$error,$success);
		View::renderTemplate('footer', $data);

	}

  // Outbox - Displays all
	public function outbox(){

    // Check if user is logged in
		if($this->auth->isLoggedIn()){
			// Get Current User's ID
			$u_id = $this->auth->user_info();
		}else{
      Url::redirect();
    }

    // Check to make sure user is trying to delete messages
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Get Post Data
				$actions = Request::post('actions');
				$msg_id = Request::post('msg_id');

        // Check to see if user is deleteing messages
        if($actions == "delete"){
  				// Delete selected messages from Outbox
          foreach($msg_id as $del_msg_id){
    				if($this->model->deleteMessageOutbox($u_id, $del_msg_id)){
    					// Success
    					$success = "You Have Successfully Deleted Messages";
    				}else{
    					// Fail
    					$error = "Messages Delete Failed";
    				}
          }
        }
			}
		}

		// Collect Data for view
		$data['title'] = "My Private Messages Outbox";
		$data['welcome_message'] = "Welcome to your Private Messages Outbox";

    // Sets "to" username display
    $data['tofrom'] = " to ";

    // Get all message that are to current user
    $data['messages'] = $this->model->getOutbox($u_id, $this->pages->getLimit());

    // Set total number of messages for paginator
    $total_num_messages = $this->model->getTotalMessagesOutbox($u_id);
    $this->pages->setTotal($total_num_messages);
    // Send page links to view
    $data['pageLinks'] = $this->pages->pageLinks();

    // Message Quota Goods
    // Get total count of messages
    $data['quota_msg_ttl'] = count($data['messages']);
    $data['quota_msg_limit'] = MESSAGE_QUOTA_LIMIT;
    $data['quota_msg_percentage'] = $this->model->getPercentage($data['quota_msg_ttl'], $data['quota_msg_limit']);

    // Check to see if user has reached message limit, if so show warning
    if($data['quota_msg_percentage'] >= "100"){
      $error[] = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                  <b>Your Outbox is Full!</b>  You Can NOT send any messages!";
    }else if($data['quota_msg_percentage'] >= "80"){
      $error[] = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                  <b>Warning!</b> Your Outbox is Almost Full!";
    }

    // What box are we showing
    $data['what_box'] = "Outbox";

    // Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."Messages'>Messages</a></li>
			<li class='active'>".$data['title']."</li>
		";
    $data['csrf_token'] = Csrf::makeToken();

    // Include Java Script for check all feature
    $data['js'] = "<script src='".Url::templatePath()."js/form_check_all.js'></script>";

    // Send data to view
		View::renderTemplate('header', $data);
    View::renderModule('Messages/views/messages_sidebar', $data);
		View::renderModule('Messages/views/messages_list', $data,$error,$success);
		View::renderTemplate('footer', $data);

	}

  // View Message - Displays requested message
	public function view($m_id){

    // Check if user is logged in
		if($this->auth->isLoggedIn()){
			// Get Current User's ID
			$u_id = $this->auth->user_info();
		}else{
      Url::redirect();
    }

    // Check to see if requested message exists and user is related to it
    if($this->model->checkMessagePerm($u_id, $m_id)){
      // Message exist and user is related
  		// Collect Data for view
  		$data['title'] = "My Private Message";
  		$data['welcome_message'] = "Welcome to Your Private Message";

      // Get requested message data
      $data['message'] = $this->model->getMessage($m_id, $u_id);
    }else{
      // User Does not own message or it does not exist
      $data['title'] = "My Private Message - Error!";
      $data['welcome_message'] = "The requested private message does not exist!";
      $data['msg_error'] = "true";
    }
    // Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."Messages'>Messages</a></li>
			<li class='active'>".$data['title']."</li>
		";

    // Send data to view
		View::renderTemplate('header', $data);
    View::renderModule('Messages/views/messages_sidebar', $data);
		View::renderModule('Messages/views/message_display', $data);
		View::renderTemplate('footer', $data);

	}

  // New Message - Displays form to create a new message or reply
	public function newmessage($to_user = NULL){

    // Check if user is logged in
		if($this->auth->isLoggedIn()){
			// Get Current User's ID
			$u_id = $this->auth->user_info();
		}else{
      Url::redirect();
    }

    // Check to see if user is over quota
    // Disable New Message Form is they are
    if($this->model->checkMessageQuota($u_id)){
      // user is over limit, disable new message form
      $data['hide_form'] = "true";
      $error[] = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                  <b>Your Outbox is Full!</b>  You Can NOT send any messages!";
    }

		// Check to make sure user is trying to send new message
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid()) {
				// Get data from post
				$to_username = Request::post('to_username');
				$subject = Request::post('subject');
				$content = Request::post('content');

        // Get the userID of to username
        $to_userID = $this->model->getUserIDFromUsername($to_username);
        // Check to make sure user exists in Database
        if(isset($to_userID)){
          // Check to see if to user's inbox is not full
          if($this->model->checkMessageQuotaToUser($to_userID)){
    				// Run the Activation script
    				if($this->model->sendmessage($to_userID, $u_id, $subject, $content)){
    					// Success
    					$success[] = "You Have Successfully Sent a Private Message";
              $data['hide_form'] = "true";
    				}else{
    					// Fail
    					$error[] = "Message Send Failed";
    				}
          }else{
            // To user's inbox is full.  Let sender know message was not sent
            $error[] = "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                        <b>${to_username}&#39;s Inbox is Full!</b>  Sorry, Message was NOT sent!";
            // Auto Fill form to make things eaiser for user
            $data['subject'] = Request::post('subject');
            $data['content'] = Request::post('content');
          }
        }else{
          // User does not exist
          $error[] = "Message Send Failed - To User Does Not Exist";
          // Auto Fill form to make things eaiser for user
          $data['subject'] = Request::post('subject');
          $data['content'] = Request::post('content');
        }
			}
		}

		// Collect Data for view
		$data['title'] = "My Private Message";
		$data['welcome_message'] = "Welcome to Your Private Message Creator";
	  $data['csrf_token'] = Csrf::makeToken();

    // Check to see if username is in url or post
    if(isset($to_user)){
      $data['to_username'] = $to_user;
    }else{
      $data['to_username'] = Request::post('to_username');
    }

    // Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li><a href='".DIR."Messages'>Messages</a></li>
			<li class='active'>".$data['title']."</li>
		";

    // Get requested message data
    //$data['message'] = $this->model->getMessage($m_id);

    // Send data to view
		View::renderTemplate('header', $data);
    View::renderModule('Messages/views/messages_sidebar', $data);
		View::renderModule('Messages/views/message_new', $data,$error,$success);
		View::renderTemplate('footer', $data);

	}

}
