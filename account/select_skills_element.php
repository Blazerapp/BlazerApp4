<?php include_once('warehouse.php');?>
<select class="select2" multiple data-placeholder="Choose a Skill Set..." style="max-width:420px">
			<?php
			$sql = "select * from `skills` order by name asc";
			$query = mysql_query($sql,$db);
			$rows = mysql_num_rows($query);
			while ($row = mysql_fetch_object($query))
			    echo "<option value=\"$row->id\">$row->name (avg:$$row->avg_salary)</option>";
?>
</select>