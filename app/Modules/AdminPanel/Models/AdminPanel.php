<?php
/**
 * Admin Panel Models
 *
 * @author DaVaR - davar@userapplepie.com
 * @version 1.0
 * @date Feb 20 2016
 * @updated Mar 4 2016
 */

namespace Modules\AdminPanel\Models;

use Core\Model;

class AdminPanel extends Model {

  // Get list of all users
  public function getUsers($orderby, $limit = null){

    // Set default orderby if one is not set
    if($orderby == "ID-DESC"){
      $run_order = "userID DESC";
    }else if($orderby == "ID-ASC"){
      $run_order = "userID ASC";
    }else if($orderby == "UN-DESC"){
      $run_order = "username DESC";
    }else if($orderby == "UN-ASC"){
      $run_order = "username ASC";
    }else{
      // Default order
      $run_order = "userID ASC";
    }

    $user_data = $this->db->select("
        SELECT
          userID,
          username,
          firstName,
          LastLogin
        FROM
          ".PREFIX."users
        ORDER BY
          $run_order
        $limit
        ");
    return $user_data;
  }

  // Get selected user's data
  public function getUser($id){
    $user_data = $this->db->select("
        SELECT
          u.userID,
          u.username,
          u.firstName,
          u.email,
          u.gender,
          u.userImage,
          u.LastLogin,
          u.SignUp,
          u.isactive,
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
        ORDER BY
          u.userID ASC
        ",
        array(':userID' => $id));
    return $user_data;
  }

  // Update User's Profile Data
	public function updateProfile($au_id, $au_username, $au_firstName, $au_email, $au_gender, $au_website, $au_userImage, $au_aboutme){
		// Format the About Me for database
		$au_aboutme = nl2br($au_aboutme);

		// Update users table
		$query_a = $this->db->update(PREFIX.'users', array('username' => $au_username, 'firstName' => $au_firstName, 'email' => $au_email, 'gender' => $au_gender, 'userImage' => $au_userImage), array('userID' => $au_id));
		$count_a = count($query_a);
		// Update users_extprofile
		$query_b = $this->db->update(PREFIX.'users_extprofile', array('website' => $au_website, 'aboutme' => $au_aboutme), array('userID' => $au_id));
		$count_b = count($query_b);
		// Check to make sure something was updated
		$count_t = $count_a + $count_b;
		if($count_t > 0){
			return true;
		}else{
			return false;
		}
	}

  // Update users isactive status
  public function activateUser($au_id){
    // Update users table isactive status
		$query_a = $this->db->update(PREFIX.'users', array('isactive' => '1'), array('userID' => $au_id));
		$count_a = count($query_a);
		if($count_a > 0){
			return true;
		}else{
			return false;
		}
  }

  // Update users isactive status
  public function deactivateUser($au_id){
    // Update users table isactive status
		$query_a = $this->db->update(PREFIX.'users', array('isactive' => '0'), array('userID' => $au_id));
		$count_a = count($query_a);
		if($count_a > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
  * getTotalUsers
  *
  * Gets total count of users
  *
  * @return int count
  */
  public function getTotalUsers(){
    $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."users
        ");
    return count($data);
  }

  // Get list of all groups
  public function getAllGroups(){
    $data = $this->db->select("
        SELECT
          groupID
        FROM
          ".PREFIX."groups
        ORDER BY
          groupID
    ");
    return $data;
  }

  // Check to see if user is member of group
  public function checkUserGroup($userID, $groupID){
    $data = $this->db->select("
        SELECT
          userID,
          groupID
        FROM
          ".PREFIX."users_groups
        WHERE
          userID = :userID
          AND
          groupID = :groupID
        ORDER BY
          groupID DESC
        ",
        array(':userID' => $userID, ':groupID' => $groupID));
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
  }

  // Get group data for requested group
  public function getGroupData($id){
    $group_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupFontColor,
          groupFontWeight
        FROM
          ".PREFIX."groups
        WHERE
          groupID = :groupID
        ORDER BY
          groupID DESC
        ",
        array(':groupID' => $id));
    return $group_data;
  }

  // Remove given user from group
  public function removeFromGroup($userID, $groupID){
    $data = $this->db->delete(PREFIX.'users_groups', array('userID' => $userID, 'groupID' => $groupID));
    $count = count($data);
    if($count > 0){
      return true;
    }else{
      return false;
    }
  }

  // Add given user to group
  public function addToGroup($userID, $groupID){
    $data = $this->db->insert(PREFIX.'users_groups', array('userID' => $userID, 'groupID' => $groupID));
    $count = count($data);
    if($count > 0){
      return true;
    }else{
      return false;
    }
  }

  // Get all groups data
  public function getGroups($orderby){

    // Set default orderby if one is not set
    if($orderby == "ID-DESC"){
      $run_order = "groupID DESC";
    }else if($orderby == "ID-ASC"){
      $run_order = "groupID ASC";
    }else if($orderby == "UN-DESC"){
      $run_order = "groupName DESC";
    }else if($orderby == "UN-ASC"){
      $run_order = "groupName ASC";
    }else{
      // Default order
      $run_order = "groupID ASC";
    }

    $user_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupFontColor,
          groupFontWeight
        FROM
          ".PREFIX."groups
        ORDER BY
          $run_order
        ");
    return $user_data;
  }

  // Get selected group's data
  public function getGroup($id){
    $group_data = $this->db->select("
        SELECT
          groupID,
          groupName,
          groupDescription,
          groupFontColor,
          groupFontWeight
        FROM
          ".PREFIX."groups
        WHERE
          groupID = :groupID
        ORDER BY
          groupID ASC
        ",
        array(':groupID' => $id));
    return $group_data;
  }

  // Update Group's Data
	public function updateGroup($ag_groupID, $ag_groupName, $ag_groupDescription, $ag_groupFontColor, $ag_groupFontWeight){
		// Update groups table
		$query = $this->db->update(PREFIX.'groups', array('groupName' => $ag_groupName, 'groupDescription' => $ag_groupDescription, 'groupFontColor' => $ag_groupFontColor, 'groupFontWeight' => $ag_groupFontWeight), array('groupID' => $ag_groupID));
		$count = count($query);
		// Check to make sure something was updated
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}

  // delete group
  public function deleteGroup($groupID){
    $data = $this->db->delete(PREFIX.'groups', array('groupID' => $groupID));
    $count = count($data);
    if($count > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
   * createGroup
   *
   * inserts new user group to database.
   *
   * @param string $groupName Name of New User Group
   *
   * @return boolean returns true/false
   */
  public function createGroup($groupName){
    $data = $this->db->insert(PREFIX.'groups', array('groupName' => $groupName));
    $new_group_id = $this->db->lastInsertId('groupID');
    $count = count($data);
    if($count > 0){
      return $new_group_id;
    }else{
      return false;
    }
  }

}
