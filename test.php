<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>


<script type="text/javascript">
$(document).ready(function() {

    $('#test').click(function(){
        check_upload_progress();
    });


    function check_upload_progress(){
        // do whatever you like here
        $.get( {
            url: "05_action.php",
            data: {
                act: "get_check_upload_rr"
            },
            success: function( data ) {
                console.log(data)
                $("#upload_count").text(data);
            }
        });
        setTimeout(check_upload_progress, 1000);//1000 = 1 sec
    }



});
</script>
<main role="main" class="flex-shrink-0">
	<div class="container">
		<h1 class="mt-5">SMART Mobile</h1>
		<button id="test">Test Me</button>
		<span id="upload_count"></span>
	</div>
</main>
<?php include "04_footer.php"; ?>