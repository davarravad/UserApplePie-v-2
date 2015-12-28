<?php
/**
 * Success class 
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Core;

use Core\Controller;
use Core\View;

/**
 * Success class to generate 404 pages.
 */
class Success extends Controller
{
    /**
     * $success holder.
     *
     * @var string
     */
    private $success = null;

    /**
     * Save success to $this->success.
     *
     * @param string $success
     */
    public function __construct($success)
    {
        parent::__construct();
        $this->success = $success;
    }

    /**
     * Display successs.
     *
     * @param  array  $success an success of successs
     * @param  string $class name of class to apply to div
     *
     * @return string return the successs inside divs
     */
    public static function display($success, $class = 'alert alert-success')
    {
        if (is_array($success)) {
            foreach ($success as $success) {
                $row.= "<div class='$class'>$success</div>";
            }
            return $row;
        } else {
            if (isset($success)) {
                return "<div class='$class'>$success</div>";
            }
        }
    }
}
