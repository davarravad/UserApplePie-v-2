<?php
/**
 * Controller - base controller
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Core;

use Core\View;
use Core\Language;
use Helpers\Auth\Auth;
use Helpers\PageFunctions;

/**
 * Core controller, all other controllers extend this base controller.
 */
abstract class Controller
{
    /**
     * View variable to use the view class.
     *
     * @var string
     */
    public $view;

    /**
     * Language variable to use the languages class.
     *
     * @var string
     */
    public $language;

    /**
     * Auth variable to use the Auth class.
     *
     * @var string
     */
    public $auth;
	
    /**
     * UserData variable to use the UserData class.
     *
     * @var string
     */
    public $UserData;
	
    /**
     * PageFunctions variable to use the PageFunctions class.
     *
     * @var string
     */
    public $PageFunctions;
	
    /**
     * RightLinks variable to use the RightLinks and LeftLinks classes.
     *
     * @var string
     */
    public $RightLinks;
	public $LeftLinks;
	
    /**
     * OnlineUsers variable to use the OnlineUsers class.
     *
     * @var string
     */
    public $OnlineUsers;
	
    /**
     * On run make an instance of the config class and view class.
     */
    public function __construct()
    {
        /** initialise the views object */
        $this->view = new View();

        /** initialise the language object */
        $this->language = new Language();
		
        /** initialise the auth object */
        $this->auth = new Auth();
		
		/** initialise the UserData object */
		$this->UserData = new \Models\UserData();
		
		/** initialise the UserData object */
		$this->PageFunctions = new PageFunctions();
		
		/** initialise the RightLinks object */
		$this->RightLinks = new \Models\RightLinks();
		
		/** initialise the RightLinks object */
		$this->LeftLinks = new \Models\LeftLinks();
		
		/** initialise the OnlineUserData object */
		$this->OnlineUsers = new \Models\OnlineUsers();
    }
}
