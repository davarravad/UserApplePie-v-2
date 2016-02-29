<?php
/**
 * Create the members view
 */

use Helpers\Form,
  Helpers\ErrorHelper,
  Helpers\SuccessHelper,
  Core\Success,
  Core\Language;

?>

<div class='col-lg-12 col-md-12'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'];  ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>

      <?php
            // Display Success and Error Messages if any (TODO: Move to header file)
          	echo ErrorHelper::display();
          	echo SuccessHelper::display();
          	echo ErrorHelper::display_raw($error);
          	echo SuccessHelper::display_raw($success);

            // Check to see if admin is editing a category
            if($data['edit_cat_main'] == true){
              // Display form with data to edit
              if(isset($data['data_cat_main'])){
                foreach ($data['data_cat_main'] as $row) {
                  echo "<div class='panel panel-primary'>";
                    echo "<div class='panel-body'>";
                      echo Form::open(array('method' => 'post'));
                        echo Form::input(array('type' => 'hidden', 'name' => 'prev_forum_title', 'value' => $row->forum_title));
                        echo Form::input(array('type' => 'hidden', 'name' => 'action', 'value' => 'update_cat_main_title'));
                        echo Form::input(array('type' => 'hidden', 'name' => 'csrf_token', 'value' => $data['csrf_token']));
                        echo "<div class='input-group'>";
                          echo "<span class='input-group-addon'><i class='glyphicon glyphicon-tower'></i> Main Category Title</span>";
                          echo Form::input(array('type' => 'text', 'name' => 'forum_title', 'class' => 'form-input text form-control', 'aria-describedby' => 'basic-addon1', 'value' => $row->forum_title, 'placeholder' => 'Main Category Title', 'maxlength' => '100'));
                          echo "<span class='input-group-btn'>";
                            echo "<button class='btn btn-success' name='submit' type='submit'>Update Main Category Title</button>";
                          echo "</span>";
                        echo "</div>";
                      echo Form::close();
                    echo "</div>";
                  echo "</div>";
                }
              }
            }else{
              // Display main categories for forum
              if(isset($data['cat_main'])){
                foreach($data['cat_main'] as $row){
                  echo "<div class='panel panel-primary'>";
                    echo "<div class='panel-body'>";
                      echo "<div class='col-lg-8 col-md-8'>";
                        echo $row->forum_title;
                      echo "</div>";
                      echo "<div class='col-lg-4 col-md-4' style='text-align: right'>";
                        // Check to see if object is at top
                        if($row->forum_order_title > 1){
                          echo "<a href='".DIR."AdminPanel-Forum-Categories/CatMainUp/$row->forum_order_title' class='btn btn-primary btn-xs' role='button'><span class='glyphicon glyphicon-triangle-top' aria-hidden='true'></span></a> ";
                        }
                        // Check to see if object is at bottom
                        if($data['fourm_cat_main_last'] != $row->forum_order_title){
                          echo "<a href='".DIR."AdminPanel-Forum-Categories/CatMainDown/$row->forum_order_title' class='btn btn-primary btn-xs' role='button'><span class='glyphicon glyphicon-triangle-bottom' aria-hidden='true'></span></a> ";
                        }
                        echo "<a href='".DIR."AdminPanel-Forum-Categories/CatMainEdit/$row->forum_id' class='btn btn-success btn-xs' role='button'><span class='glyphicon glyphicon-cog' aria-hidden='true'></span> Edit</a> ";
                        echo "Order ID: $row->forum_order_title";
                      echo "</div>";
                    echo "</div>";
                  echo "</div>";
                }// End of foreach
              }// End of isset
            }// End of action check

      ?>

		</div>
	</div>
</div>
