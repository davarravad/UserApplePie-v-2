<?php

use Helpers\CurrentUserData,
    Helpers\Url;

// Get user data if logged in
$cur_userID = CUR_LOGGED_USERID;

// Make sure user is logged in
if(isset($cur_userID)){
	// Get user's group status
	$current_user_groups = CurrentUserData::getCUGroups($cur_userID);
	foreach($current_user_groups as $user_group_data){
		$cu_groupID[] = $user_group_data->groupID;
	}
	// Get User Data From Array
	// Get user data from user's database
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

// Check to make sure user can view admin panel
if(!ctype_digit($cur_userID) && !ctype_digit($cu_groupID)){
  // Redirect member to home page
  Url::redirect(); die();
}else if(!in_array(4,$cu_groupID)){
    // Redirect member to home page
    Url::redirect(); die();
}

?>

<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content='Administrator Panel'>
    <meta name='author' content=''>

    <title>Admin Panel - <?php echo $data['title'] ?></title>

    <!-- Bootstrap Core CSS -->
    <link href='/app/Modules/AdminPanel/views/css/bootstrap.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link href='/app/Modules/AdminPanel/views/css/sb-admin.css' rel='stylesheet'>

    <!-- Morris Charts CSS -->
    <link href='/app/Modules/AdminPanel/views/css/plugins/morris.css' rel='stylesheet'>

    <!-- Custom Fonts -->
    <link href='/app/Modules/AdminPanel/views/font-awesome/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js'></script>
        <script src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js'></script>
    <![endif]-->

</head>

<body>

  <div id='wrapper'>

    <!-- Navigation -->
    <nav class='navbar navbar-inverse navbar-fixed-top' role='navigation'>
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class='navbar-header'>
        <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
          <span class='sr-only'>Toggle navigation</span>
          <span class='icon-bar'></span>
          <span class='icon-bar'></span>
          <span class='icon-bar'></span>
        </button>
        <a class='navbar-brand' href='<?php echo DIR; ?>AdminPanel'><i class='glyphicon glyphicon-cog'></i> Admin Panel</a>
      </div>
      <!-- Top Menu Items -->
      <ul class='nav navbar-right top-nav'>
        <li class='dropdown'>
          <a href='#' class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-user'></i> <?php echo $cu_username ?> <b class='caret'></b></a>
          <ul class='dropdown-menu'>
            <li>
              <a href='<?php echo DIR; ?>'><i class='fa fa-fw fa-power-off'></i> Main Site</a>
            </li>
          </ul>
        </li>
      </ul>
      <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
      <div class='collapse navbar-collapse navbar-ex1-collapse'>
        <ul class='nav navbar-nav side-nav'>
          <li <?php if($data['current_page'] == "/AdminPanel"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel'><i class='fa fa-fw fa-dashboard'></i> Dashboard</a>
          </li>
   <!-- // Pages on the todo list
          <li <?php if($data['current_page'] == "reports"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/reports'><i class='glyphicon glyphicon-info-sign'></i> Support Tickets</a>
          </li>
          <li <?php if($data['current_page'] == "errors"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/errors'><i class='glyphicon glyphicon-alert'></i> Site Errors</a>
          </li>
          <li <?php if($data['current_page'] == "admin_configuration"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/admin_configuration'><i class='glyphicon glyphicon-wrench'></i> Site Configuration</a>
          </li>
    -->
          <li <?php if($data['current_page'] == "/AdminPanel-Users"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel-Users'><i class='glyphicon glyphicon-user'></i> Users</a>
          </li>
          <li <?php if($data['current_page'] == "/AdminPanel-Groups"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel-Groups'><i class='glyphicon glyphicon-book'></i> Groups</a>
          </li>
    <!-- Todo pages
          <li <?php if($data['current_page'] == "admin_pages"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/admin_pages'><i class='glyphicon glyphicon-file'></i> Pages</a>
          </li>
          <li <?php if($data['current_page'] == "adminmessage"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/adminmessage'><i class='fa fa-fw fa-desktop'></i> Site Message</a>
          </li>
          <li <?php if($data['current_page'] == "admin_wm"){echo "class='active'";} ?>  >
            <a href='<?php echo DIR; ?>AdminPanel/admin_wm'><i class='glyphicon glyphicon-envelope'></i> Register Message</a>
          </li>
          <li>
            <a href='javascript:;' data-toggle='collapse' data-target='#demo'><i class='fa fa-fw fa-arrows-v'></i> Forum <i class='fa fa-fw fa-caret-down'></i></a>
            <ul id='demo' class='collapse'>
              <li>
                <a href='#'>Coming Soon!</a>
              </li>
              <li>
                <a href='#'>Coming Soon!</a>
              </li>
            </ul>
          </li>
    -->
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </nav>

        <div id='page-wrapper'>

      <div class='container-fluid'>

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
