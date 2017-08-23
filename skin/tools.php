<?php
header('Content-type:text/html;charset=utf-8');
define(SITE,'深圳龙华成校会计培训');
date_default_timezone_set('Etc/GMT-8');
extract($_REQUEST);
session_start();

/* ......Config over...... */
function sql_check($sql_str) {       
  if (eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile',$sql_str)) //进行验证
  {alert("提交的参数非法！");}
  else {return $sql_str;}
}

function id_check($id=0) {       
  if (!$id) {$id=0;}    // 是否为空判断
  elseif (!is_numeric($id)) {alert('提交的参数非法！');}    // 数字判断
  $id = intval($id);  // 整型化
  return $id;
}

//警告提示函数。$str=提示的文字,$topage=转向的页面,$at=调用警告框
function alert($str,$topage='',$at='alert'){
	if ($topage){$goto="this.location.href='$topage'";}
	else{$goto='history.go(-1)';}
	if ($at=='alert'){$at="alert('$str');";}
	else{$at='';}
	echo '<script type="text/javascript">'.$at.$goto.';</script>';
	exit;
}

//重写标题 默认长度为12个字符
function retitle($c='',$l=12){
	if (is_null($c) || $c==''){return '';break;}
	if (mb_strlen($c,'utf-8')> $l){$c=mb_substr($c,0,$l-3,'utf-8').'...';}
	return $c;
}

//重写日期 $d未格式化的日期,$fl非今天的日期格式,$ft今天的日期格式
function redate($d,$fl='Y-m-d',$ft='H:i'){
	if (date('Y-m-d',strtotime($d))==date('Y-m-d')){$rd=date($ft,strtotime($d));}
	else{$rd=date($fl,strtotime($d));}
	return $rd;
}

//转换编码
function enc($c){
return iconv('gb2312','utf-8',$c);
}
function dec($c){
return str_replace('\"','"',iconv('utf-8','gb2312',$c));
}

//通过ID获取资料
function getname($uid,$name,$table){
	global $conn;
	$gu=$conn->execute("select $name from $table where $uid");
	if (!$gu->eof){return enc($gu[0]);}
	else{return '';}
	$gu->close;
}

//前台分页函数
function page_front($c_file=''){
	//全局化变量
	global $rs;
	global $page;
	global $url;
	echo "<div class='page'>";
	if ($page<=1){echo "<span>上页</span> |";}
	else {echo "<a href='/".$c_file."_p".($page-1)."'>上页</a> |";}
	for ($i=$page-5;$i<=$page+5;$i++)
	{
		if ($i<=0){continue;}
		if ($i>$rs->pagecount){break;}
		if ($i==$page){$curpage=" class='ps'";}else{$curpage="";}
		echo " <a href='/".$c_file."_p".$i."'$curpage>[$i]</a> ";
	}
	if ($page==$rs->pagecount){echo "| <span>下页</span>";}
	else {echo "| <a href='/".$c_file."_p".($page+1)."'>下页</a>";}
	echo "</div>";
}
//前台分页函数
function page_front2($c_file=''){
	//全局化变量
	global $rs;
	global $page;
	global $url;
	echo "<div class='page'>";
	if ($page<=1){echo "<span>上页</span> |";}
	else {echo "<a href='$c_file?page=".($page-1).$url."'>上页</a> |";}
	for ($i=$page-2;$i<=$page+2;$i++)
	{
		if ($i<=0){continue;}
		if ($i>$rs->pagecount){break;}
		if ($i==$page){$curpage=" style='color:#f00;'";}else{$curpage="";}
		echo " <a href='$c_file?page=".$i.$url."'$curpage>[$i]</a> ";
	}
	if ($page==$pagecount){echo "| <span>下页</span>";}
	else {echo "| <a href='$c_file?page=".($page+1).$url."'>下页</a>";}
	echo "</div>";
}
//后台分页函数
function page_back($c_file=''){
	global $rs;
	global $page;
	global $url;
	echo "<div class='page'>页码：".$page."/".$rs->pagecount."页　总计：".$rs->recordcount."条　每页：".$rs->pagesize."条";
	if ($page<=1){echo "　<span style='color:#888;'>首　页 | 上一页</span>";}
	else{echo "　<a href='?page=1".$url."'>首　页</a> | <a href='?page=".($page-1).$url."'>上一页</a>";}
	if ($page>=$rs->pagecount){echo " | <span style='color:#888;'>下一页 | 尾　页</span>";}
	else{echo " | <a href='?page=".($page+1).$url."'>下一页</a> | <a href='?page=".$rs->pagecount.$url."'>尾　页</a>";}
	echo "　转到: <select size='1' name='page' onchange=\"javascript:document.location.href='?page='+this.options[this.selectedIndex].value+'".$url."';\">";
	for ($i=1;$i<=$rs->pagecount;$i++){
		if ($i==$page){echo "<option value='$i' selected='selected'>-$i-</option>";}
		else{echo "<option value='$i'>-$i-</option>";}
	}
  echo '</select></div>';
}

//通过分类名获取杂项名列表
function classname($n,$v=0){
	global $conn;
	$temp=$conn->execute("select id,classname from class_1 where sortname='".dec($n)."' order by orderno asc,id asc");
	$text='';
	while (!$temp->eof){
		if ($v==$temp[0]){$sel=' selected="selected"';}
		else{$sel='';}
		$text.='<option value="'.$temp[0].'"'.$sel.'>'.$temp[1].'</option>';
		$temp->movenext;
	}
	$temp->close;
	return enc($text);
}

//上传文件函数
function upfile($bn){
	$uf=$_FILES[$bn];
	if (substr($uf['type'],0,5)!='image')
	{alert('上传文件的类型不符合要求');}
	if ($uf['size']<=0 || $uf['size']>30000000)
	{alert('文件大小为 1Kb-30Mb，请处理后再上传！');}
	//生成日期+随机数文件名
	$filepath='../../product/';
	$fn=date('YmdHis').mt_rand(100,999).substr($uf['name'],strlen($uf['name'])-4);
	move_uploaded_file($uf['tmp_name'],$filepath.$fn);
	return 'product/'.$fn;
}
//上传文件函数
function upsoftware($bn){
	$uf=$_FILES[$bn];
	list($fname,$fext)=split('[/.-]',$uf["name"]);
	if (strripos(',doc,xls,rar,txt,zip,',$fext)===False)
	{alert('上传文件的类型不符合要求');}
	if ($uf['size']<=0 || $uf['size']>30000000)
	{alert('文件大小为 1Kb-30Mb，请处理后再上传！');}
	//生成日期+随机数文件名
	$filepath='../../product/';
	$fn=date('YmdHis').mt_rand(100,999).substr($uf['name'],strlen($uf['name'])-4);
	move_uploaded_file($uf['tmp_name'],$filepath.$fn);
	return 'product/'.$fn;
}
//首页新闻列表
function index_news($table='news',$sid=9,$num=9){
	global $conn;
	$temp=$conn->execute("select top $num * from $table where sortid=$sid order by indate desc,id desc");
	while(!$temp->eof){
		echo '<li>·<a target="_blank" href="/',CPATH,'duxinwen.php?id=',$temp[0],'" title="',enc($temp['title']),'">',enc($temp['title']),'</a></li>';
		$temp->movenext;
	}
	$temp->close;
}

?>