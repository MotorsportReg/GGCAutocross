<?php
include('functions.php');
sqlconnect();
include('auth.php');
if ($_GET[user]) {
	if ($usergroup == "admin"){
		$username = $_GET[user];
	}
}	
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="initial-scale=1.0"> 
    <script src="js/stay_standalone.js" type="text/javascript"></script>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="apple-touch-icon" href="autoxicon.png" />
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
    <link href="css/selectboxit.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
     <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <title>GGC BMW CCA Autocross page</title>
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
    <style>
        body {
         	background-image: url('img/satinweave.png')  /*thanks SubtlePatterns.com */
        }
      /* Select Box */
		#copycar {
	  		width: 800px;
	  		cursor:pointer;
	  		white-space:nowrap;
  		}
  	    .tablehead {
        	background-color: #cccccc;
      	}
      	@media (max-width: 979px) {
        	.navbar-fixed-top,
        	.navbar-fixed-bottom {
        		position: fixed;
       			margin-left: 0px;
        		margin-right: 0px;
      		}
      		.firstelement { padding-top: 60px; }
      	}
    </style>
</head>
<body>
<?php include('navbar2.html');?>
<div class="container">
	<div class="row-fluid">
		<div class="span10">
<?php
if (!$username){
	loginform();
?>
<h3>Welcome to GGC Autocross!</h3>
<p>Our autocrosses are a competitive environment where a cone track is setup in a big parking lot (Candlestick Park) or airfield (Marina Airport). We put cars into classes based on a formula (considering weight, torque, modifications, and several other factors), and drivers compete against other cars within their class. Though there is an element of risk, hazards to participants and property are not expected to exceed those encountered in normal, legal highway driving. Due to the length of our courses we allow only one to two cars on the course at any one time (opposite sides). Cars race against the clock, not side-by-side.
</p>
<p>For more information, go <a href="http://www.ggcbmwcca.org/driving-events/autocross">here</a>.</p>

A typical signup process for a <u>first timer</u> is:
<ul>
<li>Login to this page (create an account if you have not done so)
<li>Classify your car & save it
<li>Pick a driver number
<li>Visit http://www.motorsportreg.com to sign up for & pay for an event
</ul>
<p>As long as you drive the same car for each event, you should only have to classify your car once per season.  Simply sign up @ motorsportreg.com for each event- registration typically opens up the Monday after the previous event.
</p>
<?php
} else {
$result = mysql_query("SELECT drivernumber FROM autox_numbers WHERE `username` = '$username' ORDER BY `drivernumber` ASC") or die("Error: " . mysql_error());
if (mysql_num_rows($result) == "0"){
	echo"<br><Br><span class='badge badge-important'>You have not chosen a number.  You must choose a number to compete!</span><br><br>";
} else {
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$mynumber = $row[0];
	}
	echo"<p><img src='generatepic.php?number=$mynumber' class='firstelement'></p>"; 
}
	echo"
	
	<form class='form-horizontal' role='form'>
		<div class='form-group'>
			<label for='numberform' class='col-sm-8 control-label'>Your number is shown on the door of the car above. Choose a new or different number here:</label>
		<div class='col-md-2'>
			<select name='drivernumber' class='form-control' id='numberform' style='display:inline;'><option value=''></option>";
	$result = mysql_query("SELECT drivernumber FROM autox_numbers WHERE `username` = '' ORDER BY `drivernumber` ASC") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<option value='$row[0]'>$row[0]</option>";
	}
echo"</select></div></div></form>";
$result = mysql_query("SELECT * FROM autox_classifications WHERE `username` = '$username' AND `active` != 'H'") or die("Error: " . mysql_error());
if (mysql_num_rows($result) != "0") {
	echo"<h4>Your Classified Cars</h4>";
	if (mysql_num_rows($result) > 1) { echo"<h5>Click on a car to make it active for the next autocross</h5>";}
	echo"
	<table class='table table-condensed table-bordered' id='classifytable'>
	<thead>
	<tr class='tablehead'><th>Car</th><th>Points</th><th>Class</th><th>Actions</th></tr>
	</thead><tbody>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if ($row[11] == "Y"){
		$rowclass = "success";
		$activebutton = $row[0];
	} else {
		$rowclass = "";
	}
		echo"<tr class='$rowclass' id='$row[0]'><td class='activecell'>$row[4] $row[5]</td><td class='activecell'>$row[3]</td><Td class='activecell'>$row[2]</td><Td><a href='show.php?id=$row[0]&popup=Y' class='btn btn-default carinfoajax'>View details</a> <a href='#' class='btn btn-danger delcar'>Delete</a></td></tr>";
	}
	echo"</tbody></table><p><i>You cannot edit an existing classification. To make changes, you must delete the car and re-classify.</i></p>";
}

