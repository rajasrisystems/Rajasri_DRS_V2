<?php
include "includes/common.php";
include_once "includes/classes/class.report.php";
/* all resource */
$objReport = new Report();
global $config;
//if($_REQUEST['resourceq'] != '' &&  $_REQUEST['resourceq'] == '1')
//{
	$getmon = $_REQUEST['month'];
	$monthName = date("F", mktime(0, 0, 0, $getmon, 10));
	$getyr  = $_REQUEST['year'];
	$getdepart = $_REQUEST['dept'];	
	$date=$getmon.'/'.$getyr;
	$select="select * from rating r,resource re where date_format(r.RatingDate, '%m/%Y')='".$date."' and re.ID=r.ResourceID and r.DepartmentID ='".$getdepart."' group by r.ResourceID";
	$result=mysql_query($select);
	$num=mysql_num_rows($result);
	$csvFile=array('Resource','Beginning Rate','End Rate');
	
	$output = fopen('report.xls', 'a');
	fputcsv($output, $csvFile);
	
	$c=1;
	$associativeArray = array();
	if($num!=''){
		while($rows=mysql_fetch_array($result)){
			$ResourceID	=$rows['ResourceID'];
			$CodeID 	=$rows['CodeID'];
			$firstDate=$getyr.'-'.$getmon.'-01';
			$previousmonth=date('m/Y', strtotime($firstDate . ' -1 month'));
			$rat_date=explode("/", $previousmonth);
			$month_days=cal_days_in_month(CAL_GREGORIAN, $rat_date[0], $rat_date[1]);
			$prepoints=$month_days*50; 
			$nprepoints=$day*50; 
			/******************************** Beggining rate calculation**************************************/
			$result_date= "SELECT * FROM rating where date_format(RatingDate, '%m/%Y')='".$previousmonth."' and ResourceID='$ResourceID'";
			$exequery=mysql_query($result_date);
			$num_rows=mysql_num_rows($exequery);
			$points=0;
			if($num_rows>0)
			{

				while($row2=mysql_fetch_array($exequery)){
						
					$sql = "SELECT * FROM code where ID='".$row2['CodeID']."'";
					$getexe = $objReport->ExecuteQuery($sql, "select");
					$points+= $getexe[0]['Points'];
				}
			}
			if($points != '')
			{
				$tot= ($points + $prepoints)/$month_days;
				$begin_points=(round($tot,2));
				
			
			}
			else
			{
				$begin_points=50;
			}
			/********************************  Beggining rate calculation**************************************/
			/********************************  End rate calculation**************************************/
			$ppoints=0;
			$day = date("d");  // current date
			$montht = date("m"); // current month
			$nnprepoints=$day * $begin_points;
			$firstDate=$getyr.'-'.$getmon.'-01';
			$previousmonth=date('m/Y', strtotime($firstDate . ' -1 month'));
			$rat_date=explode("/", $previousmonth);
			$month_days=cal_days_in_month(CAL_GREGORIAN, $rat_date[0], $rat_date[1]);
			$prepoints=$month_days*50; 
			$presult_date= "SELECT * FROM rating where date_format(RatingDate, '%m/%Y')='".$date."' and ResourceID='$ResourceID'";
			$pexequery=mysql_query($presult_date);
			$pnum_rows=mysql_num_rows($pexequery);
			if($pnum_rows>0)
			{
				while($prow2=mysql_fetch_array($pexequery)){
					$psql = "SELECT * FROM code where ID='".$prow2['CodeID']."'";
					$pgetexe = $objReport->ExecuteQuery($psql, "select");
					$ppoints+= $pgetexe[0]['Points'];
				}
			}
			if($ppoints != '')
			{
				if($getmon == $montht)
				{
				$ptot= ($ppoints + $nnprepoints)/$day;
				$end_points=(round($ptot,2));		
				}	
				else
				{
				$ptot= ($ppoints + $prepoints)/$month_days;
				$end_points=(round($ptot,2));					
				}
			}
			else
			{
				$end_points=50;
			}
			/******************************** End rate calculation**************************************/
			$csvFile =array($rows['ResourceInitial'],$begin_points,$end_points);			
			fputcsv($output, $csvFile);
			$c++;
			}
	}
