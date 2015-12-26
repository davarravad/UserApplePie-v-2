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
		if($this->auth->isLoggedIn()){ define('ISLOGGEDIN', 'true'); }
    }

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = $this->language->get('welcome_text');
		
		if($this->auth->isLoggedIn()){
			$data['welcome_message'] = " You are logged in! ";
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
    public function Forum()
    {
        $data['title'] = $this->language->get('forum_text');
        $data['welcome_message'] = $this->language->get('forum_message');

        View::renderTemplate('header', $data);
        View::render('welcome/Forum', $data);
        View::renderTemplate('footer', $data);
    }
}
