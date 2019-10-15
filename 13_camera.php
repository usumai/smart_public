<?php 
include "01_dbcon.php"; 
include "02_header.php"; 

$ass_id	= $_GET["ass_id"];

?>
<style type="text/css">
	body{padding-top:0px;}
</style>

<table width="100%">
	<tr>
		<td width="20%">
			<form method="post" action="05_action.php" >
				<input type="hidden" name="act" value="save_photo">
				<input type="hidden" name="ass_id" value="<?=$ass_id?>">
				<input type="hidden" name="res_img_data" id="res_img_data">
				<button type="submit" class="btn btn-success btn_acceptphoto" id="btn_acceptphoto"><span class='octicon octicon-check' style='font-size:30px'></span></button><br>
				<br><br>
	        	<a href="11_ass.php?ass_id=<?=$ass_id?>" class="btn btn-danger" id="btn_cancelphoto"><span class='octicon octicon-x' style='font-size:30px'></span></a>
			</form><br><br><br><br><br>
		</td>
		<td width="60%">
			<div class="row" id="area_photo">
			</div>
			<div class="row" id="area_video">
				<video id="video" width="100%" height="100%" autoplay></video>
			</div>

			<!-- <br><br><br><br><a href="107_help.php#camera_switching">Switch Cameras</a> -->
			
		</td>
		<td width="20%" align="right">
			<form method="post" action="05_action.php" >
				<input type="hidden" name="act" value="save_photo">
				<input type="hidden" name="ass_id" value="<?=$ass_id?>">
				<input type="hidden" name="res_img_data" id="res_img_data2">
	        	<button type="submit" class="btn btn-success btn_acceptphoto" id="btn_acceptphoto"><span class='octicon octicon-check' style='font-size:30px'></span></button><br>
				<br><br>
	        	<a href="11_ass.php?ass_id=<?=$ass_id?>" class="btn btn-danger" id="btn_cancelphoto"><span class='octicon octicon-x' style='font-size:30px'></span></a>
			</form><br><br><br><br><br>
		</td>
	</tr>
</table>


<!-- <div class="container">
	<div class="row">
		<div class="col">
			<div class="form-row text-center">
			    <div class="col-12">
			    </div>
			</div>			
		</div>
		<div class="col-10">
		</div>
		<div class="col">
			<div class="form-row text-right">
			    <div class="col-12">
			    </div>
			</div>	
		</div>
	</div>
</div> -->


<canvas id="canvas" width="800" height="600"></canvas>
<!-- <canvas id="canvas" width="1600" height="1200"></canvas> -->
<!-- <div id="test"></div> -->



<script>
// Grab elements, create settings, etc.
var video = document.getElementById('video');

navigator.mediaDevices.enumerateDevices()
    .then(function(devices) {
        // devices is an array of accessible audio and video inputs. deviceId is the property I used to switch cameras
    })
    .catch(function(err) {
        console.log(err.name + ": " + error.message);
});


// Get access to the camera!
if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    // Not adding `{ audio: true }` since we only want video now
    // navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
    //     video.src = window.URL.createObjectURL(stream);
    //     video.play();
    // });
	navigator.mediaDevices.getUserMedia({ video: true, audio: false })
	  .then(stream => video.srcObject = stream)
	  .catch(e => log(e.name + ": "+ e.message));
	var log = msg => div.innerHTML += msg + "<br>";


}
// Elements for taking the snapshot
var canvas = document.getElementById('canvas');
var context = canvas.getContext('2d');
//var video = document.getElementById('video');

// Trigger photo take
// document.getElementById("snap").addEventListener("click", function() {
// 	context.drawImage(video, 0, 0, 640, 480);
// 	var res_img_data = canvas.toDataURL();
// 	alert(res_img_data);
// 	$("#test").text(res_img_data);
// });


$(document).ready(function() {
	$("#canvas").hide();
	$("#area_photo").hide();
	$(".btn_acceptphoto").hide();
	$("#video").click(function(){
		context.drawImage(video, 0, 0, 800, 600);
		var res_img_data = canvas.toDataURL();
		$("#area_video").hide();
		$("#area_photo").show();
		$(".btn_acceptphoto").show();

		// $("#test").text(res_img_data);
		$("#res_img_data").val(res_img_data);
		$("#res_img_data2").val(res_img_data);
		$("#area_photo").html("<img src='"+res_img_data+"' width='100%' height='90%'/>");
	});

	$("#area_photo").click(function(){
		$("#area_video").show();
		$("#area_photo").hide();
		$(".btn_acceptphoto").hide();
	});
});
</script>