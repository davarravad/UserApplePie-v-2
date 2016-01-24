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
					<th colspan='2'>Message</th>
          <th><div align='center'><INPUT type='checkbox' onchange='checkAll(this)' name='msg_id[]' /></div></th>
				</tr>
				<?php
					if(!empty($data['messages'])){
            echo Error::display($error);
            echo Success::display($success);
            echo Form::open(array('method' => 'post'));
						foreach($data['messages'] as $row) {
							echo "<tr>";
              echo "<td align='center' valign='middle'>";
                //Check to see if message is new
                if($row->date_read == NULL){
                  // Unread
                  echo "<span class='glyphicon glyphicon-star' aria-hidden='true' style='font-size:25px; color:#419641'></span>";
                }else{
                  // Read
                  echo "<span class='glyphicon glyphicon-star-empty' aria-hidden='true' style='font-size:25px; color:#CCC'></span>";
                }
              echo "</td>";
              echo "<td><a href='".DIR."ViewMessage/$row->id'><b>Subject:</b> $row->subject</a><br>";
							echo $data['tofrom'];
              echo " <a href='".DIR."Profile/$row->username'>$row->username</a>";
							echo " &raquo; ";
							echo  date("F d, Y - g:i A",strtotime($row->date_sent));
              echo "</td>";
              echo "<td>";
              echo Form::input(array('type' => 'checkbox', 'name' => 'msg_id[]', 'class' => 'form-control', 'value' => $row->id));
              echo "</td>";
							echo "</tr>";
						}
            echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
            echo "</tr><td colspan='3'>";
            echo "<div class='col-lg-7 col-md-7 col-sm-7 pull-left' style='font-size: 12px'>";
              // Display Quta Info
              echo "<b>${data['what_box']} Quota:</b> ${data['quota_msg_percentage']}&#37; Full<br>";
              echo "<b>Total Messages:</b> ${data['quota_msg_ttl']} - <b>Limit:</b> ${data['quota_msg_limit']}";
            echo "</div>";
            echo "<div class='col-lg-5 col-md-5 col-sm-5 input-group pull-right'>";
              echo "<span class='input-group-addon'>Actions</span>";
              echo "<select class='form-control' id='actions' name='actions'>";
                echo "<option>Select Action</option>";
                // Check to see if using inbox - oubox mark as read is disabled
                if($data['inbox'] == "true"){
                  echo "<option value='mark_read'>Make as Read</option>";
                }
                echo "<option value='delete'>Delete</option>";
              echo "</select>";
              echo "<span class='input-group-btn'><button class='btn btn-success' name='submit' type='submit'>GO</button></span>";
            echo "</div>";
            echo "</td></tr>";
            echo "<tr><td colspan='3' align='center'>";
            echo $data['pageLinks'];
            echo "</td></tr>";
            echo Form::close();
					}else{
            echo "<tr><td>No Messages to Display</td></tr>";
          }
				?>
			</table>
		</div>
	</div>
</div>
