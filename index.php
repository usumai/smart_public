<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<style>
    #myProgress {
        width: 100%;
        background-color: #ddd;
    }
    #myBar {
        width: 1%;
        height: 30px;
        background-color: #4CAF50;
    }
</style>

<script type="text/javascript">

$(function() {
    fnDo("get_system","MakeIndexTable",0)
    
    $(document).on('click', '.btnArchive', function(){ 
        let stkm_id = $(this).val();
        $.get("05_action.php?act=save_archive_return&stkm_id="+stkm_id, function(data, status){
            console.log("Result:"+data)
            if(data=="success"){
                $("#row"+stkm_id).hide();
            }
        });
    })

    $(document).on('click', '.btnToggle', function(){
        let stkm_id = $(this).val();
        $.get("05_action.php?act=save_toggle_stk_return&stkm_id="+stkm_id, function(data, status){
            console.log("Result:"+data)
            if(data.substring(0, 6)!="failed"){
                $(".toggleBTN"+stkm_id).text(data);
                $(".toggleBTN"+stkm_id).removeClass("btn-success");
                $(".toggleBTN"+stkm_id).removeClass("btn-outline-dark");
                let btnStyle = (data=="Included") ? "btn-success" : "btn-outline-dark";
                $(".toggleBTN"+stkm_id).addClass(btnStyle);
            }
            fnDo("get_system","MakeIndexTable",0)
            fnDo("get_system","SetMenu",0)
            fnDo("get_templates","LoadTemplates",0)
        });
    })

    $('#area_upload_status').hide();
    $('#fileToUpload').change(function(){
        let filename = $(this).val();
        if (filename) {
            $('#btn_submit_upload').show();    
        }else{
            $('#btn_submit_upload').hide();    
        }
    });
    $('#btn_submit_upload').click(function(){
        $('#area_upload_status').show();    
        $('#form_upload').hide();
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
                $("#upload_count").text(data+" records uploaded");
            }
        });
        setTimeout(check_upload_progress, 1000);//1000 = 1 sec
    }
});
</script>

<main role="main" class="flex-shrink-0">
	<div class="container">
		<h1 class="mt-5 display-4">SMART Mobile</h1>
	</div>
</main>

<div class="container-fluid">
    <table id="tbl_stk" class="table">
        <thead>
            <tr>
                <td>Included</td>
                <td>SMARTM#</td>
                <td>Type</td>
                <td>ID</td>
                <td>Name</td>
                <td align='right'>Orig</td>
                <td align='right'>Completed</td>
                <td align='right'>Extra</td>
                <td align='right'>Status</td>
                <td align='right'>Archive</td>
                <td align='right'>Excel</td>
                <td align='right'>Export</td>
                <td align='right'>Included</td>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <br><hr>
    <form action="05_action.php" method="post" enctype="multipart/form-data" id="form_upload">
        <h5 class="card-title">Upload file</h5>
        <h6 class="card-subtitle mb-2 text-muted">Stocktake and Raw Remainder</h6>
        <p class="card-text">
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
        </p>
        <input type="hidden" name="act" value="upload_file">
        <input type="submit" value="Upload File" name="submit" class="btn btn-link" id="btn_submit_upload" style="display:none">
    </form>
    <span id="area_upload_status" style="display:none!important">
        <div class="spinner-border" role="status" id='loading_spinner' style="width: 3rem; height: 3rem;">
            <span class="sr-only" style="">Loading...</span>
        </div>
        <span id="upload_count"></span>
    </span>

    

</div>
<?php include "04_footer.php"; ?>