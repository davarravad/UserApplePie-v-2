<?php
/**
* Model to get all user Data from users database
*/

namespace Models;

class OnlineUsers extends \Core\Model {

	// Add user online to database
	public function add($userID){
		$data = array('userID' => $userID ,'lastAccess' => date('Y-m-d G:i:s'));
		$this->db->insert(PREFIX."users_online",$data,$where);
	}
	
	// Update when user is onLine
	public function update($userID){
		$query = $this->db->select('SELECT * FROM '.PREFIX.'users_online WHERE userID = :userID ', array(':userID' => $userID));
		$count = count($query);
		if($count == 0){
			$this->add($userID);
		}else{
			$data = array('lastAccess' => date('Y-m-d G:i:s'));
			$where = array('userID' => $userID);
			$this->db->update(PREFIX."users_online",$data,$where);
		}
	}
	
	// Remove user from online status - Logged Out or Idle
	public function remove($userID){
		$this->db->delete(PREFIX.'users_online', array('userID' => $userID));
	}
	
	// Check to see if user has been idle for more than 30 min
	public function check(){
		$this->db->delete_open(PREFIX.'users_online WHERE unix_timestamp(date_add(lastAccess, interval 30 minute)) < unix_timestamp(now()) ');
	}
	
	// Gets total number of users online
	public function total(){
		$query = $this->db->select('SELECT * FROM '.PREFIX.'users_online ');
		$total_members = count($query);
	}
	
	// Gets list of members that are currently online
	public function getMembersOnline(){
		// Get online users userID
		$data = $this->db->select("
				SELECT 
					u.userID,
					u.username,
					u.firstName,
					uo.userID,
					ug.userID,
					ug.groupID,
					g.groupID,
					g.groupName,
					g.groupFontColor,
					g.groupFontWeight
				FROM 
					uap_users_online uo
				LEFT JOIN
					uap_users u
					ON u.userID = uo.userID
				LEFT JOIN
					uap_users_groups ug
					ON uo.userID = ug.userID
				LEFT JOIN
					uap_groups g
					ON ug.groupID = g.groupID
				GROUP BY
					u.userID
				ORDER BY 
					u.userID ASC, g.groupID DESC
	");
		return $data;
	}
	
}