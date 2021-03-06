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
use Models\LoadPages;

/**
 * Sample controller showing a construct and 2 methods and their typical usage.
 */
class Welcome extends Controller
{

    public $LoadPages;
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        /** initialise the LoadPages object */
    		$this->LoadPages = new \Models\LoadPages();

        $this->language->load('Welcome');

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

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = $this->language->get('welcome_text');

		$data['sidebar'] = $this->RightLinks->DisplaySiteStats();

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
		$data['sidebar'] = $this->RightLinks->DisplaySiteStats();
        $data['welcome_message'] = $this->language->get('subpage_message');

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define About page title and load template files
     */
    public function About()
    {
        $data['title'] = $this->language->get('about_text');
		$data['sidebar'] = $this->RightLinks->DisplaySiteStats();
        $data['welcome_message'] = $this->language->get('about_message');

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define Members page title and load template files
     */
    public function Members()
    {
        $data['title'] = $this->language->get('members_text');
		$data['sidebar'] = $this->RightLinks->DisplaySiteStats();
        $data['welcome_message'] = $this->language->get('members_message');
		$data['members'] = $this->UserData->getMembers();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

        View::renderTemplate('header', $data);
        View::render('welcome/members', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define Members page title and load template files
     */
    public function MembersOnline()
    {
        $data['title'] = $this->language->get('membersonline_text');
		$data['sidebar'] = $this->RightLinks->DisplaySiteStats();
        $data['welcome_message'] = $this->language->get('membersonline_message');
		$data['members'] = $this->OnlineUsers->getMembersOnline();

		// Setup Breadcrumbs
		$data['breadcrumbs'] = "
			<li><a href='".DIR."'>Home</a></li>
			<li class='active'>".$data['title']."</li>
		";

        View::renderTemplate('header', $data);
        View::render('welcome/members', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define live check email
     */
    public function LiveCheckEmail()
    {
        View::render('welcome/LiveCheckEmail', $data);
    }

    /**
     * Ready the pages data for requested page and load template files
     */
    public function pages($page_url)
    {
        $data['title'] = $this->LoadPages->getPageTitle($page_url);
        $data['welcome_message'] = $this->LoadPages->getPageContent($page_url);
        $data['sidebar'] = $this->RightLinks->DisplaySiteStats();

    		// Setup Breadcrumbs
    		$data['breadcrumbs'] = "
    			<li><a href='".DIR."'>Home</a></li>
    			<li class='active'>".$data['title']."</li>
    		";

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }

}
