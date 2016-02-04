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

  class Forum extends Controller{

  	private $model;
    private $pages;

  	public function __construct(){
  		parent::__construct();
  		$this->model = new \Modules\Forum\Models\Forum();
      $this->pages = new \Helpers\Paginator(MESSAGE_PAGEINATOR_LIMIT);
  	}

  	public function routes(){
      Router::any('Forum', 'Modules\Forum\Controllers\Forum@forum');
  		//Router::any('ViewMessage/(:any)', 'Modules\Messages\Controllers\Messages@view');
  		//Router::any('MessagesInbox', 'Modules\Messages\Controllers\Messages@inbox');
      //Router::any('MessagesInbox/(:any)', 'Modules\Messages\Controllers\Messages@inbox');
  		//Router::any('MessagesOutbox', 'Modules\Messages\Controllers\Messages@outbox');
      //Router::any('MessagesOutbox/(:any)', 'Modules\Messages\Controllers\Messages@outbox');
  		//Router::any('NewMessage', 'Modules\Messages\Controllers\Messages@newmessage');
      //Router::any('NewMessage/(:any)', 'Modules\Messages\Controllers\Messages@newmessage');
  	}

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

      // Setup Breadcrumbs
  		$data['breadcrumbs'] = "
  			<li><a href='".DIR."'>Home</a></li>
  			<li class='active'>Forum</li>
  		";
      $data['csrf_token'] = Csrf::makeToken();

      // Send data to view
  		View::renderTemplate('header', $data);
      View::renderModule('Forum/views/forum_sidebar', $data);
  		View::renderModule('Forum/views/forum_home', $data,$error,$success);
  		View::renderTemplate('footer', $data);
    }

  }
