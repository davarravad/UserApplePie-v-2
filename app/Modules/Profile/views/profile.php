<?php
/**
 * Profile layout
 */

use Core\Language;

// ToDo: Move the foreach to controller and run everything through $data['val']
// Get user data from user's database
foreach($data['user_data'] as $user_data){
	$u_name = $user_data->username;
	$u_first_name = $user_data->firstName;
	$u_gender = $user_data->gender;
	$u_lastlogin = date("F d, Y",strtotime($user_data->LastLogin));
	$u_signup = date("F d, Y",strtotime($user_data->SignUp));
	$u_img = $user_data->userImage;
	$u_aboutme = $user_data->aboutme;
	$u_website = $user_data->website;
}

?>
<div class='col-lg-12'>
	<div class='row'>
		<!-- Basic User Info Display -->
		<div class='col-md-4 col-lg-4'>
			<div class="panel panel-primary">
				<div class='panel-heading'>
					<h4><?php echo $u_name ?></h4>
				</div>
				<div class='panel-body'>
					<div class='row'>
						<div class="col-md-8 col-lg-8" align="center" style="float: none; margin: 0 auto;">
							<?php if(!empty($u_img)){ ?>
								<img alt="User Pic" src="<?php echo $u_img ?>" class="img-circle img-responsive" style='overflow:hidden'>
							<?php }else{ ?>
								<span class='glyphicon glyphicon-user icon-size'></span>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class='panel-footer'>
					<div class='row'>
						<div class=" col-md-12 col-lg-12 ">
							<table class='table table-striped'>
								<tr><td>First Name</td><td><?php echo $u_first_name ?></td></tr>
								<?php
									if($data['user_group']){
										echo "<tr><td>Group</td><td>";
										foreach($data['user_group'] as $row){
											echo " $row <br>";
										}
										echo "</td></tr>";
									}
								?>
								<tr><td>Gender</td><td><?php echo $u_gender ?></td></tr>
								<?php if(isset($u_website)){echo "<tr><td>Website</td><td><a href='$u_website' target='_blank'>View</a></td></tr>";}?>
								<tr><td>Last Login</td><td><?php echo $u_lastlogin ?></td></tr>
								<tr><td>Sign Up</td><td><?php echo $u_signup ?></td></tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Basic User Info Display -->
		<div class='col-md-8 col-lg-8'>
			<div class="panel panel-default">
				<div class='panel-heading'>
					<h4>All About <?php echo $u_name ?></h4>
				</div>
				<div class='panel-body'>
					<?php echo $u_aboutme ?>
				</div>
			</div>
		</div>
	</div>
</div>
