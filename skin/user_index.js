function showid(oid)
{
	var obj=document.getElementById(oid);
	obj.style.display=obj.style.display=="none"?"block":"none";
	return false;
}

//w��ͼƬ���,h��ͼƬ�߶�, w=0ʱ���߶���С, h=0ʱ�������С
function drawpic(obj,w,h)
{
	if (w==0)
	{if (obj.clientHeight>h){obj.style.height=h+'px';}return true;}
	
	if (h==0)
	{if (obj.clientWidth>w){obj.style.width=w+'px';}return true;}
	
	if (obj.clientHeight/obj.clientWidth>(h/w))
	{obj.style.height=h+'px';}
	else
	{obj.style.width=w+'px';}
}

//������֤��
function chgvcode()
{
	var obj=document.getElementById("verify");
	obj.src=obj.src+"?rnd=2";
	return false;
}
