<?php
/**
 * Sample layout
 */

use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;
use Helpers\PageFunctions;
use Helpers\CurrentUserData;

//initialise hooks
$hooks = Hooks::get();

// Check to see what page is being viewed
// If not Home, Login, Register, etc.. 
// Send url to Session
PageFunctions::prevpage();

// Get user data if logged in
$cur_userID = CUR_LOGGED_USERID;
// Get User Data From Array
// Get user data from user's database
if(isset($cur_userID)){
	$current_user_data = CurrentUserData::getCUD($cur_userID);
	foreach($current_user_data as $user_data){
		$cu_username = $user_data->username;
		$cu_first_name = $user_data->firstName;
		$cu_gender = $user_data->gender;
		$cu_email = $user_data->email;
		$cu_lastlogin = date("F d, Y",strtotime($user_data->LastLogin));
		$cu_signup = date("F d, Y",strtotime($user_data->SignUp));
		$cu_img = $user_data->userImage;
		$cu_aboutme = $user_data->aboutme;
		$cu_website = $user_data->website;
	}
}

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
	
	// Setup the favicon
	echo "<link rel='shortcut icon' href='".Url::templatePath() ."images/favicon.ico'>";
	
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
				<img style='max-height: 20px; border-radius: 5px' alt='Brand' src='<?php echo Url::templatePath();?>images/logo.gif'>
				</a>
				<a class='navbar-brand' href='<?php echo DIR; ?>' title='Home'>UserApplePie</a>
			</div>

			<!-- Collect Left Main Links -->
			<div id='navbar' class='navbar-collapse collapse'>
				<ul class='nav navbar-nav'>
				<li><a href='<?php echo DIR; ?>About'>About</a></li>
				</ul>
				<ul class='nav navbar-nav navbar-right'>
				<?php if(ISLOGGEDIN != "true"){ ?>
				<li><a href='<?php echo DIR; ?>Login'>Login</a></li>
				<li><a href='<?php echo DIR; ?>Register'>Register</a></li>
				<?php }else{ ?>
				<li class='dropdown'>
				<a href='#' title='<?php echo $cu_username; ?>' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>
				<span class='glyphicon glyphicon-user' aria-hidden='true'></span> <?php echo $cu_username; ?> <span class='caret'></span> </a>
					<ul class='dropdown-menu'>
						<li>
							<div class="navbar-login">
								<div class="row">
									<div class="col-lg-4 col-md-4" align="center">
										<div class="col-centered" align="center">
										<?php // Check to see if user has a profile image
											if(!empty($cu_img)){
												echo "<img src='".$cu_img."' class='img-responsive'>";
											}else{
												echo "<span class='glyphicon glyphicon-user icon-size'></span>";
											}
										?>
										</div>
									</div>
									<div class="col-lg-8 col-md-8">
										<p class="text-left"><strong><h5><?php echo $cu_username; if(isset($cu_first_name)){echo "  <small>".$cu_first_name."</small>";}?></h5></strong></p>
										<p class="text-left small"><?php echo $cu_email; ?></p>
										<p class="text-left">
											<a href='<?php echo DIR."Profile/".$cu_username; ?>' title='View Your Profile' class='btn btn-primary btn-block btn-xs'> <span class='glyphicon glyphicon-user' aria-hidden='true'></span> View Profile</a>
										</p>
									</div>
								</div>
							</div>
                        <li class="divider"></li>
                        <li>
                            <div class="navbar-login navbar-login-session">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p>
											<a href='<?php echo DIR; ?>AccountSettings' title='Change Your Account Settings' class='btn btn-info btn-block btn-xs'> <span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Account Settings</a>
											<!-- <a href='<?php echo DIR; ?>PrivacySettings' title='Change Your Privacy Settings' class='btn btn-warning btn-block btn-xs'> <span class='glyphicon glyphicon-envelope' aria-hidden='true'></span> Privacy Settings</a>-->
                                        </p>
                                    </div>
                                </div>
                            </div>
						</li>
					</ul>
				<li><a href='<?php echo DIR; ?>Logout'>Logout</a></li>
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

<!-- BreadCrumbs -->
<?php
// Display Breadcrumbs if set
if(isset($data['breadcrumbs'])){
	echo "<div class='row'>";
		echo "<div class='col-lg-12 col-md-12'>";
			echo "<ol class='breadcrumb'>";
				echo $data['breadcrumbs'];
			echo "</ol>";
		echo "</div>";
	echo "</div>";
}
?>

<div class='row'>

<!-- Left Sidebar -->
<?php
// Display Left Links Bar if one set
if(isset($data['left_sidebar'])){
	echo $data['left_sidebar'];
}
?>