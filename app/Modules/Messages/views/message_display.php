<?php

// Display a given message as requested by post from inbox or outbox

use Core\Language;

if($data['msg_error'] == 'true'){$panelclass = "panel-danger";}else{$panelclass = "panel-default";}

?>

<div class='col-lg-8 col-md-8'>
	<div class='panel <?php echo $panelclass; ?>'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>
				<?php
					if(isset($data['message'])){
            echo "<table class='table table-bordered table-striped responsive'>";
						foreach($data['message'] as $row) {
							echo "<tr>";
              echo "<td>$row->subject</td>";
              echo "</tr><tr><td>";
              echo "<b>Date Sent:</b> ".date("F d, Y - g:i A",strtotime($row->date_sent))."<br>";
              // Check to see if message is marked as read yet
              if(isset($row->date_read)){
                echo "<b>Date Read:</b> ".date("F d, Y - g:i A",strtotime($row->date_read))."<br>";
              }
							echo "<b>From:</b> <a href='".DIR."Profile/$row->username'>$row->username</a>";
              echo "</td></tr><tr>";
							echo "<td>$row->content</td>";
							echo "</tr>";
						}
            echo "</table>";
					}
				?>
		</div>
	</div>
</div>
