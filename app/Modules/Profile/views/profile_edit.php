<?php
/**
 * Edit Profile View Page
 */

 use Helpers\Form,
 	Core\Error,
 	Core\Success,
 	Core\Language;

?>
<div class='col-lg-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['profile_content'] ?></p>
			<?php echo Error::display($error); ?>
			<?php echo Success::display($success); ?>
			<?php echo Form::open(array('method' => 'post')); ?>



				<!-- First Name -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-user'></i> First Name</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'firstName', 'class' => 'form-control', 'value' => $data['u_firstName'], 'placeholder' => 'First Name', 'maxlength' => '100')); ?>
				</div>

				<!-- Gender -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-grain'></i> Gender</span>
					<select class='form-control' id='gender' name='gender'>
				    <option value='Male' <?php if($data['u_gender'] == "Male"){echo "SELECTED";}?> >Male</option>
				    <option value='Female' <?php if($data['u_gender'] == "Female"){echo "SELECTED";}?> >Female</option>
				  </select>
				</div>

				<!-- Website -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-globe'></i> Website</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'website', 'class' => 'form-control', 'value' => $data['u_website'], 'placeholder' => 'Website URL', 'maxlength' => '100')); ?>
				</div>

				<!-- Profile Image -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-picture'></i> Profile Image URL</span>
					<?php echo Form::input(array('type' => 'text', 'name' => 'userImage', 'class' => 'form-control', 'value' => $data['u_userImage'], 'placeholder' => 'Profile Image URL', 'maxlength' => '255')); ?>
				</div>

				<!-- About Me -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-book'></i> About Me</span>
					<?php echo Form::textBox(array('type' => 'text', 'name' => 'aboutme', 'class' => 'form-control', 'value' => $data['u_aboutme'], 'placeholder' => 'About Me', 'rows' => '6')); ?>
				</div>

        <!-- Signature -->
				<div class='input-group' style='margin-bottom: 25px'>
					<span class='input-group-addon'><i class='glyphicon glyphicon-book'></i> Signature</span>
					<?php echo Form::textBox(array('type' => 'text', 'name' => 'signature', 'class' => 'form-control', 'value' => $data['u_signature'], 'placeholder' => 'Forum Signature', 'rows' => '4')); ?>
				</div>

				<!-- CSRF Token -->
				<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
				<button class="btn btn-md btn-success" name="submit" type="submit">
					<?php // echo Language::show('update_profile', 'Auth'); ?>
					Update Profile
				</button>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>
