<?php
/**
 * Forum Topic View
 *
 * @author David "DaVaR" Sargent - davar@thedavar.net
 * @version 2.0
 * @date Jan 13, 2016
 * @date updated Jan 13, 2016
 */

use Core\Language,
  Helpers\ErrorHelper,
  Helpers\SuccessHelper,
  Helpers\Form,
  Helpers\TimeDiff,
  Helpers\CurrentUserData,
  Helpers\BBCode,
  Helpers\Sweets;

?>

<div class='col-lg-8 col-md-8'>

	<?php
	// Display Success and Error Messages if any (TODO: Move to header file)
	echo ErrorHelper::display();
	echo SuccessHelper::display();
	echo ErrorHelper::display_raw($error);
	echo SuccessHelper::display_raw($success);
	?>

	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
      <?php
          // Display Locked Message if Topic has been locked by admin
          if($data['topic_status'] == 2){
            echo " <strong><font color='red'>Topic Locked</font></strong> ";
          }
       ?>
		</div>
		<div class='panel-body'>
				<?php
        // Display Views Count
        echo "<div class='btn btn-xs btn-info'>Views <span class='badge'>".$data['PageViews']."</span></div>";
        // Display Total Sweets Count for Topic and All Replys
        echo Sweets::getTotalSweets($data['topic_id'], 'Forum_Topic', 'Forum_Topic_Reply');
        echo "<hr>";

        // Topic Display
    		echo "<div class='panel panel-default'>";
    			echo "<div class='panel-heading'>";
    				echo "<div class='row'>";
    					echo "<div class='col-lg-4 col-md-4 col-sm-4'>";
    						// Show user main pic
                // Get user name from userID
                $f_p_user_name = CurrentUserData::getUserName($data['topic_creator']);
    						echo " <a href='".DIR."Profile/".$data['topic_creator']."/'>$f_p_user_name</a> ";
    					echo "</div>";
    					echo "<div class='col-lg-4 col-md-4 col-sm-4' style='text-align:center'>";
    						//Show user's membership status
    						//get_up_info_mem_status($ID02);
    					echo "</div>";
    					echo "<div class='col-lg-4 col-md-4 col-sm-4' style='text-align:right'>";
    						// Display how long ago this was posted
                $data_topic_date = $data['topic_date'];
    						echo "<font color=green> " . TimeDiff::dateDiff("now", "$data_topic_date", 1) . " ago</font> ";
    					echo "</div>";
    				echo "</div>";
    			echo "</div>";
          //Format the content with bbcode
  				$data_topic_content = BBCode::getHtml($data['topic_content']);
  			echo "<div class='panel-body forum'>";
          if($data['action'] == "edit_topic" && $data['current_userID'] == $data['topic_creator']){
            echo "<font color='green' size='0.5'><b>Editing Topic</b></font>";
            echo Form::open(array('method' => 'post'));
            echo Form::input(array('type' => 'text', 'name' => 'forum_title', 'class' => 'form-control', 'value' => $data['title'], 'placeholder' => 'Topic Title', 'maxlength' => '100'));
            echo Form::textBox(array('type' => 'text', 'name' => 'forum_content', 'class' => 'form-control', 'value' => $data['topic_content'], 'placeholder' => 'Topic Content', 'rows' => '6'));
            // Topic Reply Edit True
            echo "<input type='hidden' name='action' value='update_topic' />";
            // CSRF Token
            echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
            // Display Submit Button
            echo "<button class='btn btn-xs btn-success' name='submit' type='submit'>Update Topic</button>";
            echo Form::close();
          }else{
  				  echo $data_topic_content;
          }
  			echo "</div>";
  			echo "<div class='panel-footer'>";
  				echo "<div class='row'>";
  					echo "<div class='col-lg-6 col-md-6 col-sm-6' style='text-align:left'>";
  						if($data['topic_edit_date'] != NULL){
  							// Display how long ago this was posted
  							$timestart = $data['topic_edit_date'];  //Time of post
  							echo " <font color=red>Edited</font><font color=red> " . TimeDiff::dateDiff("now", "$timestart", 1) . " ago</font> ";
  						}
  					echo "</div>";
  					echo "<div class='col-lg-6 col-md-6 col-sm-6' style='text-align:right'>";
  						//Start Sweet
              $sweet_url = "Topic/".$data['topic_id'];
              echo Sweets::displaySweetsButton($data['topic_id'], 'Forum_Topic', $data['current_userID'], "0", $sweet_url);
              echo Sweets::getSweets($data['topic_id'], 'Forum_Topic');
              // If user owns this content show forum buttons for edit and delete
              // Hide button if they are currently editing this topic or any replys
              if($data['action'] != "edit_reply" && $data['action'] != "edit_topic" && $data['current_userID'] == $data['topic_userID']){
                echo Form::open(array('method' => 'post', 'style' => 'display:inline'));
                // Topic Reply Edit True
                echo "<input type='hidden' name='action' value='edit_topic' />";
                // CSRF Token
                echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
                // Display Submit Button
                echo "<button class='btn btn-xs btn-info' name='submit' type='submit'>Edit Topic</button>";
                echo Form::close();
              }
  					echo "</div>";
  				echo "</div>"; // End row
  			echo "</div>";
  		echo "</div>";  // END panel

      // Display Paginator Links
      // Check to see if there is more than one page
      if($data['pageLinks'] > "1"){
        echo "<div class='panel panel-info'>";
          echo "<div class='panel-heading text-center'>";
            echo $data['pageLinks'];
          echo "</div>";
        echo "</div>";
      }

        foreach($data['topic_replys'] as $row)
      	{
          $rf_p_main_id = $row->id;
          $rf_p_id = $row->fpr_post_id;
          $rf_p_id_cat = $row->fpr_id;
          $rf_p_content = $row->fpr_content;
          $rf_p_edit_date = $row->fpr_edit_date;
          $rf_p_timestamp = $row->fpr_timestamp;
          $rf_p_user_id = $row->fpr_user_id;
          $rf_p_user_name = CurrentUserData::getUserName($rf_p_user_id);
          //$rf_p_content = stripslashes($rf_p_content);

          echo "<a class='anchor' name='topicreply$rf_p_main_id'></a>";

					// Reply Topic Display
					echo "<div class='panel panel-info'>";
						echo "<div class='panel-heading'>";
							echo "<div class='row'>";
								echo "<div class='col-lg-4 col-md-4 col-sm-4'>";
									echo " Reply By: ";
									// Show user main pic
									echo " <a href='".DIR."Profile/$rf_p_user_id/'>$rf_p_user_name</a> ";
								echo "</div>";
								echo "<div class='col-lg-4 col-md-4 col-sm-4' style='text-align:center'>";
									//Show user's membership status
									//get_up_info_mem_status($rf_p_user_id);
								echo "</div>";
								echo "<div class='col-lg-4 col-md-4 col-sm-4' style='text-align:right'>";
									// Display how long ago this was posted
									$timestart = "$rf_p_timestamp";  //Time of post
									echo "<font color=green> " . TimeDiff::dateDiff("now", "$timestart", 1) . " ago</font> ";
								echo "</div>";
							echo "</div>";
						echo "</div>";
						echo "<div class='panel-body forum'>";
							//Format the content with bbcode
							$rf_p_content_bb = BBCode::getHtml($rf_p_content);
              // Check to see if user is trying to edit this reply
              // Make sure user owns this reply before they can edit it
              // Make sure this is the reply user is trying to edit
              if($data['action'] == "edit_reply" && $data['current_userID'] == $rf_p_user_id && $data['edit_reply_id'] == $rf_p_main_id){
                echo "<font color='green' size='0.5'><b>Editing Topic Reply</b></font>";
                echo Form::open(array('method' => 'post', 'action' => '#topicreply'.$rf_p_main_id));
                echo Form::textBox(array('type' => 'text', 'name' => 'fpr_content', 'class' => 'form-control', 'value' => $rf_p_content, 'placeholder' => 'Topic Reply Content', 'rows' => '6'));
                // Topic Reply Edit True
                echo "<input type='hidden' name='action' value='update_reply' />";
                // Topic Reply ID for editing
                echo "<input type='hidden' name='edit_reply_id' value='".$rf_p_main_id."' />";
                // CSRF Token
                echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
                // Display Submit Button
                echo "<button class='btn btn-xs btn-success' name='submit' type='submit'>Update Reply</button>";
                echo Form::close();
              }else{
							  echo "$rf_p_content_bb";
              }
						echo "</div>";
						echo "<div class='panel-footer' style='text-align:right'>";
							echo "<div class='row'>";
								echo "<div class='col-lg-6 col-md-6 col-sm-6' style='text-align:left'>";
									if($rf_p_edit_date != NULL){
										// Display how long ago this was posted
										$timestart = "$rf_p_edit_date";  //Time of post
										echo " <font color=red>Edited</font> <font color=red> " . TimeDiff::dateDiff("now", "$timestart", 1) . " ago</font> ";
									}
								echo "</div>";
								echo "<div class='col-lg-6 col-md-6 col-sm-6' style='text-align:right'>";
									//Start Sweet
                  $sweet_url = "Topic/".$data['topic_id']."/".$data['current_page']."/#topicreply".$rf_p_main_id;
                  echo Sweets::displaySweetsButton($data['topic_id'], 'Forum_Topic_Reply', $data['current_userID'], $rf_p_main_id, $sweet_url);
                  echo Sweets::getSweets($data['topic_id'], 'Forum_Topic_Reply', $rf_p_main_id);
                  // If user owns this content show forum buttons for edit and delete
                  // Hide button if they are currently editing this reply
                  if($data['action'] != "edit_reply" && $data['action'] != "edit_topic" && $data['current_userID'] == $rf_p_user_id){
                    echo Form::open(array('method' => 'post', 'action' => '#topicreply'.$rf_p_main_id, 'style' => 'display:inline'));
                    // Topic Reply Edit True
                    echo "<input type='hidden' name='action' value='edit_reply' />";
                    // Topic Reply ID for editing
                    echo "<input type='hidden' name='edit_reply_id' value='".$rf_p_main_id."' />";
                    // CSRF Token
                    echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
                    // Display Submit Button
                    echo "<button class='btn btn-xs btn-info' name='submit' type='submit'>Edit Reply</button>";
                    echo Form::close();
                  }
								echo "</div>";
							echo "</div>"; // End row
						echo "</div>";
					echo "</div>";

      	}

        // Display Locked Message if Topic has been locked by admin
        if($data['topic_status'] == 2){
          echo " <strong><font color='red'>Topic Locked - Replies are Disabled</font></strong> ";
        }else{
          // Display Create New Topic Reply Button if user is logged in
          if(isset($data['current_userID'])){
?>
            <hr>
            <?php echo Form::open(array('method' => 'post')); ?>

            <!-- Topic Reply Content -->
            <div class='input-group' style='margin-bottom: 25px'>
              <span class='input-group-addon'><i class='glyphicon glyphicon-pencil'></i> </span>
              <?php echo Form::textBox(array('type' => 'text', 'name' => 'fpr_content', 'class' => 'form-control', 'value' => $data['fpr_content'], 'placeholder' => 'Topic Reply Content', 'rows' => '6')); ?>
            </div>

              <!-- CSRF Token -->
              <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
              <input type='hidden' name='action' value='new_reply' />
              <button class="btn btn-md btn-success" name="submit" type="submit">
                <?php // echo Language::show('update_profile', 'Auth'); ?>
                Submit New Reply
              </button>
            <?php echo Form::close(); ?>
            <hr>
<?php
          }
        }

        // Display Paginator Links
        // Check to see if there is more than one page
        if($data['pageLinks'] > "1"){
          echo "<div class='panel panel-info'>";
            echo "<div class='panel-heading text-center'>";
              echo $data['pageLinks'];
            echo "</div>";
          echo "</div>";
        }

        // Display Admin Lock/UnLock Button
        // Check if Admin
        if($data['is_admin'] == true){
          echo Form::open(array('method' => 'post'));
            if($data['topic_status'] == 2){
              // UnLock Button
              echo "<input type='hidden' name='action' value='unlock_topic' />";
              echo "<button class='btn btn-xs btn-warning' name='submit' type='submit'>UnLock Topic</button>";
            }else{
              // Lock Button
              echo "<input type='hidden' name='action' value='lock_topic' />";
              echo "<button class='btn btn-xs btn-danger' name='submit' type='submit'>Lock Topic</button>";
            }
          // CSRF Token
          echo "<input type='hidden' name='csrf_token' value='".$data['csrf_token']."' />";
          echo Form::close();
        }
				?>
		</div>
	</div>
</div>
