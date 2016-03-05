<?php
namespace Helpers;

use Helpers\Database;
use Helpers\Cookie;

class CurrentUserData
{
    private static $db;

	// Get user data for requested user's profile
	public static function getCUD($where_id){
		self::$db = Database::get();
		$user_data = self::$db->select("
				SELECT
					u.userID,
					u.username,
					u.firstName,
					u.gender,
					u.userImage,
					u.email,
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
	}

  // Get current user's groups
  public static function getCUGroups($where_id){
    self::$db = Database::get();
    $user_groups = self::$db->select("
        SELECT
          ug.userID, ug.groupID, g.groupID, g.groupName, g.groupDescription, g.groupFontColor, g.groupFontWeight
        FROM
          ".PREFIX."users_groups ug
        LEFT JOIN
          ".PREFIX."groups g
          ON g.groupID = ug.groupID
        WHERE
          ug.userID = :userID
        ",
      array(':userID' => $where_id));
    return $user_groups;
  }

  /**
	 * Get current user's username from database
	 */
	public static function getUserName($where_id){
		$data = self::$db->select("SELECT username FROM ".PREFIX."users WHERE userID = :userID",
			array(':userID' => $where_id));
		return $data[0]->username;
	}

  /**
  * Get Current User's Groups Data For Display
  */

  public function getUserGroups($where_id){
    self::$db = Database::get();
    $user_groups = self::$db->select("
        SELECT
          ug.userID, ug.groupID, g.groupID, g.groupName, g.groupDescription, g.groupFontColor, g.groupFontWeight
        FROM
          ".PREFIX."users_groups ug
        LEFT JOIN
          ".PREFIX."groups g
          ON g.groupID = ug.groupID
        WHERE
          ug.userID = :userID
        ",
      array(':userID' => $where_id));
      if(isset($user_groups)){
        foreach($user_groups as $row){
          $usergroup[] = " <font color='$row->groupFontColor' weight='$row->groupFontWeight'>$row->groupName</font> ";
        }
      }
    return $usergroup;
  }

}
