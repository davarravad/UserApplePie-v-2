<?php

/**
 * Auth class for simple mvc framework
 * @version 1.6
 * @author Jhobanny Morillo <geomorillo@yahoo.com>
 */

namespace Helpers\Auth;

use Helpers\Database;
use Helpers\Cookie;

class Auth {

    protected $db;
    public $error;
    public $success;
    public $lang;

    public function __construct() {
        new \Helpers\Auth\Setup(); // loads Setup
        $this->lang = include 'Lang.php'; //language file messages
        $this->db = Database::get();
        $this->expireAttempt(); //expire attempts
    }

    /**
     * Log user in via MySQL Database 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username, $password) {
		
        if (!Cookie::get("auth_cookie")) {
			
            $attcount = $this->getAttempt($_SERVER['REMOTE_ADDR']);

            if ($attcount[0]->count >= MAX_ATTEMPTS) {
                $error[] = $this->lang['login_lockedout'];
                $error[] = sprintf($this->lang['login_wait'], WAIT_TIME);
                return false;
            } else { 
                // Input verification :
                if (strlen($username) == 0) {
                    $error[] = $this->lang['login_username_empty'];
                    return false;
                } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
                    $error[] = $this->lang['login_username_long'];
                    return false;
                } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
                    $error[] = $this->lang['login_username_short'];
                    return false;
                } elseif (strlen($password) == 0) {
                    $error[] = $this->lang['login_password_empty'];
                    return false;
                } elseif (strlen($password) > MAX_PASSWORD_LENGTH) {
                    $error[] = $this->lang['login_password_long'];
                    return false;
                } elseif (strlen($password) < MIN_PASSWORD_LENGTH) {
                    $error[] = $this->lang['login_password_short'];
                    return false;
                } else {
                    // Input is valid
                    $query = $this->db->select('SELECT isactive,password FROM '.PREFIX.'users WHERE username=:username', array(':username' => $username));
                    $count = count($query);
                    $hashed_db_password = $query[0]->password;
                    $verify_password = password_verify($password, $hashed_db_password);
                    if ($count == 0 || $verify_password == 0) {
                        // Username or password are wrong
                        $error[] = $this->lang['login_incorrect'];
                        $this->addAttempt($_SERVER['REMOTE_ADDR']);
                        $attcount[0]->count = $attcount[0]->count + 1;
                        $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                        $this->logActivity("UNKNOWN", "AUTH_LOGIN_FAIL", "Username / Password incorrect - {$username} / {$password}");
                        $error[] = sprintf($this->lang['login_attempts_remaining'], $remaincount);
                        return false;
                    } else {
                        // Username and password are correct
                        if ($query[0]->isactive == "0") {
                            // Account is not activated
                            $this->logActivity($username, "AUTH_LOGIN_FAIL", "Account inactive");
                            $error[] = $this->lang['login_account_inactive'];
                            return false;
                        } else {
                            // Account is activated
                            $this->newCookie($username); //generate new cookie cookie
                            $this->logActivity($username, "AUTH_LOGIN_SUCCESS", "User logged in");
                            $this->success[] = $this->lang['login_success'];
                            return true;
                        }
                    }
                }
            }
        } else {
            // User is already logged in
            $error[] = $this->lang['login_already']; // Is an user already logged in an error?
            return true; // its true because is logged in if not the function would not allow to log in
        }
    }

    /**
     * Logs out an user, deletes all cookies and destroys the cookies 
     */
    public function logout() {
        $auth_cookie = Cookie::get("auth_cookie");
        if ($auth_cookie != '') {
            $this->deleteCookie($auth_cookie);
        }
    }

