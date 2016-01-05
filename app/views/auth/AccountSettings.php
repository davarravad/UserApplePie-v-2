<?php 
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language; 	
?>

<div class='col-lg-8 col-md-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title']; ?></h3>
		</div>
		<div class='panel-body'>
			<!-- Display Account Basic Information -->
			<?php echo Error::display($error); ?>
			<?php echo Success::display($success); ?>
			<?php echo $data['welcome_message']; ?>
			<!-- End Display -->
		</div>
	</div>
</div>