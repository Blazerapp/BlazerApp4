<?php
/*

UserFrosting Version: 0.2.2
By Alex Weissman
Copyright (c) 2014

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

require_once("../models/config.php");
include('warehouse.php');
if ($_GET['reset']) @mysql_query("delete from matches where user_id_a = 1;",$db);

// Request method: GET
$ajax = checkRequestMode("get");

if (!securePage(__FILE__)){
    apiReturnError($ajax);
}

setReferralPage(getAbsoluteDocumentPath(__FILE__));

?>

<!DOCTYPE html>
<html lang="en">
  <?php
  	echo renderAccountPageHeader(array("#SITE_ROOT#" => SITE_ROOT, "#SITE_TITLE#" => SITE_TITLE, "#PAGE_TITLE#" => "Browse Jobs"));
  ?>

  <body>


    <div id="wrapper">

      <!-- Sidebar -->
        <?php
          echo renderMenu("dashboard");
        ?>  

     		 <div id="page-wrapper">

<?php if (!($_GET['yesTo']) && (!$_GET['noTo'])) { ?>

		       <div class="row">
				<div>
		            <div class="alert alert-success alert-dismissable">
		              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		              Searching for Jobs. Swipe Left to Pass, Swipe right to Apply to Job
		            </div>
		          </div>
		        </div><!-- /.row -->
		
<?php }

else

{
	$user_id_a = $loggedInUser->user_id;
	$value = 0;
	$user_id_b = $_GET['noTo'];
	if ($_GET['yesTo']) { $value = 1; $user_id_b = $_GET['yesTo']; }
	mysql_query("replace into matches (user_id_a, user_id_b, value) VALUES ($user_id_a,$user_id_b,$value)",$db);
	
	$query = mysql_query("select * from uf_users, matches where user_id_a = uf_users.id and user_id_a = $user_id_b and user_id_b = $user_id_a and value = 1",$db);
	if (mysql_num_rows($query) && $value == 1)
	{
		$row = mysql_fetch_object($query);
?>

    <div class="row">
		<div>
         <div class="alert alert-success alert-dismissable">
           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
           You have matched with <b><?php echo $row->display_name; ?></b>! <p>Go to <a href="matches.php">Your Matches</a> and start a discussion.</p>
         </div>
       </div>
     </div><!-- /.row -->

<?php		
	}
	
}

 ?>

		        <div class="row">
		            <div id="swipe" class="alert alert-info">


<?php



$user_id = $loggedInUser->user_id;

$query = mysql_query("select * from user_types where user_id = $user_id",$db);
$row = mysql_fetch_object($query);
$type = $row->type_id;

$query = mysql_query("select * from uf_users, user_types where uf_users.id = user_types.user_id and type_id != $type and uf_users.id NOT IN (select user_id_b from matches where user_id_a = $user_id)",$db);
$row = mysql_fetch_object($query);

$profile = mysql_query("select * from profile where user_id = $row->id",$db);
$profile = mysql_fetch_object($profile);

$query2 = mysql_query("select * from skills_to_users, skills where skills_to_users.user_id = $row->id and skills_to_users.skill_id = skills.id",$db);

if (!mysql_num_rows($query))
{
	echo "<p>Searching for jobs in your area:</p><br><p>No results found. Refresh page or try again later</p><br><p>Go to <a href=\"matches.php\">Your Matches</a></p><br><p>Click here to <a href=\"?reset=1\">reset</a> the Blazer App demo data</p>";
}
else
{
	echo "<center>";
	echo "<p><img src=\"$profile->logo_url\" style=\"max-height: 100px; max-width:200px;\"/></p>";
	echo "<h2>$profile->Position</h2>";
	echo "<p><small>$row->display_name</small></p>";
	
	echo "<table width=420><tr><td width=140><center><span class=\"fa fa-globe\" aria-hidden=\"true\"></span><br>$profile->Location</center></td><td width=140><center><span class=\"fa fa-briefcase\" aria-hidden=\"true\"></span><br>3-5 Years</center></td><td width=140><center><span class=\"fa fa-money\" aria-hidden=\"true\"></span><br>$profile->Salary</center></td></tr></table>";
	
	echo "<table width=420><tr><td width=420>";
	
	echo "<br><p>$row->display_name looking to hire and fill positions for the following skill sets:</p>";
	$salary = array();
	while ($row2 = mysql_fetch_object($query2))
	{
		echo "<li>$row2->name</li>";
		$salary[] = $row2->avg_salary;
	}
	$avg =  array_sum($salary) / count($salary);
	
	echo "</td></tr></table>";
	
	echo "<br><p>Occupation salary statistics: <b>$".number_format($avg,0)."</b></p>";
	

	?>
			<br><br>
			<button type="button" class="btn btn-danger btn-lg" id="no">
			  <span class="fa fa-times" aria-hidden="true"></span>
			</button>

			<button type="button" class="btn btn-success btn-lg" id="yes">
			  <span class="fa fa-check" aria-hidden="true"></span>
			</button>
			</center>
	<?php
} ?>



		            </div>
	</div>

      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->

  <script>
      $(document).ready(function(){
		  $("#swipe").on("swiperight",function(){
		    window.location = '?yesTo=<?php echo $row->id; ?>';
		  });
		  $("#swipe").on("swipeleft",function(){
		    window.location = '?noTo=<?php echo $row->id; ?>';
		  });
		  $("#yes").on("click",function(){
		    window.location = '?yesTo=<?php echo $row->id; ?>';
		  });
		  $("#no").on("click",function(){
		    window.location = '?noTo=<?php echo $row->id; ?>';
		  });
      });
  </script>
	
  </body>
</html>