    /**
     * Checks if current user is logged or not 
     * @return boolean
     */
    public function isLoggedIn() {
        $auth_cookie = Cookie::get("auth_cookie"); //get hash from browser
        //check if cookie is valid
        if ($auth_cookie != '' && $this->cookieIsValid($auth_cookie)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Provides an associateve array with current user's info 
     * @return array 
     */
    public function currentCookieInfo() {
        if ($this->isLoggedIn()) {
            $auth_cookie = Cookie::get("auth_cookie"); //get hash from browser
            return $this->cookieInfo($auth_cookie);
        }
    }

    /**
     * Provides an associative array of user info based on cookie hash 
     * @param string $hash
     * @return array $cookie
     */
    private function cookieInfo($hash) {
        $query = $this->db->select("SELECT uid, username, expiredate, ip FROM ".PREFIX."sessions WHERE hash=:hash", array(':hash' => $hash));
        $count = count($query);
        if ($count == 0) {
            // Hash doesn't exist
            $error[] = $this->lang['cookieinfo_invalid'];
            //setcookie("auth_cookie", $hash, time() - 3600, '/');
            Cookie::destroy('auth_cookie', $hash); //check if destroys deletes only a specific hash
            //   \Helpers\Cookie::set("auth_cookie", $hash, time() - 3600, "/",$_SERVER["HTTP_HOST"]);
            return false;
        } else {
            // Hash exists
            $cookie["uid"] = $query[0]->uid;
            $cookie["username"] = $query[0]->username;
            $cookie["expiredate"] = $query[0]->expiredate;
            $cookie["ip"] = $query[0]->ip;
            return $cookie;
        }
    }

    /**
     * Checks if a hash cookie is valid on database 
     * @param string $hash
     * @return boolean
     */
    private function cookieIsValid($hash) {
        //if hash in db
        $sql = "SELECT username, expiredate, ip FROM ".PREFIX."sessions WHERE hash=:hash";
        $cookie = $this->db->select($sql, array(":hash" => $hash));
        $count = count($cookie);
        if ($count == 0) {
            //hash did not exists deleting cookie
            Cookie::destroy("auth_cookie", $hash);
            //Cookie::destroy("auth_cookie", $hash, '');
            //setcookie("auth_cookie", $hash, time() - 3600, "/");
            $this->logActivity('UNKNOWN', "AUTH_CHECKCOOKIE", "User cookie cookie deleted - Hash ({$hash}) didn't exist");
            return false;
        } else {
            $username = $cookie[0]->username;
            $db_expiredate = $cookie[0]->expiredate;
            $db_ip = $cookie[0]->ip;
            if ($_SERVER['REMOTE_ADDR'] != $db_ip) {
                //hash exists but ip is changed, delete cookie and hash
                $this->db->delete(PREFIX.'sessions', array('username' => $username));
                Cookie::destroy("auth_cookie", $hash);
                //setcookie("auth_cookie", $hash, time() - 3600, "/");
                $this->logActivity($username, "AUTH_CHECKCOOKIE", "User cookie cookie deleted - IP Different ( DB : {$db_ip} / Current : " . $_SERVER['REMOTE_ADDR'] . " )");
                return false;
            } else {
                $expiredate = strtotime($db_expiredate);
                $currentdate = strtotime(date("Y-m-d H:i:s"));
                if ($currentdate > $expiredate) {
                    //cookie has expired delete cookie and cookies
                    $this->db->delete(PREFIX.'sessions', array('username' => $username));
                    Cookie::destroy("auth_cookie", $hash);
                    //setcookie("auth_cookie", $hash, time() - 3600, "/");
                    $this->logActivity($username, "AUTH_CHECKCOOKIE", "User cookie cookie deleted - Cookie expired ( Expire date : {$db_expiredate} )");
                } else {
                    //all ok
                    return true;
                }
            }
        }
    }

    /**
     * Provides amount of attempts already in database based on user's IP 
     * @param string $ip
     * @return int $attempt_count
     */
    private function getAttempt($ip) {
        $attempt_count = $this->db->select("SELECT count FROM ".PREFIX."attempts WHERE ip=:ip", array(':ip' => $ip));
        $count = count($attempt_count);

        if ($count == 0) {
            $attempt_count[0] = new \stdClass();
            $attempt_count[0]->count = 0;
        }
        return $attempt_count;
    }

    /*
     * Adds a new attempt to database based on user's IP 
     * @param string $ip
     */

    private function addAttempt($ip) {
        $query_attempt = $this->db->select("SELECT count FROM ".PREFIX."attempts WHERE ip=:ip", array(':ip' => $ip));
        $count = count($query_attempt);
        $attempt_expiredate = date("Y-m-d H:i:s", strtotime(SECURITY_DURATION));
        if ($count == 0) {
            // No record of this IP in attempts table already exists, create new
            $attempt_count = 1;
            $this->db->insert(PREFIX.'attempts', array('ip' => $ip, 'count' => $attempt_count, 'expiredate' => $attempt_expiredate));
        } else {
            // IP Already exists in attempts table, add 1 to current count
            $attempt_count = intval($query_attempt[0]->count) + 1;
            $this->db->update(PREFIX.'attempts', array('count' => $attempt_count, 'expiredate' => $attempt_expiredate), array('ip' => $ip));
        }
    }

    /**
     * Used to remove expired attempt logs from database 
     * (Currently used on __construct but need more testing)
     */
    private function expireAttempt() {
        $query_attempts = $this->db->select("SELECT ip, expiredate FROM ".PREFIX."attempts");
        $count = count($query_attempts);
        $curr_time = strtotime(date("Y-m-d H:i:s"));
        if ($count != 0) {
            foreach ($query_attempts as $attempt) {
                $attempt_expiredate = strtotime($attempt->expiredate);
                if ($attempt_expiredate <= $curr_time) {
                    $where = array('ip' => $attempt->ip);
                    $this->db->delete(PREFIX.'attempts', $where);
                }
            }
        }
    }

    /**
     * Creates a new cookie for the provided username and sets cookie 
     * @param string $username
     */
    private function newCookie($username) {
        $hash = md5(microtime()); // unique cookie hash
        // Fetch User ID :		
        $queryUid = $this->db->select("SELECT userID FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
        $uid = $queryUid[0]->userID;
        // Delete all previous cookies :
        $this->db->delete(PREFIX.'sessions', array('username' => $username));
        $ip = $_SERVER['REMOTE_ADDR'];
        $expiredate = date("Y-m-d H:i:s", strtotime(SESSION_DURATION));
        $expiretime = strtotime($expiredate);
        $this->db->insert(PREFIX.'sessions', array('uid' => $uid, 'username' => $username, 'hash' => $hash, 'expiredate' => $expiredate, 'ip' => $ip));
        Cookie::set('auth_cookie', $hash, $expiretime, "/", FALSE);
    }

    /**
     * Deletes a cookie based on a hash 
     * @param string $hash
     */
    private function deleteCookie($hash) {

        $query_username = $this->db->select('SELECT username FROM '.PREFIX.'sessions WHERE hash=:hash', array(':hash' => $hash));
        $count = count($query_username);
        if ($count == 0) {
            // Hash doesn't exist
            $this->logActivity("UNKNOWN", "AUTH_LOGOUT", "User cookie cookie deleted - Database cookie not deleted - Hash ({$hash}) didn't exist");
            $error[] = $this->lang['deletecookie_invalid'];
        } else {
            $username = $query_username[0]->username;
            // Hash exists, Delete all cookies for that username :
            $this->db->delete(PREFIX.'sessions', array('username' => $username));
            $this->logActivity($username, "AUTH_LOGOUT", "User cookie cookie deleted - Database cookie deleted - Hash ({$hash})");
            //setcookie("auth_cookie", $hash, time() - 3600, "/");
            Cookie::destroy("auth_cookie", $hash);
        }
    }

    /**
     * Register a new user into the database 
     * @param string $username
     * @param string $password
     * @param string $verifypassword
     * @param string $email
     * @return boolean
     */
    public function register($username, $password, $verifypassword, $email) {
        if (!Cookie::get('auth_cookie')) {
            // Input Verification :
            if (strlen($username) == 0) {
                $error[] = $this->lang['register_username_empty'];
            } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
                $error[] = $this->lang['register_username_long'];
            } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
                $error[] = $this->lang['register_username_short'];
            }
            if (strlen($password) == 0) {
                $error[] = $this->lang['register_password_empty'];
            } elseif (strlen($password) > MAX_PASSWORD_LENGTH) {
                $error[] = $this->lang['register_password_long'];
            } elseif (strlen($password) < MIN_PASSWORD_LENGTH) {
                $error[] = $this->lang['register_password_short'];
            } elseif ($password !== $verifypassword) {
                $error[] = $this->lang['register_password_nomatch'];
            } elseif (strstr($password, $username)) {
                $error[] = $this->lang['register_password_username'];
            }
            if (strlen($email) == 0) {
                $error[] = $this->lang['register_email_empty'];
            } elseif (strlen($email) > MAX_EMAIL_LENGTH) {
                $error[] = $this->lang['register_email_long'];
            } elseif (strlen($email) < MIN_EMAIL_LENGTH) {
                $error[] = $this->lang['register_email_short'];
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error[] = $this->lang['register_email_invalid'];
            }
            if (count($this->error) == 0) {
                // Input is valid
                $query = $this->db->select("SELECT * FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
                $count = count($query);
                if ($count != 0) {
                    // Username already exists
                    $this->logActivity("UNKNOWN", "AUTH_REGISTER_FAIL", "Username ({$username}) already exists");
                    $error[] = $this->lang['register_username_exist'];
                    return false;
                } else {
                    // Username is not taken 
                    $query = $this->db->select('SELECT * FROM '.PREFIX.'users WHERE email=:email', array(':email' => $email));
                    $count = count($query);
                    if ($count != 0) {
                        // Email address is already used
                        $this->logActivity("UNKNOWN", "AUTH_REGISTER_FAIL", "Email ({$email}) already exists");
                        $error[] = $this->lang['register_email_exist'];
                        return false;
                    } else {
						// Check to see if user has to activate their account or not
						if(NEW_USER_ACTIVATION == "true"){
							// Site is set for new members to activate their account by email
							// Email address isn't already used
							$password = $this->hashPass($password);
							$activekey = $this->randomKey(RANDOM_KEY_LENGTH);
							$this->db->insert(PREFIX.'users', array('username' => $username, 'password' => $password, 'email' => $email, 'activekey' => $activekey));
							//EMAIL MESSAGE USING PHPMAILER
							$mail = new \Helpers\PhpMailer\Mail();
							$mail->setFrom(EMAIL_FROM);
							$mail->addAddress($email);
							$mail->subject(SITE_NAME);
							$body = "Hello {$username}<br/><br/>";
							$body .= "You recently registered a new account on " . SITE_NAME . "<br/>";
							$body .= "To activate your account please click the following link<br/><br/>";
							$body .= "<b><a href=\"" . BASE_URL . ACTIVATION_ROUTE . "?username={$username}&key={$activekey}\">Activate my account</a></b>";
							$body .= "<br><br> You May Copy and Paste this URL in your Browser Address Bar: <br>";
							$body .= " " . BASE_URL . ACTIVATION_ROUTE . "?username={$username}&key={$activekey}";
							$mail->body($body);
							$mail->send();
							$this->logActivity($username, "AUTH_REGISTER_SUCCESS", "Account created and activation email sent");
							$this->success[] = $this->lang['register_success'];
							return true;
						}
						if(NEW_USER_ACTIVATION == "false"){
							// Site is set to let new members register without email activation
							$password = $this->hashPass($password);
							$activekey = $this->randomKey(RANDOM_KEY_LENGTH);
							$this->db->insert(PREFIX.'users', array('username' => $username, 'password' => $password, 'email' => $email, 'isactive' => '1'));
							$this->logActivity($username, "AUTH_REGISTER_SUCCESS", "Account created and activation email sent");
							$this->success[] = $this->lang['register_success'];
							return true;
						}
                    }
                }
            } else {
                //some error 
                return false;
            }
        } else {
            // User is logged in
            $error[] = $this->lang['register_email_loggedin'];
            return false;
        }
    }

    /**
     * Activates an account 
     * @param string $username
     * @param string $key
     */
    public function activateAccount($username, $key) {
        // check lengst of keys and username strings since this can be directly called
        //  if current account is active dont activate 

		// Get Data from Database for requested user
        $query_active = $this->db->select("SELECT isactive,activekey FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
        $db_isactive = $query_active[0]->isactive;
		$db_key = $query_active[0]->activekey;
		
		// Check to see if Keys Match Account and user is not already active
		if(isset($username) && $db_isactive == "0" && $key == $db_key){
			$this->db->update(PREFIX.'users', array('isactive' => 1, 'activekey' => 0), array('username' => $username));
			$this->logActivity($username, "AUTH_ACTIVATE_SUCCESS", "Activation successful. Key Entry deleted.");
			$this->success[] = $this->lang['activate_success'];
			return true;
		}else{
			return false;
		}

    }

    /**
     * Logs users actions on the site to database for future viewing 
     * @param string $username
     * @param string $action
     * @param string $additionalinfo
     * @return boolean
     */
    public function logActivity($username, $action, $additionalinfo = "none") {
        if (strlen($username) == 0) {
            $username = "GUEST";
        } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
            $error[] = $this->lang['logactivity_username_short'];
            return false;
        } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
            $error[] = $this->lang['logactivity_username_long'];
            return false;
        }
        if (strlen($action) == 0) {
            $error[] = $this->lang['logactivity_action_empty'];
            return false;
        } elseif (strlen($action) < 3) {
            $error[] = $this->lang['logactivity_action_short'];
            return false;
        } elseif (strlen($action) > 100) {
            $error[] = $this->lang['logactivity_action_long'];
            return false;
        }
        if (strlen($additionalinfo) == 0) {
            $additionalinfo = "none";
        } elseif (strlen($additionalinfo) > 500) {
            $error[] = $this->lang['logactivity_addinfo_long'];
            return false;
        }
        if (count($this->error) == 0) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $date = date("Y-m-d H:i:s");
            $this->db->insert(PREFIX.'activitylog', array('date' => $date, 'username' => $username, 'action' => $action, 'additionalinfo' => $additionalinfo, 'ip' => $ip));
            return true;
        }
    }

