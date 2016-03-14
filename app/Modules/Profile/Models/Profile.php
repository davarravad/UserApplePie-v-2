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
						ue.aboutme,
						ue.signature
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

	// Update User's Profile Data
	public function updateProfile($u_id, $firstName, $gender, $website, $userImage, $aboutme, $signature){
		// Format the About Me for database
		$aboutme = nl2br($aboutme);
		// Update users table
		$query_a = $this->db->update(PREFIX.'users', array('firstName' => $firstName, 'gender' => $gender, 'userImage' => $userImage), array('userID' => $u_id));
		$count_a = count($query_a);
		// Update users_extprofile
		$query_b = $this->db->update(PREFIX.'users_extprofile', array('website' => $website, 'aboutme' => $aboutme, 'signature' => $signature), array('userID' => $u_id));
		$count_b = count($query_b);
		// Check to make sure something was updated
		$count_t = $count_a + $count_b;
		if($count_t > 0){
			return true;
		}else{
			return false;
		}
	}

}