if (!$activebutton){ echo"<br><Br><span class='badge badge-important' id='classwarning'>Warning: You do not have an active car classification</span><br><br>"; }
	echo"<br><a href='classify.php' class='btn btn-info'>Classify your own car</a> ";
	echo" <a href='#' class='btn btn-info' id='otherpickerbutton'>Copy someone else's classification if you will be driving their car</a>";
	echo"<div id='otherpicker'>
	<br><Br>
	<select name='other' id='copycar'>
	<option value=''>Select a driver to copy</option>";
	$result = mysql_query("SELECT autox_numbers.drivernumber,gy01d_users.name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,gy01d_users,autox_numbers WHERE gy01d_users.username = autox_classifications.username and gy01d_users.username = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<option value='$row[6]'>#$row[0] - $row[1] - $row[2] $row[3] - $row[4] pts $row[5] class</option>";
	}
	echo"</select></div>";
}
?>

</div> <!--span10-->
<div class="span2">
		<div class="well sidebar-nav">
		<ul class="nav nav-list">
			<li class="nav-header">Upcoming autocrosses</li>
<?php

		$result = mysql_query("SELECT * FROM autox_dates ORDER BY `autoxdate` ASC") or die("Error: " . mysql_error());	
		$open = 0;
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			if (time() < strtotime("$row[1] 10:00AM")) {
				$open++;
				$thedate = date("D, M j", strtotime($row[1]));
				echo"<li><h6>$thedate<br>&nbsp;&nbsp;$row[2]";
				if ((!$closemsg) && ($open == 1)){
					echo"<br>&nbsp;&nbsp;&nbsp; <a href='http://ggcbmwcca.motorsportreg.com/' target='_blank'>Reg open</a>";
				}
				echo"</h6></li>";
			}
		}
?>			
		</ul>
		</div> <!-- well sidebar-nav-->
</div> <!-- span2 -->
</div> <!--row-fluid-->
</div>  <!--container-->
<?php include ('bottombar.html'); ?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/selectboxit.js"></script>
<script>
<?php if (!$activebutton) { $activebutton = "0";}?>
var activebutton = <?php echo $activebutton;?>;
$(document).ready(function() {
		$(".carinfoajax").colorbox();
/* 		$("select").selectBoxIt({}); */
		$("#otherpicker").hide();
		
}); 
$("#numberform").change(function(event){
	var numma = $("#numberform option:selected").text();
	$.get('updatecars.php?action=renumber&username=<?php echo $username;?>' + '&id='+numma, function(data) {
		location.reload();
	});
});
$("#copycar").change(function(event){
	var numma = $("#copycar option:selected").val();
	$.get('updatecars.php?action=copycar&username=<?php echo $username;?>' + '&id='+numma, function(data) {
		location.reload();
	});
});
$('#otherpickerbutton').on('click', function(event) {
	$('#otherpicker').show();	
});
$('.activecell').on('click', function(event) {
	var trid = $(this).closest('tr').attr('id');
	$.get('updatecars.php?action=makeactive&username=<?php echo $username;?>' + '&id='+trid, function(data) {});
    if ($(this).closest('tr').hasClass('success')) {
        $(this).closest('tr').removeClass('');
        $("#classwarning").hide();
    } else {
        $(this).closest('tr').addClass('success').siblings().removeClass('success'); 
        $("#classwarning").hide();
    }
});
$('.delcar').click(function(event){
	var trid = $(this).closest('tr').attr('id');	
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=delcar&username=<?php echo $username;?>' + '&id='+trid, function(data) {
		location.reload();
		
	});
});
</script>
</body></html>