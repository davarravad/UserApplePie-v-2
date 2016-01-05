<?php
/**
* Model to get all user Data from users database
*/

namespace Models;

class LeftLinks extends \Core\Model {

	/**
	*	Function to display Account Links
	*/
	public function AccountLinks(){
		
		$left_account_links = "
			<div class='col-lg-4 col-md-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading' style='font-weight: bold'>
						Account Settings
					</div>
					<ul class='list-group'>
						<li class='list-group-item'><a href='${site_url_link}ChangeEmail' rel='nofollow'>Change Email</a></li>
						<li class='list-group-item'><a href='${site_url_link}ChangePassword' rel='nofollow'>Change Password</a></li>
					</ul>
				</div>
			</div>";
		return $left_account_links;
		
	}
	

	
}