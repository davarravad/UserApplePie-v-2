<?php
/**
 * Sample layout
 */

use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;
use Models\RightLinks;

//initialise hooks
$hooks = Hooks::get();

// Display Right Links Bar if one set
if(isset($data['sidebar'])){
	echo $data['sidebar'];
}
?>

<!-- Footer goods -->
</div> <!-- End of row -->
</div> <!-- End of Container -->
<div class='container'>
	<div class='row'>
		<div class='col-lg-12'>
			<!-- Footer (sticky) -->
			<footer class='navbar navbar-default'>
				<div class='container'>
					<div class='navbar-text'>

						<!-- Footer links / text -->
						<a href='http://www.userapplepie.com' title='View UserApplePie Website' ALT='UserApplePie' target='_blank'>UserApplePie</a>

						<!-- Display Copywrite stuff with auto year -->
						<Br> &copy; <?php echo date("Y") ?> <?php echo SITETITLE;?> All Rights Reserved.
					</div>
				</div>
			</footer>
		</div>
	</div>
</div>

<!-- JS -->
<?php
echo $data['js'];

Assets::js(array(
	Url::templatePath() . 'js/jquery.js',
	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
));

//hook for plugging in javascript
$hooks->run('js');

//hook for plugging in code into the footer
$hooks->run('footer');
?>

</body>
</html>
