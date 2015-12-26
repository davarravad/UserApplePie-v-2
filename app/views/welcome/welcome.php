<?php
/**
 * Sample layout
 */

use Core\Language;

?>
<div class='col-lg-8 col-centered'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

			<a class="btn btn-md btn-success" href="<?php echo DIR;?>subpage">
				<?php echo Language::show('open_subpage', 'Welcome'); ?>
			</a>
		</div>
	</div>
</div>