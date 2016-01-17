<?php
/**
 * Create the members view
 */

use Core\Language;

$orderby = $data['orderby'];

?>
<div class='col-lg-12 col-md-12'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>
			<table class='table table-hover responsive'>
				<tr>
					<th>
            <?php
              if(empty($data['orderby'])){
                $ob_value = "ID-DESC";
                $ob_icon = "";
              }
              else if($data['orderby'] == "ID-DESC"){
                $ob_value = "ID-ASC";
                $ob_icon = "<i class='glyphicon glyphicon-triangle-bottom'></i>";
              }
              else if($data['orderby'] == "ID-ASC"){
                $ob_value = "ID-DESC";
                $ob_icon = "<i class='glyphicon glyphicon-triangle-top'></i>";
              }
                // Setup the order by id button
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='orderby' value='$ob_value'>";
                echo "<button type='submit' class='btn btn-info btn-sm'>ID $ob_icon</button>";
                echo "</form>";
            ?>
          </th>
					<th>
            <?php
              if(empty($data['orderby'])){
                $obu_value = "UN-DESC";
                $obu_icon = "";
              }
              else if($data['orderby'] == "UN-DESC"){
                $obu_value = "UN-ASC";
                $obu_icon = "<i class='glyphicon glyphicon-triangle-bottom'></i>";
              }
              else if($data['orderby'] == "UN-ASC"){
                $obu_value = "UN-DESC";
                $obu_icon = "<i class='glyphicon glyphicon-triangle-top'></i>";
              }
                // Setup the order by id button
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='orderby' value='$obu_value'>";
                echo "<button type='submit' class='btn btn-info btn-sm'>UserName $obu_icon</button>";
                echo "</form>";
            ?>
          </th>
          <th>FirstName</th>
          <th>LastLogin</th>
				</tr>
				<?php
					if(isset($data['users_list'])){
						foreach($data['users_list'] as $row) {
							echo "<tr>";
              echo "<td>$row->userID</td>";
							echo "<td><a href='".DIR."AdminPanel-User/$row->userID'>$row->username</a></td>";
							echo "<td>$row->firstName</td>";
              echo "<td>".date("F d, Y",strtotime($row->LastLogin))."</td>";
							echo "</tr>";
						}
					}
				?>
			</table>
		</div>
	</div>
</div>
