<?php
include('settings.kak');
$connect = mysql_connect($host, $account, $password);
$db = mysql_select_db($dbname, $connect) or die("������ ����������� � ��");
$setnames = mysql_query( 'SET NAMES utf8' );
$back="";
if (!isset($_REQUEST['backcall'])){
    $back="&backcall=".$_REQUEST['backcall'];
}
$pageidp = "2.0/auth/login/?application_id=".$appidlogin."&redirect_uri=http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$back;	

if (!isset($_REQUEST['status'])){
    Header("Location: https://api.{$wot_host}/{$pageidp}");
	exit();
}
if ($_REQUEST['status']=="ok"){
	$user=$_REQUEST['account_id'];
	$token=$_REQUEST['access_token'];
	setcookie("user",$user,time()+604800);
	setcookie("atoken",$token,time()+604800);
	$pl = mysql_query("select name, idc from player  where idp='$user' order by `date` desc",$connect);
	if (mysql_errno() <> 0) echo "MySQL Error ".mysql_errno().": ".mysql_error()."\n";
	$userd=mysql_fetch_array($pl,MYSQL_ASSOC);
	$username=$userd['name'];
	$date=time();
	$ip=$_SERVER['REMOTE_ADDR'];
	$cntr=geoip_country_code_by_name ( $ip );
	$pl1 = mysql_query("insert into access_log (idp,name, country, date,ip,token) values ('$user','$username','$cntr','$date','$ip','$token')",$connect);
	if (mysql_errno() <> 0) echo "MySQL Error ".mysql_errno().": ".mysql_error()."\n";
	
	$idc=$userd['idc'];
	if ($idc<>NULL){
		setcookie("idc",$idc,time()+604800);
		foreach ($clan_array as $clan_i) {
			$idc_temp = $clan_i["clan_id"];
			if ($idc == $idc_temp) {
				
				// print_r ($_REQUEST);
				// print_r ($_SERVER);
				// exit;
				if (isset($_REQUEST['backcall'])){
					
					Header("Location: {$_REQUEST['backcall']}");
					exit();	
				}
				Header("Location: wotstat.php?idc={$idc}");
				exit();	
			}
		}
		
	}else{
		setcookie("idc");
	}
}
Header("Location: wotstat.php");
exit();	
?>