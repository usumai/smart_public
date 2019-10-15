<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php


$cherry=0;
$sql = "SELECT* FROM smartdb.sm13_stk WHERE  stk_include = 1";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
          if ($cherry==0){
               $cherry=1;
               $stkm_id_one    = $row["stkm_id"];
               $journal_text_a = $row["journal_text"];
          }else{
               $stkm_id_two    = $row["stkm_id"];
               $journal_text_b = $row["journal_text"];
          }
          $stk_id             = $row["stk_id"];
          $stk_name           = $row["stk_name"];
          $dpn_extract_date   = $row["dpn_extract_date"];
          $rowcount_original  = $row["rowcount_original"];
          $stk_type           = $row["stk_type"];
}}

$sql1 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one) AS vtsql1";
$sql2 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two) AS vtsql2";

$sql_a = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2, 
'Full match', vtsql1.ass_id
FROM $sql1, $sql2 
WHERE vtsql1.storage_id = vtsql2.storage_id
AND  vtsql1.fingerprint = vtsql2.fingerprint";

$sql_b = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
'Only STK1 result', vtsql1.ass_id
FROM $sql1, $sql2 
WHERE vtsql1.storage_id = vtsql2.storage_id
AND vtsql1.fingerprint IS NOT NULL
AND vtsql2.fingerprint IS NULL";

$sql_c = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
'Only STK2 result', vtsql2.ass_id
FROM $sql1, $sql2 
WHERE vtsql1.storage_id = vtsql2.storage_id
AND vtsql1.fingerprint IS NULL
AND vtsql2.fingerprint IS NOT NULL";

// $sql_d = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
// vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
// 'FF match', vtsql1.ass_id
// FROM $sql1, $sql2 
// WHERE vtsql1.storage_id IS NULL
// AND vtsql2.storage_id IS NULL
// AND  vtsql1.fingerprint = vtsql2.fingerprint";

$sql_d = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
'FF match', vtsql1.ass_id
FROM 
(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
WHERE vtsql1.fingerprint = vtsql2.fingerprint";
// $sql_e = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
// vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
// 'FF stk1', vtsql1.ass_id
// FROM $sql1, $sql2 
// WHERE vtsql1.storage_id IS NULL
// AND vtsql2.storage_id IS NULL
// AND vtsql1.fingerprint IS NOT NULL
// AND vtsql2.fingerprint IS NULL";
$sql_e = "     SELECT NULL AS stID1, fingerprint AS fp1, NULL AS stID2, NULL AS fp2,
                'FF stk1', ass_id
                FROM smartdb.sm14_ass 
                WHERE stkm_id = 1 
                AND storage_id IS NULL
                AND fingerprint IS NOT NULL
                AND ass_id NOT IN (
                    SELECT 
                    vtsql1.ass_id
                    FROM
                    (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id_one AND storage_id IS NULL) AS vtsql1,
                    (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id_two AND storage_id IS NULL) AS vtsql2
                    WHERE vtsql1.fingerprint = vtsql2.fingerprint
                )";

// $sql_f = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
// vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
// 'FF stk2', vtsql2.ass_id
// FROM $sql1, $sql2 
// WHERE vtsql1.storage_id IS NULL
// AND vtsql2.storage_id IS NULL
// AND vtsql1.fingerprint IS NULL
// AND vtsql2.fingerprint IS NOT NULL";
$sql_f = "     SELECT NULL AS stID1, NULL AS fp1, NULL AS stID2, fingerprint AS fp2,
'FF stk1', ass_id
FROM smartdb.sm14_ass 
WHERE stkm_id = 2 
AND storage_id IS NULL
AND fingerprint IS NOT NULL
AND ass_id NOT IN (
    SELECT 
    vtsql2.ass_id
    FROM
    (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id_one AND storage_id IS NULL) AS vtsql1,
    (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id_two AND storage_id IS NULL) AS vtsql2
    WHERE vtsql1.fingerprint = vtsql2.fingerprint
)";

$sql_g = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
'No result', vtsql1.ass_id
FROM $sql1, $sql2 
WHERE vtsql1.storage_id = vtsql2.storage_id
AND vtsql1.fingerprint IS NULL
AND vtsql2.fingerprint IS NULL";

$sql_h = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
'Needs comparison', vtsql1.ass_id AS asID1, vtsql2.ass_id AS asID2
FROM $sql1, $sql2 
WHERE vtsql1.storage_id = vtsql2.storage_id
AND vtsql1.fingerprint IS NOT NULL
AND vtsql2.fingerprint IS NOT NULL
AND vtsql1.fingerprint <> vtsql2.fingerprint";


$log = "";
$log .= fnSQLRes($sql_a,"Full match on storageID and fingerprint");
$log .= fnSQLRes($sql_b,"Only STK1 result");
$log .= fnSQLRes($sql_c,"Only STK2 result");
$log .= fnSQLRes($sql_d,"FF match - Fingerprints match");
$log .= fnSQLRes($sql_e,"FF stk1");
$log .= fnSQLRes($sql_f,"FF stk2");
$log .= fnSQLRes($sql_g,"No result");
$log .= fnSQLRes($sql_h,"Needs comparison");


function fnSQLRes($sql,$title){
    global $con;
    $sql_count = "SELECT COUNT(*) AS rowcount FROM ($sql) AS vtSubSQL";
    $result = $con->query($sql_count);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $rowcount = $row['rowcount'];
    }}
    return "<b>$title</b><br>$sql<br>Rowcount:$rowcount<br><br>";
 
}

?>

<br><br><br>

<div class='container'>
    <div class='row'>
        <div class='col'>
            <div class='display-4'>
                Merge considerations
            </div>
            <?=$log ?>
        </div>
    </div>
</div>



<?php include "04_footer.php"; ?>