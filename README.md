# UAP Version 2.0.0

We have moved on to v3 since the release of Nova Framework.  Check out our new github repo [UAP3](https://github.com/UserApplePie/UAP-MVC-CMS-3/)

[![Join the chat at https://gitter.im/davarravad/UserApplePie-v-2](https://badges.gitter.im/davarravad/UserApplePie-v-2.svg)](https://gitter.im/davarravad/UserApplePie-v-2?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)  

We are starting from scratch with UAP2.  This build is expected to take some time.  Check back soon for updates!

[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/simple-mvc-framework/v2/master/license.txt)

## What is UserApplePie v2.0.0?

Simple User Management System Based on MVC Framework. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of [helper classes](https://github.com/simple-mvc-framework/framework/tree/master/app/Helpers).

## Documentation

(Coming Soon!)Full docs & tutorials are available at [www.userapplepie.com](http://www.userapplepie.com).

## Requirements

The framework requirements are limited:

- [Apache Web Server](https://httpd.apache.org/) or equivalent with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) support
- [PHP 5.5 or greater](http://php.net/downloads.php) is required
- [MySQL database](http://www.mysql.com/). The framework can be changed to work with another database type.

## Installation

Tested on Windows 10 WAMP Server and Ubuntu 14.04

1. [Download](http://www.userapplepie.com/Downloads/) the framework.
2. Unzip the package to your web server.
3. Open `app/Core/Config.php` and set your base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do) and database credentials.
4. Edit `.htaccess` file and save the base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do).
5. Import the database.sql to your database (Updated table PREFIX if changed in Config.php).
6. Enjoy!

Note: The first user that registers for the site is set as admin.  Make sure you are first to sign up!

Note: Project is not complete, Stuff might be broken...

## UAP Default Site URL Map
Pages with Guest Access
 - Home (Welcome - Default)  
 - About  
 - subpage  
 - Login  
 - Register  
 - ForgotPassword  
 - ResendActivationEmail  
 - Members  
 - MembersOnline  
 - Profile/(UserName or UserID)  

Pages that Require User to be Logged In  
 - Logout  
 - AccountSettings  
 - EditProfile  
 - ChangePassword  
 - ChangeEmail  
 - PrivacySettings  

Pages that Are Used From Email Link  
 - ResetPassword  
 - Activate  

Pages within the Admin Panel
 - AdminPanel (Main Dashboard)
 - AdminPanel-Users
 - AdminPanel-User
 - AdminPanel-Groups
 - AdminPanel-Group
 - AdminPanel-Forum-Settings
 - AdminPanel-Forum-Categories

Pages For Messages Module
 - Messages (Messages Home Page)
 - MessagesInbox
 - MessagesOutbox
 - NewMessage (Create New Message/Reply)
 - ViewMessage/# (Displays Requested Message)

Pages For Forum Module (Disabled in Admin Panel by Default)
 - Forum (Forum Home Page)
 - Topics/# (Display Topics by Category ID)
 - NewTopic/# (Category ID Topic is Related to)
 - Topic/# (Topic and Replies Related to Topic ID)

## UAP Examples
Login
![Login](http://images.userapplepie.com/uap/uap2loginpage.jpg "Login")
Register
![Register](http://images.userapplepie.com/uap/uap2registerpage.jpg "Register")
User Profile
![User Profile](http://images.userapplepie.com/uap/uap2userprofile.jpg "User Profile")
Edit User Profile
![Edit User Profile](http://images.userapplepie.com/uap/uap2usereditprofile.jpg "Edit User Profile")

## Admin Panel Examples
Users List
![Admin Panel Users List](http://images.userapplepie.com/uap/uap2adminpanelpreview3.jpg "Admin Panel Users List")
User Edit
![Admin Panel User Edit](http://images.userapplepie.com/uap/uap2adminpanelpreview.jpg "Admin Panel User Edit")
Groups List
![Admin Panel Groups List](http://images.userapplepie.com/uap/uap2adminpanelpreview2.jpg "Admin Panel Groups List")
Group Edit
![Admin Panel Group Edit](http://images.userapplepie.com/uap/uap2adminpanelpreview4.jpg "Admin Panel Group Edit")

## Credit
Thank You to All Who Make UAP Possible!
- [Auth System](https://github.com/geomorillo/Auth) By: @geomorillo

## Built on Simple MVC Framework

![Simple MVC Framework](http://simplemvcframework.com/app/templates/publicthemes/smvc/images/logo.png)
