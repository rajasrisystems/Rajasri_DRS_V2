<?php 
class Report extends MysqlFns
{
	function Report()
		{
		global $config;
        	$this->MysqlFns();
		$this->Offset			= 0;
		$this->Limit			= 15;
		$this->page				= 0;
		$this->Keyword			= '';
		$this->Operator			= '';
		$this->PerPage			= '';
		}
	// All resources
	function allresource()
	{
		global $objSmarty;
		$getmon = $_REQUEST['month'];
		$getyr  = $_REQUEST['year'];
		
		$date=$getmon.'/'.$getyr;
		$select="select * from rating r,resource re where date_format(r.RatingDate, '%m/%Y')='".$date."' and re.ID=r.ResourceID group by r.ResourceID";
		$exeresource = $this->ExecuteQuery($select,"select");
		$objSmarty->assign('resdata',$exeresource);
			
	}
	function oneresource()
	{
		global $objSmarty;
		$getmon = $_REQUEST['month'];
		$getyr  = $_REQUEST['year'];
		$getini = $_REQUEST['newresid'];
		
		$date=$getmon.'/'.$getyr;
		$select="select * from rating where ResourceID='".$getini."' and date_format(RatingDate, '%m/%Y')='".$date."'";
		$exeresource = $this->ExecuteQuery($select,"select");
		$objSmarty->assign('oneresdata',$exeresource);
	
	}
	function getuserbyId($id)
	{
		global $objSmarty,$config;
		//Get the details from table for edit option
		$tempdisvar= "SELECT * FROM admin where ID= ' $id' ";
		$displaydet= $this->ExecuteQuery($tempdisvar, "select");
		//$objSmarty->assign('adminDetails', $displaydet);
		return $displaydet[0]['Username'];
	}
	function getb($m,$i,$y)
	{
		global $objSmarty,$config;
		$getyr  =$y ;
		$getmon =$m;
		$getini = $i;
		$firstDate=$getyr.'-'.$getmon.'-01';
		$date=date('Y-m-d', strtotime($firstDate . ' -1 month'));
		$numberOfDays=cal_days_in_month(CAL_GREGORIAN, $getmon, $getyr);
		$lastDate=date("Y-m-t", strtotime($firstDate));
		$date=$firstDate;
		$datesArr[]=$date;
		for($i=1;$i<$numberOfDays;$i++)
		{
			$date=date('Y-m-d', strtotime($date . ' +1 day'));
			$datesArr[]=$date;
		}
		$c=0;
		$today=date('Y-m-d');
		$onlydate=date('d');
		$nnprepoints = $onlydate * $begin_points;
		$noofdays = 0;
		foreach($datesArr as $date)
			{
			if($date <= $today)
			{
				$noofdays++;	
				$select_res="select * from rating where ResourceID='".$getini."' and RatingDate='".$date."' order by RatingDate desc";
				$exe_res=$this->ExecuteQuery($select_res, "select");
				$count_res=$this->ExecuteQuery($select_res, "norows");
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
					while($row2=mysql_fetch_array($exequery))
					{
						$sql = "SELECT * FROM code where ID='".$row2['CodeID']."'";
						$getexe = $this->ExecuteQuery($sql, "select");
						$points+= $getexe[0]['Points'];
					}
				}
				if($points != '')
				{
					$tot= ($points + $prepoints)/$month_days;
					return $begin_points=(round($tot,2));
				}
				else
				{
					return $begin_points=50;
				}
			}
			}
	}	
	function gete($m,$i,$y)
	{
	$getmon = $m;
	$getyr  = $y;
	$getini = $i;
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
	$c=0;
	$today=date('Y-m-d');
	$onlydate=date('d');
	$nnprepoints = $onlydate * $begin_points;
	$noofdays = 0;
	foreach($datesArr as $date){
		if($date <= $today){
			$noofdays++;	
			$select_res="select * from rating where ResourceID='".$getini."' and RatingDate='".$date."' order by RatingDate desc";
			$exe_res=$this->ExecuteQuery($select_res, "select");
			$count_res=$this->ExecuteQuery($select_res, "norows");
			/******************************** For Previous Month**************************************/	
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
					$getexe = $this->ExecuteQuery($sql, "select");
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
			/******************************** For Previous Month**************************************/
			$change=0;
			if($count_res > 0){
			$mulvar="select * from rating where ResourceID='".$getini."' and RatingDate='".$date."' order by RatingDate desc";
			$mulquery=mysql_query($mulvar);
			$num_rows=mysql_num_rows($mulquery);
			while($mulrow=mysql_fetch_array($mulquery)){
					$mulcode=$mulrow['CodeID'];
					$mulselect="select * from code where ID='".$mulcode."'";
				$multiple=$this->ExecuteQuery($mulselect, "select");
				$change +=$multiple[0]['Points'];
				$newPoint= $begin_points +$change;
				}
				$newcodevar[]=$exe_res[0]['CodeID'];
				$pointsArr[]=$newPoint;
			}else{
				$pointsArr[]=$begin_points;
			}
		}
	}
	return $average=round((array_sum($pointsArr))/$noofdays,2);
	}		
}
?>
