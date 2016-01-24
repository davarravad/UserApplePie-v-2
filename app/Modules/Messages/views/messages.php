<?php

// Displays a list of all message for current user
// Only displays From Subject, Status, Date Read, Date Sent

use Core\Language,
  Core\Error,
  Core\Success,
  Helpers\Form;

?>

<div class='col-lg-8 col-md-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>
			<table class='table table-striped table-hover table-bordered responsive'>
				<tr>
					<th colspan='2'>Private Messages</th>
				</tr>
        <tr><td>
          You have <?php echo $data['unread_messages'] ?> Unread Messages
        <br>
          You have <?php echo $data['total_messages'] ?> Messages in your Inbox
        </td></tr>
			</table>
		</div>
	</div>
</div>
