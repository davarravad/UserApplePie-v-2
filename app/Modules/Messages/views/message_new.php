<?php

// Message New Displays form for user to create a new message to send
// Also works with message reply

use Core\Language,
  Helpers\ErrorHelper,
  Helpers\SuccessHelper,
  Core\Error,
  Helpers\Form;

?>

<div class='col-lg-8 col-md-8'>

  <?php
  // Display Success and Error Messages if any (TODO: Move to header file)
  echo ErrorHelper::display();
  echo SuccessHelper::display();
  echo ErrorHelper::display_raw($error);
  echo SuccessHelper::display_raw($success);
  ?>

	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

<?php
  // Check to see if message form is disabled
  if($data['hide_form'] != "true"){
?>

			<?php echo Form::open(array('method' => 'post')); ?>

      <!-- To UserName -->
      <div class='input-group' style='margin-bottom: 25px'>
        <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i> </span>
        <?php echo Form::input(array('type' => 'text', 'name' => 'to_username', 'class' => 'form-control', 'value' => $data['to_username'], 'placeholder' => 'To Username', 'maxlength' => '100')); ?>
      </div>

      <!-- Subject -->
      <div class='input-group' style='margin-bottom: 25px'>
        <span class='input-group-addon'><i class='glyphicon glyphicon-book'></i> </span>
        <?php echo Form::input(array('type' => 'text', 'name' => 'subject', 'class' => 'form-control', 'value' => $data['subject'], 'placeholder' => 'Subject', 'maxlength' => '100')); ?>
      </div>

      <!-- Message Content -->
      <div class='input-group' style='margin-bottom: 25px'>
        <span class='input-group-addon'><i class='glyphicon glyphicon-pencil'></i> </span>
        <?php echo Form::textBox(array('type' => 'text', 'name' => 'content', 'class' => 'form-control', 'value' => $data['content'], 'placeholder' => 'Message Content', 'rows' => '6')); ?>
      </div>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
        <button class="btn btn-md btn-success" name="submit" type="submit">
          <?php // echo Language::show('update_profile', 'Auth'); ?>
          Send Message
        </button>
      <?php echo Form::close(); ?>

<?php
  // END Check to see if message form is disabled
  }
?>

		</div>
	</div>
</div>
