<?php

/**
* Helper to get all user Data from users database
*/

namespace Models;

class UserData extends \Core\Model {

	/**
	 * Get current user's username from database
	 */
	public function getUserName($where_id){
		$data = $this->db->select("SELECT username FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		return $data[0]->username;
	}

	/**
	 * Get current user's Email from database
	 */
	public function getUserEmail($where_id){
		$data = $this->db->select("SELECT email FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		return $data[0]->email;
	}
	
	/**
	 * Get current user's Last Login Date from database
	 */
	public function getUserLastLogin($where_id){
		$data = $this->db->select("SELECT LastLogin FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		return $data[0]->LastLogin;
	}
	
	/**
	 * Get current user's Sign Up Date from database
	 */
	public function getUserSignUp($where_id){
		$data = $this->db->select("SELECT SignUp FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		return $data[0]->SignUp;
	}
	
}