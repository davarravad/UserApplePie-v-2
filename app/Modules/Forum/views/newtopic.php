<?php
/**
 * Forum New Topic View
 *
 * @author David "DaVaR" Sargent - davar@thedavar.net
 * @version 2.0
 * @date Jan 13, 2016
 * @date updated Jan 13, 2016
 */

use Core\Language,
  Helpers\ErrorHelper,
  Helpers\SuccessHelper,
  Helpers\Form,
  Helpers\TimeDiff,
  Helpers\CurrentUserData,
  Helpers\BBCode;

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
				<?php echo $data['welcome_message']; ?>


            <?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>

            <!-- Topic Title -->
            <div class='input-group' style='margin-bottom: 25px'>
              <span class='input-group-addon'><i class='glyphicon glyphicon-book'></i> </span>
              <?php echo Form::input(array('type' => 'text', 'name' => 'forum_title', 'class' => 'form-control', 'value' => $data['forum_title'], 'placeholder' => 'Topic Title', 'maxlength' => '100')); ?>
            </div>

            <!-- Topic Content -->
            <div class='input-group' style='margin-bottom: 25px'>
              <span class='input-group-addon'><i class='glyphicon glyphicon-pencil'></i> </span>
              <?php echo Form::textBox(array('type' => 'text', 'name' => 'forum_content', 'class' => 'form-control', 'value' => $data['forum_content'], 'placeholder' => 'Topic Content', 'rows' => '6')); ?>
            </div>

            <!-- Image Upload -->
            <div class='input-group' style='margin-bottom: 25px'>
              <span class='input-group-addon'><i class='glyphicon glyphicon-picture'></i> </span>
              <?php echo Form::input(array('type' => 'file', 'name' => 'forumImage', 'id' => 'forumImage', 'class' => 'form-control', 'accept' => 'image/jpeg,image/png,image/gif')); ?>
            </div>

              <!-- CSRF Token -->
              <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
              <button class="btn btn-md btn-success" name="submit" type="submit">
                <?php // echo Language::show('update_profile', 'Auth'); ?>
                Submit New Topic
              </button>
            <?php echo Form::close(); ?>


		</div>
	</div>
</div>
