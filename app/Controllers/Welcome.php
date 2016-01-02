<?php
/**
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Controllers;

use Core\View;
use Core\Controller;
use Helpers\Url;
use Helpers\Auth\Auth;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends Controller
{

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->language->load('Welcome');
		
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

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = $this->language->get('welcome_text');
		
		if($this->auth->isLoggedIn()){
			// Get current user's information
			$u_id = $this->auth->user_info();
			$u_username = $this->UserData->getUserName($u_id);
			$u_email = $this->UserData->getUserEmail($u_id);
			$u_lastlogin = date("F d, Y",strtotime($this->UserData->getUserLastLogin($u_id)));
			$u_signup = date("F d, Y",strtotime($this->UserData->getUserSignUp($u_id)));
			$data['user_group'] = $this->UserData->getUserGroupName($u_id);
			// Setup the output data
			$page_data = " 
				You are logged in! <br><br>
				Cookie userID: $u_id <br>
				UserName: $u_username <br>
				Email: $u_email <Br>
				Last Login Date: $u_lastlogin <Br>
				Sign Up Date: $u_signup <br>
			";
			$data['welcome_message'] = $page_data;
		}else{
			$data['welcome_message'] = $this->language->get('welcome_message');
		}
		
        View::renderTemplate('header', $data);
        View::render('welcome/welcome', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define Subpage page title and load template files
     */
    public function subPage()
    {
        $data['title'] = $this->language->get('subpage_text');
        $data['welcome_message'] = $this->language->get('subpage_message');

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }
	
    /**
     * Define Forum page title and load template files
     */
    public function About()
    {
        $data['title'] = $this->language->get('about_text');
        $data['welcome_message'] = $this->language->get('about_message');

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }
	
    /**
     * Define live check email
     */
    public function LiveCheckEmail()
    {
        View::render('welcome/LiveCheckEmail', $data);
    }
}
