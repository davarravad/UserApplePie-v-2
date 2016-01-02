<?php
/**
 * Sample layout
 */

use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;
use Helpers\PageFunctions;

//initialise hooks
$hooks = Hooks::get();

// Check to see what page is being viewed
// If not Home, Login, Register, etc.. 
// Send url to Session
PageFunctions::prevpage();

?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<?php
	//hook for plugging in meta tags
	$hooks->run('meta');
	?>
	<title><?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in app/Core/Config.php ?></title>

	<!-- CSS -->
	<?php
	Assets::css(array(
		'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
		Url::templatePath() . 'css/style.css',
	));

	//hook for plugging in css
	$hooks->run('css');
	?>

</head>
<body>
   <nav class='navbar navbar-default navbar-fixed-top'>
      <div class='container-fluid'>
        <div class='navbar-header'>
          <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </button>
		 <a class='navbar-brand' href='<?php echo DIR; ?>' title='Home'>
          <img style='max-height: 20px; border-radius: 5px' alt='Brand' src='/app/templates/default/images/logo.gif'>
         </a>
         <a class='navbar-brand' href='<?php echo DIR; ?>' title='Home'>UserApplePie</a>
        </div>
		
		<!-- Collect Left Main Links -->
        <div id='navbar' class='navbar-collapse collapse'>
          <ul class='nav navbar-nav'>
			<li><a href='<?php echo DIR; ?>About'>About</a></li>
		  </ul>
		  <ul class='nav navbar-nav navbar-right'>
				<?php if(ISLOGGEDIN != 'true'){ ?>
					<li><a href='<?php echo DIR; ?>Login'>Login</a></li>
					<li><a href='<?php echo DIR; ?>Register'>Register</a></li>
				<?php }else{ ?>
					<li class='dropdown'>
						<a href='#' title='<?php echo CUR_USERNAME; ?>' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>
						<span class='glyphicon glyphicon-user' aria-hidden='true'></span> <?php echo CUR_USERNAME; ?> <span class='caret'></span> </a>
						<ul class='dropdown-menu'>
							<li><a href='<?php echo DIR; ?>ChangePassword' title='Change Your Account Settings'> <span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Change Password</a></li>
							<li><a href='<?php echo DIR; ?>ChangeEmail' title='Change Your Account Settings'> <span class='glyphicon glyphicon-envelope' aria-hidden='true'></span> Change Email</a></li>
						</ul>
						<li><a href='<?php echo DIR; ?>Logout'>Logout</a></li>
					</li>
				<?php } ?>
		  </ul>
		</div>
	  </div>
   </nav>

<!-- Create Spacer For Navbar -->
<div class='visible-lg visible-md visible-sm' style='height: 70px'></div>
<div class='visible-xs' style='height: 110px'></div>
   
<?php
//hook for running code after body tag
$hooks->run('afterBody');
?>
<div class="container">
