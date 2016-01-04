<?php
/**
* Model to get all user Data from users database
*/

namespace Models;

class RightLinks extends \Core\Model {

	public function RecentForumPosts(){
		// Which database do we use
		$stc_page_sel = "forum";
		$site_forum_title = "Forum";
		
		echo "<div class='panel panel-default'>";
			echo "<div class='panel-heading' style='font-weight: bold'>";
				echo "Forum Recent Posts";
			echo "</div>";
			echo "<ul class='list-group'>";

				// Recent forum post limits
				if(isUserLoggedIn()){
					// How many recent posts to show if user is logged in
					$rp_limit = 10;
				}else{
					// How many recent posts to show if user is not logged in
					$rp_limit = 5;
				}
			
				// Get all Sub Categories for current category
				//$query = "SELECT * FROM ".$db_table_prefix."forum_posts WHERE `forum_id`='$f_id' ORDER BY forum_timestamp DESC";
				$query = "
					SELECT sub.*
					FROM
					(SELECT 
						fp.forum_post_id as forum_post_id, fp.forum_id as forum_id, 
						fp.forum_user_id as forum_user_id, fp.forum_title as forum_title, 
						fp.forum_content as forum_content, fp.forum_edit_date as forum_edit_date,
						fp.forum_timestamp as forum_timestamp, fpr.id as id,
						fpr.fpr_post_id as fpr_post_id, fpr.fpr_id as fpr_id,
						fpr.fpr_user_id as fpr_user_id, fpr.fpr_title as fpr_title,
						fpr.fpr_content as fpr_content, fpr.fpr_edit_date as fpr_edit_date,
						fpr.fpr_timestamp as fpr_timestamp,		
						GREATEST(fp.forum_timestamp, COALESCE(fpr.fpr_timestamp, '00-00-00 00:00:00')) AS tstamp
						FROM ".$db_table_prefix."forum_posts fp
						LEFT JOIN ".$db_table_prefix."forum_posts_replys fpr
						ON fp.forum_post_id = fpr.fpr_post_id
						ORDER BY tstamp DESC
					) sub
					
					GROUP BY forum_post_id
					ORDER BY tstamp DESC
					LIMIT $rp_limit
				";
				
				if($result = $mysqli->query($query)){
					$arr2 = $result->fetch_all(MYSQLI_BOTH);
					foreach($arr2 as $row2)
					{
						$f_p_id = $row2['forum_post_id'];
						$f_p_id_cat = $row2['forum_id'];
						$f_p_title = $row2['forum_title'];
						$f_p_timestamp = $row2['forum_timestamp'];
						$f_p_user_id = $row2['forum_user_id'];
						$tstamp = $row2['tstamp'];
						$f_p_user_name = get_user_name_2($f_p_user_id);
						
						$f_p_title = stripslashes($f_p_title);

						//Reply information
						$rp_user_id2 = $row2['fpr_user_id'];
						$rp_timestamp2 = $row2['fpr_timestamp'];
						
						// Set the incrament of each post
						if(isset($vm_id_a_rp)){ $vm_id_a_rp++; }else{ $vm_id_a_rp = "1"; };
						//echo "$vm_id_a_rp";
							
						$f_p_title = strlen($f_p_title) > 30 ? substr($f_p_title, 0, 30) . ".." : $f_p_title;
						
						//If no reply show created by
						if($rp_timestamp2 == NULL){
							echo "<ul class='list-group-item'>";
							//echo "($tstamp)"; // Test timestamp
							echo "<a href='${site_url_link}member/$f_p_user_id/'>$f_p_user_name</a> created.. <br>";
							echo "<strong>";
							echo "<a href='${site_url_link}${site_forum_title}/display_topic/$f_p_id/' title='$f_p_title' ALT='$f_p_title'>$f_p_title</a>";
							echo "</strong>";
							echo "<br>";
							//Display how long ago this was posted
							$timestart = "$f_p_timestamp";  //Time of post
							require_once "external/timediff.php";
							echo " <font color=green> " . dateDiff("now", "$timestart", 1) . " ago</font> ";
							//echo "($f_p_timestamp)"; // Test timestamp
							echo "</ul>";
						}else{
							$rp_user_name2 = get_user_name_2($rp_user_id2);
							//If reply show the following
							echo "<ul class='list-group-item'>";
							//echo "($tstamp)"; // Test timestamp
							echo "<a href='${site_url_link}member/$rp_user_id2/'>$rp_user_name2</a> posted on.. <br>";
							echo "<strong>";
							echo "<a href='${site_url_link}${site_forum_title}/display_topic/$f_p_id/' title='$f_p_title' ALT='$f_p_title'>$f_p_title</a>";
							echo "</strong>";
							//Display how long ago this was posted
							$timestart = "$rp_timestamp2";  //Time of post
							require_once "external/timediff.php";
							echo "<br><font color=green> " . dateDiff("now", "$timestart", 1) . " ago</font> ";
							//echo "($rp_timestamp2)"; // Test timestamp
							unset($timestart, $rp_timestamp2);
							echo "</ul>";
						}// End reply check
					} // End query
				} // End query check
				
		echo "</ul></div>";
	} // End of recent forum posts
	
	
	/**
	*	Function to display web site stats
	*/
	public function DisplaySiteStats(){
		
		// Get total number of currently online users
		$queryA = $this->db->select('SELECT * FROM '.PREFIX.'users_online ');
		$total_users_online = count($queryA);
		
		//Gets total number of members that are have activated accounts
		$where_val = "1";
		$queryB = $this->db->select('SELECT * FROM '.PREFIX.'users WHERE isactive = :isactive',
			array(':isactive' => $where_val));
		$total_members = count($queryB);
		
		$site_stats_display = "
			<div class='col-lg-4 col-md-4'>
				<div class='panel panel-default'>
					<div class='panel-heading' style='font-weight: bold'>
						Site Stats
					</div>
					<ul class='list-group'>
						<li class='list-group-item'><a href='${site_url_link}Members' rel='nofollow'>Members: $total_members</a></li>
						<li class='list-group-item'><a href='${site_url_link}MembersOnline' rel='nofollow'>Members Online: $total_users_online</a></li>
					</ul>
				</div>
			</div>";
		return $site_stats_display;
		
		//echo "<li class='list-group-item'><a href='${site_url_link}Forum/'>Forum Topics: </a></li>";
		//echo "<li class='list-group-item'><a href='${site_url_link}Forum/'>Forum Replys: </a></li>";
	}
	

	
}