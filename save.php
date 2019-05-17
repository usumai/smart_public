<?php 

include '01_dbcon.php'; 

// $sql_save = $_POST['sql'];
$sql_save = "INSERT INTO smartdb.sm14_ass (create_date,create_user,delete_date,delete_user,stkm_id,storage_id,stk_include,Asset,Subnumber,impairment_code,genesis_cat,first_found_flag,rr_id,fingerprint,res_create_date,res_create_user,res_reason_code,res_reason_code_desc,res_impairment_completed,res_completed,res_comment,AssetDesc1,AssetDesc2,AssetMainNoText,Class,classDesc,assetType,Inventory,Quantity,SNo,InventNo,accNo,Location,Room,State,latitude,longitude,CurrentNBV,AcqValue,OrigValue,ScrapVal,ValMethod,RevOdep,CapDate,LastInv,DeactDate,PlRetDate,CCC_ParentName,CCC_GrandparentName,GrpCustod,CostCtr,WBSElem,Fund,RspCCtr,CoCd,PlateNo,Vendor,Mfr,UseNo,res_AssetDesc1,res_AssetDesc2,res_AssetMainNoText,res_Class,res_classDesc,res_assetType,res_Inventory,res_Quantity,res_SNo,res_InventNo,res_accNo,res_Location,res_Room,res_State,res_latitude,res_longitude,res_CurrentNBV,res_AcqValue,res_OrigValue,res_ScrapVal,res_ValMethod,res_RevOdep,res_CapDate,res_LastInv,res_DeactDate,res_PlRetDate,res_CCC_ParentName,res_CCC_GrandparentName,res_GrpCustod,res_CostCtr,res_WBSElem,res_Fund,res_RspCCtr,res_CoCd,res_PlateNo,res_Vendor,res_Mfr,res_UseNo,res_isq_5,res_isq_6,res_isq_7,res_isq_8,res_isq_9,res_isq_10,res_isq_13,res_isq_14,res_isq_15) VALUES ('2019-05-16 10:43:17',null,null,null,'1','97314',null,'497330',null,null,null,'0',null,null,null,null,null,null,null,null,null,'1408/A0325 CALCIUM NITRATE FACILITY','Acid Area (Photo No.002505)','Line no 2530 Maximo no 101604 PU no  Thales no','2000',null,null,'SNSW-ST2190','1','','250M2','1408','1408/A0325','','NSW','','','187979.67','190191.00','425000.00','0.00','FCIV','-2211.53','2000-07-01 00:00:00','2016-05-06 00:00:00',null,'2015-11-30 00:00:00','ESTATE AND INFRASTRUCTURE GROUP','','DMO','681524','','99998','681524','1000','','','','33',null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);";

if (!mysqli_multi_query($con,$sql_save)){
	echo("Error description: " . mysqli_error($con));
}
echo "*";
echo $sql_save;
?>