    /**
     * Hash user's password with PHP built in function ! 
     * @param string $password
     * @return string $hashed_password
     */
    private function hashPass($password) {
		// Hash that password
        $hashed_password = password_hash("$password", PASSWORD_DEFAULT);
        return $hashed_password;
    }

    /**
     * Returns a random string, length can be modified 
     * @param int $length
     * @return string $key
     */
    private function randomKey($length = 10) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars{rand(0, strlen($chars) - 1)};
        }
        return $key;
    }

    /**
     * Changes a user's password, providing the current password is known 
     * @param string $username
     * @param string $currpass
     * @param string $newpass
     * @param string $verifynewpass
     * @return boolean
     */
    function changePass($username, $currpass, $newpass, $verifynewpass) {
        if (strlen($username) == 0) {
            $error[] = $this->lang['changepass_username_empty'];
        } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
            $error[] = $this->lang['changepass_username_long'];
        } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
            $error[] = $this->lang['changepass_username_short'];
        }
        if (strlen($currpass) == 0) {
            $error[] = $this->lang['changepass_currpass_empty'];
        } elseif (strlen($currpass) < MIN_PASSWORD_LENGTH) {
            $error[] = $this->lang['changepass_currpass_short'];
        } elseif (strlen($currpass) > MAX_PASSWORD_LENGTH) {
            $error[] = $this->lang['changepass_currpass_long'];
        }
        if (strlen($newpass) == 0) {
            $error[] = $this->lang['changepass_newpass_empty'];
        } elseif (strlen($newpass) < MIN_PASSWORD_LENGTH) {
            $error[] = $this->lang['changepass_newpass_short'];
        } elseif (strlen($newpass) > MAX_PASSWORD_LENGTH) {
            $error[] = $this->lang['changepass_newpass_long'];
        } elseif (strstr($newpass, $username)) {
            $error[] = $this->lang['changepass_password_username'];
        } elseif ($newpass !== $verifynewpass) {
            $error[] = $this->lang['changepass_password_nomatch'];
        }
        if (count($this->error) == 0) {
            //$currpass = $this->hashPass($currpass);
            $newpass = $this->hashPass($newpass);
            $query = $this->db->select("SELECT password FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
            $count = count($query);
            if ($count == 0) {
                $this->logActivity("UNKNOWN", "AUTH_CHANGEPASS_FAIL", "Username Incorrect ({$username})");
                $error[] = $this->lang['changepass_username_incorrect'];
                return false;
            } else {
                $db_currpass = $query[0]->password;
                $verify_password = \Helpers\Password::verify($currpass, $db_currpass);
                if ($verify_password) {
                    $this->db->update(PREFIX.'users', array('password' => $newpass), array('username' => $username));
                    $this->logActivity($username, "AUTH_CHANGEPASS_SUCCESS", "Password changed");
                    $this->success[] = $this->lang['changepass_success'];
                    return true;
                } else {
                    $this->logActivity($username, "AUTH_CHANGEPASS_FAIL", "Current Password Incorrect ( DB : {$db_currpass} / Given : {$currpass} )");
                    $error[] = $this->lang['changepass_currpass_incorrect'];
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Changes the stored email address based on username 
     * @param string $username
     * @param string $email
     * @return boolean
     */
    function changeEmail($username, $email) {
        if (strlen($username) == 0) {
            $error[] = $this->lang['changeemail_username_empty'];
        } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
            $error[] = $this->lang['changeemail_username_long'];
        } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
            $error[] = $this->lang['changeemail_username_short'];
        }
        if (strlen($email) == 0) {
            $error[] = $this->lang['changeemail_email_empty'];
        } elseif (strlen($email) > MAX_EMAIL_LENGTH) {
            $error[] = $this->lang['changeemail_email_long'];
        } elseif (strlen($email) < MIN_EMAIL_LENGTH) {
            $error[] = $this->lang['changeemail_email_short'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = $this->lang['changeemail_email_invalid'];
        }
        if (count($this->error) == 0) {
            $query = $this->db->select("SELECT email FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
            $count = count($query);
            if ($count == 0) {
                $this->logActivity("UNKNOWN", "AUTH_CHANGEEMAIL_FAIL", "Username Incorrect ({$username})");
                $error[] = $this->lang['changeemail_username_incorrect'];
                return false;
            } else {
                $db_email = $query[0]->email;
                if ($email == $db_email) {
                    $this->logActivity($username, "AUTH_CHANGEEMAIL_FAIL", "Old and new email matched ({$email})");
                    $error[] = $this->lang['changeemail_email_match'];
                    return false;
                } else {
                    $this->db->update(PREFIX.'users', array('email' => $email), array('username' => $username));
                    $this->logActivity($username, "AUTH_CHANGEEMAIL_SUCCESS", "Email changed from {$db_email} to {$email}");
                    $this->success[] = $this->lang['changeemail_success'];
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Give the user the ability to change their password if the current password is forgotten 
     * by sending email to the email address associated to that user
     * @param string $email
     * @param string $username
     * @param string $key
     * @param string $newpass
     * @param string $verifynewpass
     * @return boolean
     */
    function resetPass($email = '0', $username = '0', $key = '0', $newpass = '0', $verifynewpass = '0') {
        $attcount = $this->getAttempt($_SERVER['REMOTE_ADDR']);
        if ($attcount[0]->count >= MAX_ATTEMPTS) {
            $error[] = $this->lang['resetpass_lockedout'];
            $error[] = sprintf($this->lang['resetpass_wait'], WAIT_TIME);
            return false;
        } else {
            if ($username == '0' && $key == '0') {
                if (strlen($email) == 0) {
                    $error[] = $this->lang['resetpass_email_empty'];
                } elseif (strlen($email) > MAX_EMAIL_LENGTH) {
                    $error[] = $this->lang['resetpass_email_long'];
                } elseif (strlen($email) < MIN_EMAIL_LENGTH) {
                    $error[] = $this->lang['resetpass_email_short'];
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error[] = $this->lang['resetpass_email_invalid'];
                }

                $query = $this->db->select("SELECT username FROM ".PREFIX."users WHERE email=:email", array(':email' => $email));
                $count = count($query);
                if ($count == 0) {
                    $error[] = $this->lang['resetpass_email_incorrect'];
                    $attcount[0]->count = $attcount[0]->count + 1;
                    $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                    $this->logActivity("UNKNOWN", "AUTH_RESETPASS_FAIL", "Email incorrect ({$email})");
                    $error[] = sprintf($this->lang['resetpass_attempts_remaining'], $remaincount);
                    $this->addAttempt($_SERVER['REMOTE_ADDR']);
                    return false;
                } else {
                    $resetkey = $this->randomKey(RANDOM_KEY_LENGTH);
                    $username = $query[0]->username;
                    $this->db->update(PREFIX.'users', array('resetkey' => $resetkey), array('username' => $username));

                    //EMAIL MESSAGE USING PHPMAILER
                    $mail = new \Helpers\PhpMailer\Mail();
                    $mail->setFrom(EMAIL_FROM);
                    $mail->addAddress($email);
                    $mail->subject(SITE_NAME . " - Password reset request !");
                    $body = "Hello {$username}<br/><br/>";
                    $body .= "You recently requested a password reset on " . SITE_NAME . "<br/>";
                    $body .= "To proceed with the password reset, please click the following link :<br/><br/>";
                    $body .= "<b><a href=\"" . BASE_URL . RESET_PASSWORD_ROUTE . "?username={$username}&key={$resetkey}\">Reset My Password</a></b>";
                    $mail->body($body);
                    $mail->send();
                    $this->logActivity($username, "AUTH_RESETPASS_SUCCESS", "Reset pass request sent to {$email} ( Key : {$resetkey} )");
                    $this->success[] = $this->lang['resetpass_email_sent'];
                    return true;
                }
            } else {
                // if username, key  and newpass are provided
                // Reset Password
                if (strlen($key) == 0) {
                    $error[] = $this->lang['resetpass_key_empty'];
                } elseif (strlen($key) < RANDOM_KEY_LENGTH) {
                    $error[] = $this->lang['resetpass_key_short'];
                } elseif (strlen($key) > RANDOM_KEY_LENGTH) {
                    $error[] = $this->lang['resetpass_key_long'];
                }
                if (strlen($newpass) == 0) {
                    $error[] = $this->lang['resetpass_newpass_empty'];
                } elseif (strlen($newpass) > MAX_PASSWORD_LENGTH) {
                    $error[] = $this->lang['resetpass_newpass_long'];
                } elseif (strlen($newpass) < MIN_PASSWORD_LENGTH) {
                    $error[] = $this->lang['resetpass_newpass_short'];
                } elseif (strstr($newpass, $username)) {
                    $error[] = $this->lang['resetpass_newpass_username'];
                } elseif ($newpass !== $verifynewpass) {
                    $error[] = $this->lang['resetpass_newpass_nomatch'];
                }
                if (count($this->error) == 0) {
                    $query = $this->db->select("SELECT resetkey FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
                    $count = count($query);
                    if ($count == 0) {
                        $error[] = $this->lang['resetpass_username_incorrect'];
                        $attcount[0]->count = $attcount[0]->count + 1;
                        $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                        $this->logActivity("UNKNOWN", "AUTH_RESETPASS_FAIL", "Username incorrect ({$username})");
                        $error[] = sprintf($this->lang['resetpass_attempts_remaining'], $remaincount);
                        $this->addAttempt($_SERVER['REMOTE_ADDR']);
                        return false;
                    } else {
                        $db_key = $query[0]->resetkey;
                        if ($key == $db_key) {
                            //if reset key ok update pass
                            $newpass = $this->hashpass($newpass);
                            $resetkey = '0';
                            $this->db->update(PREFIX.'users', array('password' => $newpass, 'resetkey' => $resetkey), array('username' => $username));
                            $this->logActivity($username, "AUTH_RESETPASS_SUCCESS", "Password reset - Key reset");
                            $this->success[] = $this->lang['resetpass_success'];
                            return true;
                        } else {
                            $error[] = $this->lang['resetpass_key_incorrect'];
                            $attcount[0]->count = $attcount[0]->count + 1;
                            $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                            $this->logActivity($username, "AUTH_RESETPASS_FAIL", "Key Incorrect ( DB : {$db_key} / Given : {$key} )");
                            $error[] = sprintf($this->lang['resetpass_attempts_remaining'], $remaincount);
                            $this->addAttempt($_SERVER['REMOTE_ADDR']);
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Checks if the reset key is correct for provided username 
     * @param string $username
     * @param string $key
     * @return boolean
     */
    function checkResetKey($username, $key) {
        $attcount = $this->getAttempt($_SERVER['REMOTE_ADDR']);
        if ($attcount[0]->count >= MAX_ATTEMPTS) {
            $error[] = $this->lang['resetpass_lockedout'];
            $error[] = sprintf($this->lang['resetpass_wait'], WAIT_TIME);
            return false;
        } else {
            if (strlen($username) == 0) {
                return false;
            } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
                return false;
            } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
                return false;
            } elseif (strlen($key) == 0) {
                return false;
            } elseif (strlen($key) < RANDOM_KEY_LENGTH) {
                return false;
            } elseif (strlen($key) > RANDOM_KEY_LENGTH) {
                return false;
            } else {
                $query = $this->db->select("SELECT resetkey FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
                $count = count($query);
                if ($count == 0) {
                    $this->logActivity("UNKNOWN", "AUTH_CHECKRESETKEY_FAIL", "Username doesn't exist ({$username})");
                    $this->addAttempt($_SERVER['REMOTE_ADDR']);
                    $error[] = $this->lang['checkresetkey_username_incorrect'];
                    $attcount[0]->count = $attcount[0]->count + 1;
                    $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                    $error[] = sprintf($this->lang['checkresetkey_attempts_remaining'], $remaincount);
                    return false;
                } else {
                    $db_key = $query[0]->resetkey;
                    if ($key == $db_key) {
                        return true;
                    } else {
                        $this->logActivity($username, "AUTH_CHECKRESETKEY_FAIL", "Key provided is different to DB key ( DB : {$db_key} / Given : {$key} )");
                        $this->addAttempt($_SERVER['REMOTE_ADDR']);
                        $error[] = $this->lang['checkresetkey_key_incorrect'];
                        $attcount[0]->count = $attcount[0]->count + 1;
                        $remaincount = (int) MAX_ATTEMPTS - $attcount[0]->count;
                        $error[] = sprintf($this->lang['checkresetkey_attempts_remaining'], $remaincount);
                        return false;
                    }
                }
            }
        }
    }

    /**
     * Deletes a user's account. Requires user's password
     * @param string $username
     * @param string $password
     * @return boolean
     */
    function deleteAccount($username, $password) {
        if (strlen($username) == 0) {
            $error[] = $this->lang['deleteaccount_username_empty'];
        } elseif (strlen($username) > MAX_USERNAME_LENGTH) {
            $error[] = $this->lang['deleteaccount_username_long'];
        } elseif (strlen($username) < MIN_USERNAME_LENGTH) {
            $error[] = $this->lang['deleteaccount_username_short'];
        }
        if (strlen($password) == 0) {
            $error[] = $this->lang['deleteaccount_password_empty'];
        } elseif (strlen($password) > MAX_PASSWORD_LENGTH) {
            $error[] = $this->lang['deleteaccount_password_long'];
        } elseif (strlen($password) < MIN_PASSWORD_LENGTH) {
            $error[] = $this->lang['deleteaccount_password_short'];
        }
        if (count($this->error) == 0) {

            $query = $this->db->select("SELECT password FROM ".PREFIX."users WHERE username=:username", array(':username' => $username));
            $count = count($query);
            if ($count == 0) {
                $this->logActivity("UNKNOWN", "AUTH_DELETEACCOUNT_FAIL", "Username Incorrect ({$username})");
                $error[] = $this->lang['deleteaccount_username_incorrect'];
                return false;
            } else {
                $db_password = $query[0]->password;
                $verify_password = \Helpers\Password::verify($password, $db_password);
                if ($verify_password) {
                    $this->db->delete(PREFIX.'users', array('username' => $username));
                    $this->db->delete(PREFIX.'sessions', array('username' => $username));
                    $this->logActivity($username, "AUTH_DELETEACCOUNT_SUCCESS", "Account deleted - Cookies deleted");
                    $this->success[] = $this->lang['deleteaccount_success'];
                    return true;
                } else {
                    $this->logActivity($username, "AUTH_DELETEACCOUNT_FAIL", "Password incorrect ( DB : {$db_password} / Given : {$password} )");
                    $error[] = $this->lang['deleteaccount_password_incorrect'];
                    return false;
                }
            }
        } else {
            return false;
        }
    }

	/**
	 * Get current user's ID
	 */
	public function getID($username){
		$data = $this->db->select("SELECT userID FROM ".PREFIX."users WHERE username = :username",
			array(':username' => $username));
		return $data[0]->userID;
	}
	
	/**
	 * Update given field in users table
	 */
	public function updateUser($data,$where){
		$this->db->update(PREFIX."users",$data,$where);
	}
	
	/**
	 * Get Current Session Data
	 */
    public function user_info(){
        return $this->currentCookieInfo()['uid'];
    }
	
}
