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
  	echo renderAccountPageHeader(array("#SITE_ROOT#" => SITE_ROOT, "#SITE_TITLE#" => SITE_TITLE, "#PAGE_TITLE#" => "Matches"));
  ?>

  <body>


    <div id="wrapper">

      <!-- Sidebar -->
        <?php
          echo renderMenu("dashboard");
        ?>  

     		 <div id="page-wrapper">
<h1>Matches</h1>
		   

		        <div class="row">
		          <div>
		            <div id="swipe" class="alert alert-info">
		             
		<?php
		
		include('warehouse.php');
		
		$user_id = $loggedInUser->user_id;
		
		$query = mysql_query("
		select *
		from uf_users, matches m1
		where m1.user_id_b = $user_id 
		and uf_users.id = m1.user_id_a
		and m1.value = 1 
		and uf_users.id IN (select m2.user_id_b from matches m2 where m2.user_id_a = $user_id and m2.value = 1)",$db);
		
		if (!mysql_num_rows($query))
		{
			echo "<p>You have no matches yet, keep trying and be patient!</p>";
		}
		else
		{
			echo "<p>Congratulations, you have ".mysql_num_rows($query)." matches. You can now email your match, just click on them!</p>";
			while($row = mysql_fetch_object($query))
			{
				echo "<p><a href=\"mailto:$row->email\">$row->display_name</a></p>";
			}			
		}

		
		?>
		
		            </div>

		          </div>
	

      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->

  </body>
</html>


