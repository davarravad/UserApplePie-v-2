<?php
/**
 * Create the members view
 */

use Helpers\Form,
    Helpers\ErrorHelper,
    Helpers\SuccessHelper,
    Core\Language;

?>

<div class='col-lg-8 col-md-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title']." - ".$data['u_username']  ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

      <?php
        // Display Success and Error Messages if any (TODO: Move to header file)
      	echo ErrorHelper::display();
      	echo SuccessHelper::display();
      	echo ErrorHelper::display_raw($error);
      	echo SuccessHelper::display_raw($success);
      ?>

			<?php echo Form::open(array('method' => 'post')); ?>

			<!-- User Name -->
			<div class='input-group' style='margin-bottom: 25px'>
				<span class='input-group-addon'><i class='glyphicon glyphicon-user'></i> UserName</span>
				<?php echo Form::input(array('type' => 'text', 'name' => 'au_username', 'class' => 'form-control', 'value' => $data['u_username'], 'placeholder' => 'UserName', 'maxlength' => '100')); ?>
			</div>

				<!-- First Name -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-user'></i> First Name</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'au_firstName', 'class' => 'form-control', 'value' => $data['u_firstName'], 'placeholder' => 'First Name', 'maxlength' => '100')); ?>
				</div>

				<!-- Email -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i> Email</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'au_email', 'class' => 'form-control', 'value' => $data['u_email'], 'placeholder' => 'Email Address', 'maxlength' => '100')); ?>
				</div>

				<!-- Gender -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-grain'></i> Gender</span>
					<select class='form-control' id='gender' name='au_gender'>
				    <option value='Male' <?php if($data['u_gender'] == "Male"){echo "SELECTED";}?> >Male</option>
				    <option value='Female' <?php if($data['u_gender'] == "Female"){echo "SELECTED";}?> >Female</option>
				  </select>
				</div>

				<!-- Website -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-globe'></i> Website</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'au_website', 'class' => 'form-control', 'value' => $data['u_website'], 'placeholder' => 'Website URL', 'maxlength' => '100')); ?>
				</div>

				<!-- Profile Image -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-picture'></i> Profile Image URL</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'au_userImage', 'class' => 'form-control', 'value' => $data['u_userImage'], 'placeholder' => 'Profile Image URL', 'maxlength' => '255')); ?>
				</div>

				<!-- About Me -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-book'></i> About Me</span>
					<?php echo Form::textBox(array('type' => 'text', 'name' => 'au_aboutme', 'class' => 'form-control', 'value' => $data['u_aboutme'], 'placeholder' => 'About Me', 'rows' => '6')); ?>
				</div>

				<!-- CSRF Token -->
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<input type="hidden" name="au_id" value="<?php echo $data['u_id']; ?>" />
        <input type="hidden" name="update_profile" value="true" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					<?php // echo Language::show('update_profile', 'Auth'); ?>
					Update Profile
				</button>
			<?php echo Form::close(); ?>

		</div>
	</div>
</div>

<div class='col-lg-4 col-md-4'>
	<div class='panel panel-primary'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'>Groups</h3>
		</div>
		<div class='panel-body'>
			<?php
        echo "<table class='table table-hover responsive'>";
          // Displays User's Groups they are a member of
          if(isset($data['user_member_groups'])){
            echo "<th style='background-color: #EEE'>Member of Following Groups: </th>";
            foreach($data['user_member_groups'] as $member){
              echo "<tr><td>";
              echo Form::open(array('method' => 'post', 'style' => 'display:inline-block'));
              echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."'>";
              echo "<input type='hidden' name='remove_group' value='true' />";
              echo "<input type='hidden' name='au_userID' value='".$data['u_id']."'>";
              echo "<input type='hidden' name='au_groupID' value='".$member[0]->groupID."'>";
              echo "<button class='btn btn-xs btn-danger' name='submit' type='submit'>Remove</button>";
              echo Form::close();
              echo " - <font color='".$member[0]->groupFontColor."' style='font-weight: ".$member[0]->groupFontWeight."'>".$member[0]->groupName."</font>";
              echo "</td></tr>";
            }
          }else{
            echo "<th style='background-color: #EEE'>Member of Following Groups: </th>";
            echo "<tr><td> User Not Member of Any Groups </td></tr>";
          }
        echo "</table>";

        echo "<table class='table table-hover responsive'>";
          // Displays User's Groups they are not a member of
          if(isset($data['user_notmember_groups'])){
            echo "<th style='background-color: #EEE'>Not Member of Following Groups: </th>";
            foreach($data['user_notmember_groups'] as $notmember){
              echo "<tr><td>";
              echo Form::open(array('method' => 'post', 'style' => 'display:inline-block'));
              echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."'>";
              echo "<input type='hidden' name='add_group' value='true' />";
              echo "<input type='hidden' name='au_userID' value='".$data['u_id']."'>";
              echo "<input type='hidden' name='au_groupID' value='".$notmember[0]->groupID."'>";
              echo "<button class='btn btn-xs btn-success' name='submit' type='submit'>Add</button>";
              echo Form::close();
              echo " - <font color='".$notmember[0]->groupFontColor."' style='font-weight: ".$notmember[0]->groupFontWeight."'>".$notmember[0]->groupName."</font> ";
              echo "</td></tr>";
            }
          }else{
            echo "<th style='background-color: #EEE'>Not Member of Following Groups: </th>";
            echo "<tr><td> User is Member of All Groups </td></tr>";
          }
        echo "</table>";
      ?>
		</div>
	</div>
</div>

<div class='col-lg-4 col-md-4'>
	<div class='panel panel-info'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'>User Stats</h3>
		</div>
		<div class='panel-body'>
			<b>Last Login</b>: <?php if($data['u_lastlogin']){ echo date("F d, Y",strtotime($data['u_lastlogin'])); }else{ echo "Never"; } ?><br>
			<b>SignUp</b>: <?php echo date("F d, Y",strtotime($data['u_signup'])) ?>
			<hr>
			<?php
				if($data['u_isactive'] == "1"){
					echo "User Account Is Active";
          echo Form::open(array('method' => 'post'));
          echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."'>";
          echo "<input type='hidden' name='deactivate_user' value='true' />";
          echo "<input type='hidden' name='au_id' value='".$data['u_id']."'>";
          echo "<button class='btn btn-xs btn-danger' name='submit' type='submit'>Deactivate User</button>";
          echo Form::close();
				}else{
					echo "User Account Is Not Active";
          echo Form::open(array('method' => 'post'));
          echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."'>";
          echo "<input type='hidden' name='activate_user' value='true' />";
          echo "<input type='hidden' name='au_id' value='".$data['u_id']."'>";
          echo "<button class='btn btn-xs btn-success' name='submit' type='submit'>Activate User</button>";
          echo Form::close();
				}
			?>
		</div>
	</div>
</div>