fclose($output);
//}
/************** one resource *******************/
/*if($_REQUEST['resourceq'] != '' &&  $_REQUEST['resourceq'] == '2')
{
	$getmon = $_REQUEST['month'];
	$monthName = date("F", mktime(0, 0, 0, $getmon, 10));
	$getyr  = $_REQUEST['year'];
	$getini = $_REQUEST['newresid'];
	$iniquery=$_REQUEST['optionname'];
	$firstDate=$getyr.'-'.$getmon.'-01';
	$date=date('Y-m-d', strtotime($firstDate . ' -1 month'));
	$numberOfDays=cal_days_in_month(CAL_GREGORIAN, $getmon, $getyr);
	$lastDate=date("Y-m-t", strtotime($firstDate));
	$date=$firstDate;
	$datesArr[]=$date;
	for($i=1;$i<$numberOfDays;$i++){
		$date=date('Y-m-d', strtotime($date . ' +1 day'));

		$datesArr[]=$date;
	}
	$csvFile=array('Date','Change','Rating','Notes','Manager');
	$output = fopen('php://output', 'w');
	fputcsv($output, $csvFile);
	$c=0;
	$today=date('Y-m-d');
	$onlydate=date('d');
	$nnprepoints = $onlydate * $begin_points;
	$noofdays = 0;
	foreach($datesArr as $date){
		if($date <= $today){
			$noofdays++;	
			$select_res="select * from rating where ResourceID='".$getini."' and RatingDate='".$date."' order by RatingDate desc";
			$exe_res=$objReport->ExecuteQuery($select_res, "select");
			$count_res=$objReport->ExecuteQuery($select_res, "norows");
			$CodeID =$rows['CodeID'];			
			$previousmonth=date('m/Y', strtotime($firstDate . ' -1 month'));
			$rat_date=explode("/", $previousmonth);
			$month_days=cal_days_in_month(CAL_GREGORIAN, $rat_date[0], $rat_date[1]);
			$day = date("d");  // current date
			$prepoints=$month_days*50; // prev month
			$nprepoints=$day*50; // cur month
			$result_date= "SELECT * FROM rating where date_format(RatingDate, '%m/%Y')='".$previousmonth."' and ResourceID='$getini'";
			$exequery=mysql_query($result_date);
			$num_rows=mysql_num_rows($exequery);
			$points=0;
			if($num_rows>0)
			{
				while($row2=mysql_fetch_array($exequery)){
					$sql = "SELECT * FROM code where ID='".$row2['CodeID']."'";
					$getexe = $objReport->ExecuteQuery($sql, "select");
					$points+= $getexe[0]['Points'];
				}
			}
			if($points != '')
			{
				$tot= ($points + $prepoints)/$month_days;
				$begin_points=(round($tot,2));
			}
			else
			{
				$begin_points=50;
			}
			$change=0;
			if($count_res > 0){
			$mulvar="select * from rating where ResourceID='".$getini."' and RatingDate='".$date."' order by RatingDate desc";
			$mulquery=mysql_query($mulvar);
			$num_rows=mysql_num_rows($mulquery);
			while($mulrow=mysql_fetch_array($mulquery)){
					$mulcode=$mulrow['CodeID'];
					$mulselect="select * from code where ID='".$mulcode."'";
				$multiple=$objReport->ExecuteQuery($mulselect, "select");
				$change +=$multiple[0]['Points'];
				$newPoint= $begin_points +$change;
				}
			
				$newcodevar[]=$exe_res[0]['CodeID'];
				/* $codeId=$exe_res[0]['CodeID'];
				$selectChange="select * from code where ID='".$codeId."'";
				$exeChange=$objReport->ExecuteQuery($selectChange, "select");
				$change=$exeChange[0]['Points'];
				$newPoint= $begin_points +$change;
				$pointsArr[]=$newPoint;
				$csvFile =array(date("d/m/Y",strtotime($date)),$change,$newPoint,$exe_res[0]['Notes'],$objReport->getuserbyId($exe_res[0]['CreatedBy']));					
				
			}else{
				$pointsArr[]=$begin_points;
			$csvFile =array(date("d/m/Y",strtotime($date)),'0',$begin_points,$exe_res[0]['Notes'],$objReport->getuserbyId($exe_res[0]['CreatedBy']));					
				
			}
		}
	}
	$average=round((array_sum($pointsArr))/$noofdays,2);
	$csvFile =array('Average');
	$csvFile =array($average);		
fputcsv($output, $csvFile);
fclose($output);	
}*/

header("Content-type: application/octet-stream");
header("Content-disposition:attachment; filename=Research.xls");
?>
