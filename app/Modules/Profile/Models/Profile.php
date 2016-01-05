<?php
namespace Modules\Profile\Models;

use Core\Model;

class Profile extends Model {
	
	// Get user data for requested user's profile
	public function user_data($where_id){
		if(ctype_digit($where_id)){
			$user_data = $this->db->select("
					SELECT 
						u.userID, 
						u.username, 
						u.firstName, 
						u.gender, 
						u.userImage, 
						u.LastLogin, 
						u.SignUp,
						ue.userID,
						ue.website,
						ue.aboutme
					FROM 
						".PREFIX."users u
					LEFT JOIN
						".PREFIX."users_extprofile ue
						ON u.userID = ue.userID
					WHERE 
						u.userID = :userID
					",
				array(':userID' => $where_id));
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