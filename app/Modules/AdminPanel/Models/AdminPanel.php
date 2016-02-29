<?php
namespace Modules\AdminPanel\Models;

use Core\Model;
use Helpers\Request;

class AdminPanel extends Model {

  // Get list of all users
  public function getUsers($orderby){

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
      \Helpers\Url::redirect('AdminPanel-Group/'.$new_group_id);
      return true;
    }else{
      return false;
    }
  }

  /**
   * checkForumGroup
   *
   * get forum group data.
   *
   * @param string $forum_group Name of Forum Group
   * @param int $groupID ID of User Group
   *
   * @return boolean returns true/false
   */
  public function checkGroupForum($forum_group, $groupID){
    $data = $this->db->select("
        SELECT
          forum_group,
          groupID
        FROM
          ".PREFIX."forum_groups
        WHERE
          forum_group = :forum_group
          AND
          groupID = :groupID
        ORDER BY
          groupID DESC
        ",
        array(':forum_group' => $forum_group, ':groupID' => $groupID));
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
  }

  /**
   * editForumGroup
   *
   * create or delete forum group.
   *
   * @param string $groupName Name of Forum Group
   * @param string $action Add/Remove
   * @param int $groupID ID of User Group
   *
   * @return boolean returns true/false
   */
  public function editForumGroup($groupName, $action, $groupID){
    if($action == "add"){
      // Add Forum Group to Group
      $data = $this->db->insert(PREFIX.'forum_groups', array('forum_group' => $groupName, 'groupID' => $groupID));
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }else if($action == "remove"){
      // Remove Forum Group from Group
      $data = $this->db->delete(PREFIX.'forum_groups', array('forum_group' => $groupName, 'groupID' => $groupID));
      $count = count($data);
      if($count > 0){
        return true;
      }else{
        return false;
      }
    }
  }

  /**
   * globalForumSetting
   *
   * get setting for requested setting.
   *
   * @param string $action which setting to get
   *
   * @return string returns requested setting
   */
  public function globalForumSetting($action){
    $data = $this->db->select("
      SELECT setting_value
      FROM ".PREFIX."forum_settings
      WHERE setting_title = :action
      LIMIT 1
    ",
    array(':action' => $action));
    return $data[0]->setting_value;
  }

  /**
   * updateGlobalSettings
   *
   * update Forum Global Settings to database.
   *
   * @param string $forum_on_off Enable/Disable True/Fale
   * @param string $forum_title Title/Name
   * @param string $forum_description Forum Description
   * @param int $forum_topic_limit Topic Per Page Limit
   * @param int $forum_topic_reply_limit Topic Rely Per Page Limit
   *
   * @return boolean returns true/false
   */
  public function updateGlobalSettings($forum_on_off,$forum_title,$forum_description,$forum_topic_limit,$forum_topic_reply_limit){
    // Update groups table
		$query[] = $this->db->update(PREFIX.'forum_settings', array('setting_value' => $forum_on_off), array('setting_title' => 'forum_on_off'));
    $query[] = $this->db->update(PREFIX.'forum_settings', array('setting_value' => $forum_title), array('setting_title' => 'forum_title'));
    $query[] = $this->db->update(PREFIX.'forum_settings', array('setting_value' => $forum_description), array('setting_title' => 'forum_description'));
    $query[] = $this->db->update(PREFIX.'forum_settings', array('setting_value' => $forum_topic_limit), array('setting_title' => 'forum_topic_limit'));
    $query[] = $this->db->update(PREFIX.'forum_settings', array('setting_value' => $forum_topic_reply_limit), array('setting_title' => 'forum_topic_reply_limit'));
		$count = count($query);
		// Check to make sure something was updated
		if($count > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
   * catMainList
   *
   * get main forum categories.
   *
   * @return array returns list from db
   */
  public function catMainList(){
    // Get main categories list from db
    $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."forum_cat
        GROUP BY
          forum_title
        ORDER BY
          forum_order_title ASC
        ");
    return $data;
  }

  /**
   * getCatMain
   *
   * get data for selected Main Category.
   *
   * @param string gets data based on forum_title
   *
   * @return array returns list from db
   */
  public function getCatMain($id){
    // Get main categories list from db
    $data = $this->db->select("
        SELECT
          *
        FROM
          ".PREFIX."forum_cat
        WHERE
          forum_id = :forum_id
        LIMIT 1
        ",
        array(':forum_id' => $id));
    return $data;
  }

  /**
   * updateCatMainTitle
   *
   * update data for selected Main Category.
   *
   * @param string gets data based on prev_forum_title
   * @param string puts data from $new_forum_title
   *
   * @return boolean returns true/false
   */
  public function updateCatMainTitle($prev_forum_title,$new_forum_title){
    // Update groups table
		$query = $this->db->update(PREFIX.'forum_cat', array('forum_title' => $new_forum_title), array('forum_title' => $prev_forum_title));
		$count = count($query);
		// Check to make sure something was updated
		if($count > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
   * moveUpCatMain
   *
   * update position of given object.
   *
   * @param int id of the object
   *
   * @return boolean returns true/false
   */
  public function moveUpCatMain($old){
    // Moving up one spot
    $new = $old - 1;
    // Make sure this object is not already at top
    if($new > 0){
      // Update groups table
  		$query = $this->db->raw("
        UPDATE
          ".PREFIX."forum_cat
        SET
          `forum_order_title` = CASE
          WHEN (`forum_order_title` = $old) THEN
            $new
          WHEN (`forum_order_title` > $old and `forum_order_title` <= $new) THEN
            `forum_order_title`- 1
          WHEN (`forum_order_title` < $old and `forum_order_title` >= $new) THEN
            `forum_order_title`+ 1
          ELSE
            `forum_order_title`
        END
        ");
  		$count = count($query);
  		// Check to make sure something was updated
  		if($count > 0){
  			return true;
  		}else{
  			return false;
  		}
    }else{
      return false;
    }
  }

  /**
   * moveDownCatMain
   *
   * update position of given object.
   *
   * @param int id of the object
   *
   * @return boolean returns true/false
   */
  public function moveDownCatMain($old){
    // Moving down one spot
    $new = $old + 1;
    // Update groups table
		$query = $this->db->raw("
      UPDATE
        ".PREFIX."forum_cat
      SET
        `forum_order_title` = CASE
        WHEN (`forum_order_title` = $old) THEN
          $new
        WHEN (`forum_order_title` < $old and `forum_order_title` >= $new) THEN
          `forum_order_title`+ 1
        WHEN (`forum_order_title` > $old and `forum_order_title` <= $new) THEN
          `forum_order_title`- 1
        ELSE
          `forum_order_title`
      END
      ");
		$count = count($query);
		// Check to make sure something was updated
		if($count > 0){
			return true;
		}else{
			return false;
		}
  }

  /**
   * getLastCatMain
   *
   * get last order number.
   *
   * @return int returns last order id
   */
  public function getLastCatMain(){
    $data = $this->db->select("
      SELECT forum_order_title
      FROM ".PREFIX."forum_cat
      ORDER BY forum_order_title DESC
      LIMIT 1
    ");
    return $data[0]->forum_order_title;
  }

}
