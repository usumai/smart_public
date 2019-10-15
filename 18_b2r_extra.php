<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$auto_storageID = $_GET["auto_storageID"];
$BIN_CODE = $_GET["BIN_CODE"];

$sql = "SELECT stkm_id, STOCK_CODE, ITEM_NAME, SOH, finalResult, finalResultPath FROM smartdb.sm18_impairment WHERE auto_storageID = '$auto_storageID'";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {      
        $stkm_id            = $row['stkm_id'];
        $finalResult        = $row['finalResult'];
        $finalResultPath    = $row['finalResultPath'];
}}
?>

<script type="text/javascript">
let ans = '<?=$finalResultPath?>'
if(ans==''){
    ans = {
        1 : "",
        2 : "",
        3 : "",
        4 : "",
        5 : "",
        6 : "",
        7 : "",
        8 : "",
        9 : "",
    }
}else{
    ans = JSON.parse(ans)
}

let qns = {
    1:{
        name:   "Is the item a commonwealth asset?",
        yesPath:"2",
        noPath: "nstr",
    },
    2:{
        name:   "Is the item serial tracked?",
        yesPath:"3",
        noPath: "4",
    },
    3:{
        name:   "Does serial no. exists in WHS on MILIS?",
        yesPath:"4",
        noPath: "FF",
    },
    4:{
        name:   "Check dues in/out status?",
        yesPath:"nstr",
        noPath: "5",
    },
    5:{
        name:   "Does it belong in a SCA?",
        yesPath:"nstr",
        noPath: "6",
    },
    6:{
        name:   "Does the item belong in this WHS?",
        yesPath:"7",
        noPath: "nstr",
    },
    7:{
        name:   "Verify inventory category. Does it fall under an exclusion list?",
        yesPath:"nstr",
        noPath: "8",
    },
    8:{
        name:   "Are there other MILIS bins in the warehouse that contain this item/stockcode?",
        yesPath:"9",
        noPath: "nstr",
    },
    9:{
        name:   "Is the physical SOH different to 1RB?",
        yesPath:"LE",
        noPath: "FF",
    },
}
// console.log(qns)
let path, finalResult
$(document).ready(function() {
    
    setPage()
    function assessQuestion(qno){
        questionText ="<li class='list-group-item history'><b>"+qns[qno]['name']+"</b></li>"
        path+=questionText
        if (ans[qno]){
            btnRepeal = "&nbsp;<button type='button' class='btn btn-sm btn-outline-dark btnRepeal' value='"+qno+"'>X</button>"
            if (ans[qno]=="Yes"){
                nextStep = qns[qno]['yesPath']
                path+="<li class='list-group-item list-group-item-success history'>Yes"+btnRepeal+"</li>"
            }else{
                nextStep = qns[qno]['noPath']
                path+="<li class='list-group-item list-group-item-danger history'>No"+btnRepeal+"</li>"
            }
        }else{
            let btnYes = "<button class='list-group-item list-group-item-action list-group-item-success question'  value='Yes' data-qno='"+qno+"'>Yes</button>"
            let btnNo = "<button class='list-group-item list-group-item-action list-group-item-danger question'  value='No' data-qno='"+qno+"'>No</button>"
            path+=btnYes+btnNo
            nextStep = "waiting"
        }
        return nextStep
    }

    function setPage(){
        path='';
        $('.question').toggle(false);

        isFinished = false
        nextStep = 1
        do {
            nextStep = assessQuestion(nextStep)
            
            if (nextStep=='nstr'||nextStep=='LE'||nextStep=='FF'||nextStep=='waiting'){
                isFinished = true
            }
        }
        while ( isFinished == false);
        
        if (nextStep!='waiting'){
            // path+="<br><br>Final result: "+nextStep
            finalResult         = nextStep
            finalResult_disp    = nextStep
            if (finalResult_disp=='nstr'){
                finalResult_disp = "No further investigation"
            }
            path+="<li class='list-group-item'><b class='display-4'>"+finalResult_disp+"</b><br>Final result</li><br>"


            path+="<button type='button' id='btnSave' class='btn btn-outline-dark'>Save</button>"
        }
        $("#workingChain").html(path);
    }


    $('body').on('click', '.question', function() {
        let answer = $(this).val();
        let qno = $(this).data('qno');
        ans[qno] = answer;
        
        setPage()
    })

    $('body').on('click', '#btnSave', function() {
        $.post("05_action.php",
        {
            act:            "save_b2r_extra",
            auto_storageID: "<?=$auto_storageID?>",
            BIN_CODE:       "<?=$BIN_CODE?>",
            stkm_id:       "<?=$stkm_id?>",
            finalResult,
            finalResultPath:JSON.stringify(ans)
        },
        function(data, status){
            console.log(data);
            if(data=="success"){
                window.location.replace("17_b2r.php?BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>");
            }
        });
    })

    $('body').on('click', '.btnRepeal', function() {
        let qno = $(this).val();
        qno=Number(qno)
        let deleteRest = false
        for (let key in ans){
            key=Number(key)
            if(key==qno){
                deleteRest = true
            }
            if(deleteRest){// console.log('deleting answer:'+key+'-'+ans[key])
                ans[key]=''
            }
        }
        setPage()
    })

});
</script>

<style>
.history{
    padding:1px;
}
</style>


<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>
            Extra stockcode investigation
            <a href='17_b2r.php?BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>' class='btn btn-outline-dark float-right'>Back</a>
        </h1>
        <div class='dropdown'><button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Clear</button><div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'><a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_clear_b2r_extra&auto_storageID=<?=$auto_storageID?>&BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>'>I'm sure</a></div></div>
    </div>
</div>

<div class='row'>
    <div class='col lead  auto-mx' id='menuleft'>
        <ul class="list-group list-group-flush text-center">
            <div id='workingChain'></div>
        </ul>
    </div>
</div>



<?php include "04_footer.php"; ?>