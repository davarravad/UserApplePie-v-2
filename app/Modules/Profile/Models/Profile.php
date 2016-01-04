<?php
namespace Modules\Profile\Models;

use Core\Model;

class Profile extends Model {
	
	// Get user data for requested user's profile
	public function user_data($where_id){
		if(ctype_digit($where_id)){
			$user_data = $this->db->select("SELECT userID, username, firstName, gender, userImage, LastLogin, SignUp FROM ".PREFIX."users WHERE userID = :userID",
				array(':userID' => $where_id));
			return $user_data;
		}else if(isset($where_id)){
			$user_data = $this->db->select("SELECT userID, username, firstName, gender, userImage, LastLogin, SignUp FROM ".PREFIX."users WHERE username = :username",
				array(':username' => $where_id));
			return $user_data;
		}else{
			return false;
		}
	}
	
	// Check to see if profile exist
	public function profile_exist($where_id){
		$query = $this->db->select("SELECT * FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		$count = count($query);
		if($count == 0){
			return false;
		}else{
			return true;
		}
	}
}