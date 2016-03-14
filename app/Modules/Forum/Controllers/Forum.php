<?php
/**
 * Forum controller
 *
 * @author David "DaVaR" Sargent - davar@thedavar.net
 * @version 2.0
 * @date Jan 13, 2016
 * @date updated Mar 5, 2016
 */

namespace Modules\Forum\Controllers;

use Core\Controller,
  Core\View,
  Core\Router,
  Helpers\Auth\Auth,
  Helpers\Csrf,
  Helpers\Request,
  Helpers\Url,
  Helpers\SuccessHelper,
  Helpers\ErrorHelper,
  Helpers\PageViews,
  Helpers\Sweets,
  Helpers\SimpleImage;

  class Forum extends Controller{

  	private $model;
    private $pagesTopic;
    private $pagesReply;
    private $forum_on_off;
    private $forum_title;
    private $forum_description;
    private $forum_topic_limit;
    private $forum_topic_reply_limit;

  	public function __construct(){
  		parent::__construct();
  		$this->model = new \Modules\Forum\Models\Forum();
      // Get data for global forum settings
      $this->forum_on_off = $this->model->globalForumSetting('forum_on_off');
      $this->forum_title = $this->model->globalForumSetting('forum_title');
      $this->forum_description = $this->model->globalForumSetting('forum_description');
      $this->forum_topic_limit = $this->model->globalForumSetting('forum_topic_limit');
      $this->forum_topic_reply_limit = $this->model->globalForumSetting('forum_topic_reply_limit');
      $this->pagesTopic = new \Helpers\Paginator($this->forum_topic_limit);
      $this->pagesReply = new \Helpers\Paginator($this->forum_topic_reply_limit);
  	}

  	public function routes(){
      // Check to make sure Forum is Enabled, otherwise hide it
      if($this->forum_on_off == 'Enabled'){
        Router::any('Forum', 'Modules\Forum\Controllers\Forum@forum');
        Router::any('Topics/(:num)', 'Modules\Forum\Controllers\Forum@topics');
    		Router::any('Topics/(:num)/(:num)', 'Modules\Forum\Controllers\Forum@topics');
        Router::any('Topic/(:num)', 'Modules\Forum\Controllers\Forum@topic');
    		Router::any('Topic/(:num)/(:num)', 'Modules\Forum\Controllers\Forum@topic');
        Router::any('NewTopic/(:num)', 'Modules\Forum\Controllers\Forum@newtopic');
      }
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
  		$data['title'] = $this->forum_title;
  		$data['welcome_message'] = $this->forum_description;

      // Get list of all forum categories
      $data['forum_categories'] = $this->model->forum_categories();

      // Get list of all forum categories
      $data['forum_titles'] = $this->model->forum_titles();

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
  			<li class='active'>".$this->forum_title."</li>
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
      $data['forum_topics'] = $this->model->forum_topics($id, $this->pagesTopic->getLimit($current_page, $this->forum_topic_limit));

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
        <li><a href='".DIR."Forum'>".$this->forum_title."</a></li>
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
      $data['topic_id'] = $id;
      $data['title'] = $this->model->topic_title($id);
      $data['topic_creator'] = $this->model->topic_creator($id);
      $data['topic_date'] = $this->model->topic_date($id);
      $data['topic_content'] = $this->model->topic_content($id);
      $data['topic_edit_date'] = $this->model->topic_edit_date($id);
      $data['topic_status'] = $this->model->topic_status($id);
      $data['topic_allow'] = $this->model->topic_allow($id);

      // Get hidden information if there is any
      $data['hidden_userID'] = $this->model->topic_hidden_userID($id);
      $data['hidden_reason'] = $this->model->topic_hidden_reason($id);
      $data['hidden_timestamp'] = $this->model->topic_hidden_timestamp($id);

      // Check to see if current user owns the origianal post
      $data['current_userID'] = $u_id;
      $data['topic_userID'] = $this->model->topic_userID($id);

      // Get current page number
      if($current_page > 1){
        $data['current_page'] = $current_page;
      }

      // Check to see if current user is admin
      $data['is_admin'] = $this->auth->checkIsAdmin($u_id);

      // Check to see if current user is moderator
      $data['is_mod'] = $this->auth->checkIsMod($u_id);

      // Check to see if current user is a new user
      $data['is_new_user'] = $this->auth->checkIsNewUser($u_id);

      // Get replys that are related to Requested Topic
      $data['topic_replys'] = $this->model->forum_topic_replys($id, $this->pagesReply->getLimit($current_page, $this->forum_topic_reply_limit));

      // Check to see if user has posted on this topic
      $data['checkUserPosted'] = $this->model->checkUserPosted($id, $u_id);

      // If user has not yet posted, then we set subcribe to true for new posts
      if($data['checkUserPosted'] == true){
        // Check to see if current user is subscribed to this topic
        $data['is_user_subscribed'] = $this->model->checkTopicSubscribe($id, $u_id);
      }else{
        $data['is_user_subscribed'] = true;
      }

      // Set total number of messages for paginator
      $total_num_replys = $this->model->getTotalReplys($id);
      $this->pagesReply->setTotal($total_num_replys);

      // Send page links to view
      $pageFormat = DIR."Topic/$id/"; // URL page where pages are
      $data['pageLinks'] = $this->pagesReply->pageLinks($pageFormat, null, $current_page);

      // Get related images if any
      $data['forum_topic_images'] = $this->model->getForumImagesTopic($id);

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
    				$data['forum_content'] = strip_tags(Request::post('forum_content'));
            $data['forum_title'] = strip_tags(Request::post('forum_title'));
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
    				$data['fpr_content'] = strip_tags(Request::post('fpr_content'));
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
    				$data['fpr_content'] = strip_tags(Request::post('fpr_content'));
              // Check to make sure user completed all required fields in form
              if(empty($data['fpr_content'])){
                // Subject field is empty
                $error[] = 'Topic Reply Content Field is Blank!';
              }
              // Check for errors before sending message
              if(count($error) == 0){
                  // No Errors, lets submit the new topic to db
          				if($this->model->sendTopicReply($u_id, $id, $topic_forum_id, $data['fpr_content'], $data['is_user_subscribed'])){
                    // Get Submitted Reply ID
                    $reply_id = $this->model->lastTopicReplyID($id);
                    // Check to see if post is going on a new page
                    $page_reply_limit = $this->forum_topic_reply_limit;
                    $redirect_page_num = ceil(($total_num_replys + 1) / $page_reply_limit);
                    // Send emails to those who are subscribed to this topic
                    $this->model->sendTopicSubscribeEmails($id, $u_id, $data['title'], $data['forum_cat'], $data['fpr_content']);

                    // Check for image upload with this topic
                    $picture = file_exists($_FILES['forumImage']['tmp_name']) || is_uploaded_file($_FILES['forumImage']['tmp_name']) ? $_FILES['forumImage'] : array ();
                      // Make sure image is being uploaded before going further
                      if(sizeof($picture)>0 && ($data['is_new_user'] != true)){
                        // Get image size
                        $check = getimagesize ( $picture['tmp_name'] );
                        // Get file size for db
                        $file_size = $picture['size'];
                        // Make sure image size is not too large
                        if($picture['size'] < 5000000 && $check && ($check['mime'] == "image/jpeg" || $check['mime'] == "image/png" || $check['mime'] == "image/gif")){
                          if(!file_exists('images/forum-pics')){
                            mkdir('images/forum-pics',0777,true);
                          }
                          // Upload the image to server
                          $image = new SimpleImage($picture['tmp_name']);
                          $new_image_name = "forum-image-topic-reply-uid{$u_id}-fid{$id}-ftid{$reply_id}";
                          $dir = 'images/forum-pics/'.$new_image_name.'.gif';
                          $image->best_fit(400,300)->save($dir);
                          $forumImage = $dir;
                          // Make sure image was Successfull
                          if($forumImage){
                            // Add new image to database
                            if($this->model->sendNewImage($u_id, $new_image_name, $dir, $file_size, $topic_forum_id, $id, $reply_id)){
                              $img_success = "<br> Image Successfully Uploaded";
                            }else{
                              $img_success = "<br> No Image Uploaded";
                            }
                          }
                        }else{
                          $img_success = "<br> Image was NOT uploaded because the file size was too large!";
                        }
                      }

          					// Success
                    SuccessHelper::push('You Have Successfully Created a New Topic Reply'.$img_success, 'Topic/'.$id.'/'.$redirect_page_num.'/#topicreply'.$reply_id);
                    $data['hide_form'] = "true";
          				}else{
          					// Fail
                    $error[] = 'New Topic Reply Create Failed';
          				}
              }// End Form Complete Check
          }else if($data['action'] == "lock_topic" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update database with topic locked (2)
            if($this->model->updateTopicLockStatus($id, "2")){
              SuccessHelper::push('You Have Successfully Locked This Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "unlock_topic" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update the database with topic unlocked (1)
            if($this->model->updateTopicLockStatus($id, "1")){
              SuccessHelper::push('You Have Successfully UnLocked This Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "hide_topic" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update database with topic hidden (TRUE)
            $hide_reason = Request::post('hide_reason');
            if($this->model->updateTopicHideStatus($id, "FALSE", $u_id, $hide_reason)){
              SuccessHelper::push('You Have Successfully Hidden This Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "unhide_topic" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update the database with topic unhide (FALSE)
            if($this->model->updateTopicHideStatus($id, "TRUE", $u_id, "UnHide")){
              SuccessHelper::push('You Have Successfully UnHide This Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "hide_reply" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update database with topic reply hidden (TRUE)
            $hide_reason = Request::post('hide_reason');
            $reply_id = Request::post('reply_id');
            $reply_url = Request::post('reply_url');
            if($this->model->updateReplyHideStatus($reply_id, "FALSE", $u_id, $hide_reason)){
              SuccessHelper::push('You Have Successfully Hidden Topic Reply', $reply_url);
            }
          }else if($data['action'] == "unhide_reply" && ($data['is_admin'] == true || $data['is_mod'] == true)){
            // Update the database with topic reply unhide (FALSE)
            $reply_id = Request::post('reply_id');
            $reply_url = Request::post('reply_url');
            if($this->model->updateReplyHideStatus($reply_id, "TRUE", $u_id, "UnHide")){
              SuccessHelper::push('You Have Successfully UnHide Topic Reply', $reply_url);
            }
          }else if($data['action'] == "subscribe" && isset($u_id)){
            // Update users topic subcrition status as true
            if($this->model->updateTopicSubcrition($id, $u_id, "true")){
              SuccessHelper::push('You Have Successfully Subscribed to this Topic', 'Topic/'.$id);
            }
          }else if($data['action'] == "unsubscribe" && isset($u_id)){
            // Update users topic subcrition status as false
            if($this->model->updateTopicSubcrition($id, $u_id, "false")){
              SuccessHelper::push('You Have Successfully UnSubscribed from this Topic', 'Topic/'.$id);
            }
          }// End Action Check
  			} // End token check
  		} // End post check

      // Update and Get Views Data
      $data['PageViews'] = PageViews::views('true', $id, 'Forum_Topic', $u_id);

      // Get Recent Posts List for Sidebar
      $data['forum_recent_posts'] = $this->model->forum_recent_posts();

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
        <li><a href='".DIR."Forum'>".$this->forum_title."</a></li>
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

      // Check to see if current user is a new user
      $data['is_new_user'] = $this->auth->checkIsNewUser($u_id);

      // Check to see if user is submitting a new topic
  		if(isset($_POST['submit'])){

  			// Check to make sure the csrf token is good
  			if (Csrf::isTokenValid()) {
  				// Get data from post
  				$data['forum_title'] = strip_tags(Request::post('forum_title'));
  				$data['forum_content'] = strip_tags(Request::post('forum_content'));

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
                $new_topic = $this->model->sendTopic($u_id, $id, $data['forum_title'], $data['forum_content']);
        				if($new_topic){
                  // New Topic Successfully Created Now Check if User is Uploading Image
                  // Check for image upload with this topic
                  $picture = file_exists($_FILES['forumImage']['tmp_name']) || is_uploaded_file($_FILES['forumImage']['tmp_name']) ? $_FILES['forumImage'] : array ();
                    // Make sure image is being uploaded before going further
                    if(sizeof($picture)>0 && ($data['is_new_user'] != true)){
                      // Get image size
                      $check = getimagesize ( $picture['tmp_name'] );
                      // Get file size for db
                      $file_size = $picture['size'];
                      // Make sure image size is not too large
                      if($picture['size'] < 5000000 && $check && ($check['mime'] == "image/jpeg" || $check['mime'] == "image/png" || $check['mime'] == "image/gif")){
                        if(!file_exists('images/forum-pics')){
                          mkdir('images/forum-pics',0777,true);
                        }
                        // Upload the image to server
                        $image = new SimpleImage($picture['tmp_name']);
                        $new_image_name = "forum-image-topic-uid{$u_id}-fid{$id}-ftid{$new_topic}";
                        $dir = 'images/forum-pics/'.$new_image_name.'.gif';
                        $image->best_fit(400,300)->save($dir);
                        $forumImage = $dir;
                        var_dump($forumImage);
                        // Make sure image was Successfull
                        if($forumImage){
                          // Add new image to database
                          if($this->model->sendNewImage($u_id, $new_image_name, $dir, $file_size, $id, $new_topic)){
                            $img_success = "<br> Image Successfully Uploaded";
                          }else{
                            $img_success = "<br> No Image Uploaded";
                          }
                        }
                      }else{
                        $img_success = "<br> Image was NOT uploaded because the file size was too large!";
                      }
                    }
        					// Success
                  SuccessHelper::push('You Have Successfully Created a New Topic'.$img_success, 'Topic/'.$new_topic);
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
        <li><a href='".DIR."Forum'>".$this->forum_title."</a></li>
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
