<?php
/**
 * Sample layout
 */

use Core\Language;

?>
<div class='col-lg-8 col-md-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

			<a class="btn btn-md btn-success" href="<?php echo DIR;?>">
				<?php echo Language::show('back_home', 'Welcome'); ?>
			</a>
		</div>
	</div>
</div>