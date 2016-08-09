<?php
class Rating extends MysqlFns
{
	function insertdetails()
	{
		global $objSmarty,$config;
		$rat_date=explode("/", $_REQUEST['ratingdate']);
		$newdate=$rat_date[2]."-".$rat_date[1]."-".$rat_date[0];
		$newResourceid = $_REQUEST['resource'];
		$newnotes=$_REQUEST['notes'];	
		$enterCode= $_REQUEST['code'];
		// Checking for duplicate records in rating table
		$checkvar = "SELECT * FROM rating WHERE RatingDate = '$newdate' AND ResourceID = '$newResourceid' AND CodeID = (SELECT ID FROM code WHERE Code='$enterCode') ";
		$execvar = mysql_query($checkvar);
		if (mysql_num_rows($execvar) > 0) 
		{
			return false;
       		}
		else
		{		
		// Insert details into rating table
		$a = "INSERT INTO rating(RatingDate,
					 ResourceID,
					 CodeID,
					 Notes,
					 CreatedBy) 
				         VALUES 
				      	 ('$newdate','$newResourceid',(SELECT ID FROM code WHERE Code='$enterCode'),'$newnotes','".$_SESSION['UserId']."')"; 
		$this->ExecuteQuery($a, "insert");
		header("location:rating.php?successmsg=3");// redirecting
		}
	}
	
	function display()
	{
		global $objSmarty,$config;
		$orderBy='';
		if(isset($_REQUEST['sortflag']) && $_REQUEST['sortflag']!='')
		{
			if($_REQUEST['sortflag']=='1')
			{
				$orderBy.="order by ResourceInitial asc";
			}
			elseif($_REQUEST['sortflag']=="2")
			{
				$orderBy.="order by ResourceInitial desc";
			}
			elseif($_REQUEST['sortflag']=='3'){
				$orderBy.="order by Code asc";
			}
			elseif($_REQUEST['sortflag']=='4'){
				$orderBy.="order by Code desc";
			}
			elseif($_REQUEST['sortflag']=='5'){
				$orderBy.="order by Notes asc";
			}
			elseif($_REQUEST['sortflag']=='6'){
				$orderBy.="order by Notes desc";
			}
			elseif($_REQUEST['sortflag']=='7'){
				$orderBy.="order by RatingDate asc";
			}
			elseif($_REQUEST['sortflag']=='8'){
				$orderBy.="order by RatingDate desc";
			}
			elseif($_REQUEST['sortflag']=='9'){
				$orderBy.="order by Username asc";
			}
			elseif($_REQUEST['sortflag']=='10'){
				$orderBy.="order by Username desc";
			}
		}
		else
		{
			$orderBy.="order by RatingDate asc";
		}
		$tempdisvar= "SELECT * FROM rating r,resource re,code c,admin a WHERE r.ResourceID=re.ID AND r.CodeID=c.ID  and a.ID=r.CreatedBy AND (r.RatingDate BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()) $orderBy";
		$displaydet= $this->ExecuteQuery($tempdisvar, "select");
		$objSmarty->assign('displaydet', $displaydet);
	}
	function managername($id)
	{
		global $objSmarty,$config;
		$dtempdisvar= "SELECT * FROM admin where ID= ' $id' ";
		$ddisplaydet= $this->ExecuteQuery($dtempdisvar, "select");
		return $ddisplaydet[0]['Username'];
	}
	function Update_rating($id)
	{
		global $objSmarty,$config;
		//$uprdate = $_REQUEST['ratingdate'];
		$rat_date=explode("/", $_REQUEST['ratingdate']);
		$uprdate=$rat_date[2]."-".$rat_date[1]."-".$rat_date[0];
		$upresid = $_REQUEST['resource'];
		$upnotes=$_REQUEST['notes'];	
		$upcodeid= $_REQUEST['code'];	
		// update details into Rating Table
		$tempvar = " UPDATE rating SET RatingDate= '$uprdate ',
					 ResourceID = '$upresid',
					 CodeID =(SELECT ID FROM code WHERE Code='$upcodeid'),
					 Notes='$upnotes' WHERE RatingID ='$id'";
		$this->ExecuteQuery($tempvar, "update");
		header("location:rating.php?successmsg=1");// redirecting
	}	
	function get_RatingID($id)
	{
		global $objSmarty,$config;
		//Get the details from table for edit option
		$datetemp = date("y/m/d");  
		$tempdisvar= "SELECT * FROM rating r,resource re,code c 
				   WHERE r.ResourceID=re.ID and r.CodeID=c.ID 
				   AND r.RatingID=' $id'";
		$displaydet= $this->ExecuteQuery($tempdisvar, "select");
		$objSmarty->assign('getRating', $displaydet);
	}
	function deleterow($id)
	{
		global $objSmarty,$config;
		//Delete the corresponding record from rating table 
		$tempdrop = "DELETE FROM rating WHERE RatingID ='$id'";
		$this->ExecuteQuery($tempdrop, "delete");
		header("location:rating.php?successmsg=2");// redirecting
	}
}
?>
