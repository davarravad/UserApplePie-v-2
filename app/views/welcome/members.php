<?php
/**
 * Create the members view
 */

use Core\Language;

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
					<th>UserName</th>
					<th>FirstName</th>
					<th>Group</th>
				</tr>
				<?php
					if(isset($data['members'])){
						foreach($data['members'] as $row) {
							if(isset($row->groupFontColor)){ $font_color = " color='$row->groupFontColor' "; }else{$font_color="";}
							if(isset($row->groupFontWeight)){ $font_weight = " style='font-weight:$row->groupFontWeight' "; }else{$font_weight="";}
							echo "<tr>";
							echo "<td><a href='".DIR."Profile/$row->username'>$row->username</a></td>";
							echo "<td>$row->firstName</td>";
							echo "<td><font $font_color $font_weight>$row->groupName</font></td>";
							echo "</tr>";
						}
					}
				?>
			</table>
		</div>
	</div>
</div>