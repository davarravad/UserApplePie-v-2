<?php
/**
 * Create the members view
 */

use Helpers\Form,
  Core\Error,
  Core\Success,
  Core\Language;

?>

<div class='col-lg-12 col-md-12'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'];  ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

			<?php echo Error::display($error); ?>
			<?php echo Success::display($success); ?>


		</div>
	</div>
</div>
