<?php
	include 'includes/common.php';
	include_once "includes/classes/class.resource.php";
	$objResource = new Resource();
	
	if(isset($_REQUEST['hdAction']) && $_REQUEST['hdAction']!='')
	{
		$objResource -> ResourceAdding();
	}
	if($_REQUEST['updateAction']=='1') // 'updateAction'---> hidden id
	{
		$objResource->Resource_update($_REQUEST['ResID']); // Resource_update --> Update Function: ResID -->Hidden ID
	}
	if($_REQUEST['Rs_Id']!='')
	{
		$objResource->getResourcebyId($_REQUEST['Rs_Id']); //($_REQUEST['Rs_Id']); -->Passing ID as parameter;
	}
	$objResource->show_resource();
	$objSmarty->assign('objReport',$objResource);
	$objSmarty->assign('activePage',"4");
	$objSmarty->assign('IncludeTpl',"resource.tpl");
	$objSmarty->display("pagetemplate.tpl");
?>
