<?php
namespace Modules\Forum\Controllers;

use Core\Controller,
  Core\View,
  Core\Router,
  Helpers\Auth\Auth,
  Helpers\Csrf,
  Helpers\Request,
  Helpers\Url,
  Helpers\SuccessHelper,
  Helpers\ErrorHelper;

  // Move this to Core\Config.php
  /**
  *  Define Topic Posts pages Limit for Forum
  */
  define('FORUM_TOPIC_PAGEINATOR_LIMIT','20');  // How many topics to display per page
  define('FORUM_REPLY_PAGEINATOR_LIMIT','10');  // How many topic replys to display per page

  class Forum extends Controller{

  	private $model;
    private $pagesTopic;
    private $pagesReply;

  	public function __construct(){
  		parent::__construct();
  		$this->model = new \Modules\Forum\Models\Forum();
      $this->pagesTopic = new \Helpers\Paginator(FORUM_TOPIC_PAGEINATOR_LIMIT);
      $this->pagesReply = new \Helpers\Paginator(FORUM_REPLY_PAGEINATOR_LIMIT);
  	}

  	public function routes(){
      Router::any('Forum', 'Modules\Forum\Controllers\Forum@forum');
      Router::any('Topics/(:num)', 'Modules\Forum\Controllers\Forum@topics');
  		Router::any('Topics/(:num)/(:num)', 'Modules\Forum\Controllers\Forum@topics');
      Router::any('Topic/(:num)', 'Modules\Forum\Controllers\Forum@topic');
  		Router::any('Topic/(:num)/(:num)', 'Modules\Forum\Controllers\Forum@topic');
      Router::any('NewTopic/(:num)', 'Modules\Forum\Controllers\Forum@newtopic');
  	}


    // Forume Home Page Display
    public function forum(){
      // Check if user is logged in
  		if($this->auth->isLoggedIn()){
  			// Get Current User's ID
  			$u_id = $this->auth->user_info();
  		}else{
        //Url::redirect();
      }

  		// Collect Data for view
  		$data['title'] = SITETITLE." Forum";
  		$data['welcome_message'] = "Welcome to ".SITETITLE." Forum";

      // Get list of all forum categories
      $data['forum_categories'] = $this->model->forum_categories();

      // Get list of all forum categories
      $data['forum_titles'] = $this->model->forum_titles();

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
  			<li class='active'>Forum</li>
  		";
      $data['csrf_token'] = Csrf::makeToken();

      // Send data to view
  		View::renderTemplate('header', $data);
  		View::renderModule('Forum/views/forum_home', $data,$error,$success);
      View::renderModule('Forum/views/forum_sidebar', $data);
  		View::renderTemplate('footer', $data);
    }

    // Forum Topic List Display
    public function topics($id, $current_page = null){
      // Check if user is logged in
  		if($this->auth->isLoggedIn()){
  			// Get Current User's ID
  			$u_id = $this->auth->user_info();
  		}else{
        //Url::redirect();
      }

      // Get Requested Topic's Title and Description
      $data['forum_title'] = $this->model->forum_title($id);
      $data['forum_cat'] = $this->model->forum_cat($id);
      $data['forum_cat_des'] = $this->model->forum_cat_des($id);
      $data['forum_topics'] = $this->model->forum_topics($id, $this->pagesTopic->getLimit($current_page, FORUM_TOPIC_PAGEINATOR_LIMIT));

      // Set total number of messages for paginator
      $total_num_topics = count($this->model->forum_topics($id));
      $this->pagesTopic->setTotal($total_num_topics);

      // Send page links to view
      $pageFormat = DIR."Topics/$id/"; // URL page where pages are
      $data['pageLinks'] = $this->pagesTopic->pageLinks($pageFormat, null, $current_page);

  		// Collect Data for view
  		$data['title'] = $data['forum_cat'];
  		$data['welcome_message'] = $data['forum_cat_des'];

      // Output current user's ID
      $data['current_userID'] = $u_id;

      // Output current topic ID
      $data['current_topic_id'] = $id;

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
        <li><a href='".DIR."Forum'>Forum</a></li>
  			<li class='active'>".$data['title']."</li>
  		";

      // Ready the token!
      $data['csrf_token'] = Csrf::makeToken();

      // Send data to view
      View::renderTemplate('header', $data);
      View::renderModule('Forum/views/topics', $data,$error,$success);
      View::renderModule('Forum/views/forum_sidebar', $data);
      View::renderTemplate('footer', $data);
    }

    // Forum Topic Display
    public function topic($id, $current_page = null){
      // Check if user is logged in
  		if($this->auth->isLoggedIn()){
  			// Get Current User's ID
  			$u_id = $this->auth->user_info();
  		}else{
        //Url::redirect();
      }

      // Get Cat ID for this topic
      $topic_forum_id = $this->model->forum_topic_cat_id($id);

      // Get Requested Topic's Title and Description
      $data['forum_cat'] = $this->model->forum_cat($topic_forum_id);
      $data['forum_cat_des'] = $this->model->forum_cat_des($topic_forum_id);
      $data['forum_topics'] = $this->model->forum_topics($topic_forum_id);

      // Get Requested Topic Information
      $data['title'] = $this->model->topic_title($id);
      $data['topic_creator'] = $this->model->topic_creator($id);
      $data['topic_date'] = $this->model->topic_date($id);
      $data['topic_content'] = $this->model->topic_content($id);
      $data['topic_edit_date'] = $this->model->topic_edit_date($id);
      $data['topic_status'] = $this->model->topic_status($id);

      // Check to see if current user owns the origianal post
      $data['current_userID'] = $u_id;
      $data['topic_userID'] = $this->model->topic_userID($id);

      // Check to see if current user is admin
      $data['is_admin'] = $this->auth->checkIsAdmin($u_id);

      // Get replys that are related to Requested Topic
      $data['topic_replys'] = $this->model->forum_topic_replys($id, $this->pagesReply->getLimit($current_page, FORUM_REPLY_PAGEINATOR_LIMIT));

      // Set total number of messages for paginator
      $total_num_replys = $this->model->getTotalReplys($id);
      $this->pagesReply->setTotal($total_num_replys);

      // Send page links to view
      $pageFormat = DIR."Topic/$id/"; // URL page where pages are
      $data['pageLinks'] = $this->pagesReply->pageLinks($pageFormat, null, $current_page);

      // Check to see if user is submitting a new topic reply
  		if(isset($_POST['submit'])){

  			// Check to make sure the csrf token is good
  			if (Csrf::isTokenValid()) {
          // Get Action from POST
          $data['action'] = Request::post('action');
          $data['edit_reply_id'] = Request::post('edit_reply_id');

          // Check to see if user is editing topic
          if($data['action'] == "update_topic"){
            // Get data from post
    				$data['forum_content'] = Request::post('forum_content');
            $data['forum_title'] = Request::post('forum_title');
              // Check to make sure user completed all required fields in form
              if(empty($data['forum_title'])){
                // Subject field is empty
                $error[] = 'Topic Title Field is Blank!';
              }
              if(empty($data['forum_content'])){
                // Subject field is empty
                $error[] = 'Topic Content Field is Blank!';
              }
              // Check to make sure user owns the content they are trying to edit
              // Get the id of the user that owns the post that is getting edited
              if($u_id != $this->model->getTopicOwner($id)){
                // User does not own this content
                $error[] = 'You Do Not Own The Content You Were Trying To Edit!';
              }
              // Check for errors before sending message
              if(count($error) == 0){
                  // No Errors, lets submit the new topic to db
          				if($this->model->updateTopic($id, $data['forum_title'], $data['forum_content'])){
          					// Success
                    SuccessHelper::push('You Have Successfully Updated a Topic', 'Topic/'.$id);
          				}else{
          					// Fail
                    $error[] = 'Edit Topic Failed';
          				}
              }// End Form Complete Check
          }
          // Check to see if user is editing or creating topic reply
          else if($data['action'] == "update_reply"){
            // Get data from post
    				$data['fpr_content'] = Request::post('fpr_content');
              // Check to make sure user completed all required fields in form
              if(empty($data['fpr_content'])){
                // Subject field is empty
                $error[] = 'Topic Reply Content Field is Blank!';
              }
              // Check to make sure user owns the content they are trying to edit
              // Get the id of the user that owns the post that is getting edited
              if($u_id != $this->model->getReplyOwner($data['edit_reply_id'])){
                // User does not own this content
                $error[] = 'You Do Not Own The Content You Were Trying To Edit!';
              }
              // Check for errors before sending message
              if(count($error) == 0){
                  // No Errors, lets submit the new topic to db
          				if($this->model->updateTopicReply($data['edit_reply_id'], $data['fpr_content'])){
          					// Success
                    SuccessHelper::push('You Have Successfully Updated a Topic Reply', 'Topic/'.$id.'/'.$redirect_page_num.'/#topicreply'.$data['edit_reply_id']);
          				}else{
          					// Fail
                    $error[] = 'Edit Topic Reply Failed';
          				}
              }// End Form Complete Check
          }else if($data['action'] == "new_reply"){
    				// Get data from post
    				$data['fpr_content'] = Request::post('fpr_content');
              // Check to make sure user completed all required fields in form
              if(empty($data['fpr_content'])){
                // Subject field is empty
                $error[] = 'Topic Reply Content Field is Blank!';
              }
              // Check for errors before sending message
              if(count($error) == 0){
                  // No Errors, lets submit the new topic to db
          				if($this->model->sendTopicReply($u_id, $id, $topic_forum_id, $data['fpr_content'])){
                    // Get Submitted Reply ID
                    $reply_id = $this->model->lastTopicReplyID($id);
                    // Check to see if post is going on a new page
                    $page_reply_limit = FORUM_REPLY_PAGEINATOR_LIMIT;
                    $redirect_page_num = ceil(($total_num_replys + 1) / $page_reply_limit);
          					// Success
                    SuccessHelper::push('You Have Successfully Created a New Topic Reply', 'Topic/'.$id.'/'.$redirect_page_num.'/#topicreply'.$reply_id);
                    $data['hide_form'] = "true";
          				}else{
          					// Fail
                    $error[] = 'New Topic Reply Create Failed';
          				}
              }// End Form Complete Check
          }else if($data['action'] == "lock_topic" && $data['is_admin'] == true){
            // Update database with topic locked (2)
            if($this->model->updateTopicLockStatus($id, "2")){
              SuccessHelper::push('You Have Successfully Locked This Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "unlock_topic" && $data['is_admin'] == true){
            // Update the database with topic unlocked (1)
            if($this->model->updateTopicLockStatus($id, "1")){
              SuccessHelper::push('You Have Successfully UnLocked This Topic', 'Topic/'.$id);
            }
          }// End Action Check
  			} // End token check
  		} // End post check

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
        <li><a href='".DIR."Forum'>Forum</a></li>
        <li><a href='".DIR."Topics/$topic_forum_id'>".$data['forum_cat']."</a>
  			<li class='active'>".$data['title']."</li>
  		";

      // Ready the token!
      $data['csrf_token'] = Csrf::makeToken();

      // Send data to view
      View::renderTemplate('header', $data);
      View::renderModule('Forum/views/topic', $data,$error,$success);
      View::renderModule('Forum/views/forum_sidebar', $data);
      View::renderTemplate('footer', $data);
    }

    // Forum New Topic Form Display
    public function newtopic($id){
      // Check if user is logged in
  		if($this->auth->isLoggedIn()){
  			// Get Current User's ID
  			$u_id = $this->auth->user_info();
  		}else{
        //Url::redirect();
      }

      // Output Current User's ID
      $data['current_userID'] = $u_id;

      // Get Requested Topic's Title and Description
      $data['forum_cat'] = $this->model->forum_cat($id);
      $data['forum_cat_des'] = $this->model->forum_cat_des($id);
      $data['forum_topics'] = $this->model->forum_topics($id);

      // Ouput Page Title
      $data['title'] = "New Topic for ".$data['forum_cat'];

      // Output Welcome Message
      $data['welcome_message'] = "Welcome to the new topic page.";

      // Check to see if user is submitting a new topic
  		if(isset($_POST['submit'])){

  			// Check to make sure the csrf token is good
  			if (Csrf::isTokenValid()) {
  				// Get data from post
  				$data['forum_title'] = Request::post('forum_title');
  				$data['forum_content'] = Request::post('forum_content');
            // Check to make sure user completed all required fields in form
            if(empty($data['forum_title'])){
              // Username field is empty
              $error[] = 'Topic Title Field is Blank!';
            }
            if(empty($data['forum_content'])){
              // Subject field is empty
              $error[] = 'Topic Content Field is Blank!';
            }
            // Check for errors before sending message
            if(count($error) == 0){
                // No Errors, lets submit the new topic to db
        				if($this->model->sendTopic($u_id, $id, $data['forum_title'], $data['forum_content'])){
        					// Success
                  SuccessHelper::push('You Have Successfully Created a New Topic', 'Topics/'.$id);
                  $data['hide_form'] = "true";
        				}else{
        					// Fail
                  $error[] = 'New Topic Create Failed';
        				}
            }// End Form Complete Check
  			}
  		}

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
        <li><a href='".DIR."Forum'>Forum</a></li>
        <li><a href='".DIR."Topics/$id'>".$data['forum_cat']."</a>
  			<li class='active'>".$data['title']."</li>
  		";

      // Ready the token!
      $data['csrf_token'] = Csrf::makeToken();

      // Send data to view
      View::renderTemplate('header', $data);
      View::renderModule('Forum/views/newtopic', $data,$error,$success);
      View::renderModule('Forum/views/forum_sidebar', $data);
      View::renderTemplate('footer', $data);
    }

  }
