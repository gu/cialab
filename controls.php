<?php 
include("SecurePage.php"); 
include("GlobalVariables.php"); 
include("GlobalFunctions.php"); 

if($_SESSION['Permissions']['view_controls'] != 1)
{
	header("Location:".$MainIndex);
}

Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<TITLE>Login To Barcode Database</TITLE>
<link rel="stylesheet" type="text/css" href="./css/indexstyle.css" />
<link href="toChecklist.css" rel="stylesheet" type="text/css" />

<!-- Load Helper Function -->
<script language="Javascript" src="MD5.js"></script>
<script language="Javascript" src="qtip.js"></script>

<!-- Load jQuery Functions -->
<script language="Javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script language="Javascript" src="toChecklist.js"></script>

<div id='testme2' style='display:none;position:absolute;height:100%;width:100%;left:0px;top:0px;opacity:0.4;filter:alpha(opacity=40);background-color:black;'></div>
<div id='testme' style='text-align:center;display:none;z-index:200;position:absolute;top:50%;margin-top:-50px;left:50%;margin-left:-200px;width:400px;background-color:white;border:solid 1px black;height:100px;padding:10px;font-family:Arial;'>
<div id='YesNoText'>
Are you sure you want to remove from the DataFields with all of its Data?
</div>
<table style='width:400px;height:50px;position:absolute;bottom:0px;'>
	<tr>
		<td style='width:200px;'>
			<input type='button' value='Yes' name='Yes' style='width:120px;' onClick="get(VarDivName,VarIdentity,VarDisplay,false,'');HideYesNo();">
		</td>
		
		<td style='width:200px;'>
			<input type='button' value='No' name='No' style='width:120px;' onClick='HideYesNo();'>
		</td>
	</tr>
</table>
</div>
<div id="TotalPage">
	<div id="Content">
		<div id="Header"></div>
		<div id="NavBar">
			<?php
			print_head("controls.php");
			?>
		</div>
		<div id="CenterPage">
		<script language='Javascript'>

			var VarDivName,VarIdentity,VarDisplay;
			var FieldCounter = 1;
			function removeOption(ElementID,ElementValue)
			{
				var elSel = document.getElementById(ElementID);
				var i;
				for (i = elSel.length - 1; i>=0; i--)
				{
					if (elSel.options[i].value == ElementValue)
					{
						elSel.remove(i);
					}
				}
			}
			function addOption(ElementID, OptionValue, OptionText)
			{
				var selectObj = document.getElementById(ElementID);
				try
				{
					selectObj.add(new Option(OptionText, OptionValue), null);
				}
				catch(e)
				{
					selectObj.add(new Option(OptionText, OptionValue))
				}
			}
			function removeAllOptions(ElementID)
			{
				var selectObj = document.getElementById(ElementID);
				var i;
				for (i = selectObj.length - 1; i>=0; i--)
				{
					selectObj.remove(i);
				}
			}
			function clearDiv(divID)
			{
				document.getElementById(divID).innerHTML = '';
			}
			function sendOptionsToHiddenFields(targertSelectId,targetFormId,FieldName)
			{
				var selectObj = document.getElementById(targertSelectId);
				var formObj = document.getElementById(targetFormId);
				for (var i=0; i<selectObj.options.length; i++)
				{
					//alert(selectObj.options[i].value);
					formObj.innerHTML += "<input name='"+FieldName+"[]' type='hidden' value='"+selectObj.options[i].value+"'>";
				}
			}
			function WebRequest(URL,DivName,AddToFront,AddToBack)
			{
				if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				{
					xmlhttp=new XMLHttpRequest();
				}
				else // code for IE6, IE5
				{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function()
				{
					if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						document.getElementById(DivName).innerHTML = AddToFront + xmlhttp.responseText + AddToBack; 
						tooltip.init();
					}
				}
				xmlhttp.open("GET",URL,true);
				xmlhttp.send();
			}
			
			function ScriptWebRequest(URL)
			{
				if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				{
					xmlhttp=new XMLHttpRequest();
				}
				else // code for IE6, IE5
				{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				
				xmlhttp.onreadystatechange=function()
				{
					if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						returnScriptWebRequest(xmlhttp.responseText);
					}
				};
				xmlhttp.open("GET",URL,true);
				xmlhttp.send();
			}
			
			function returnScriptWebRequest(requestData)
			{
				eval(requestData);
			}
			
			function UpdateSelectUsers(projectID,addReview,addAny)
			{
				removeAllOptions('users');
				var addOnString = "";
				
				if(addReview == true)
				{
					addOnString += "&addReview=true";
				}
				else
				{
					addOnString += "&addReview=false";
				}
				
				if(addAny == true)
				{
					addOnString += "&addAny=true";
				}
				else
				{
					addOnString += "&addAny=false";
				}
				
				ScriptWebRequest("ControlPanelServer.php?project="+projectID+"&selectid=users"+addOnString);
			}
			function test()
			{
				alert('test');
			}
			function uploadComplete(returnString)
			{
				updateResults(returnString,true);
				//$('#Results').css('display','block');
				//$('#Results').html(returnString);
			}
			function updateResults(HTMLtext,fade)
			{
				//Display the data
				$('#Results').stop();
				$('#Results').css('display','none');
				$('#Results').html(HTMLtext);
				
				//If fade then fade the data in and out.
				if(fade==true)
				{
					$('#Results').fadeIn(3000);
					$('#Results').fadeOut(3000);
				}
				else
				{
					$('#Results').css('display','block');
				}
			}
			function uploadStart()
			{
				$('#Results').css('display','block');
				$('#Results').html("<img src='./images/loading.gif'>");
			}
			function PostWebRequest(URL,parameters,DivName,AddToFront,AddToBack)
			{
				if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				{
					xmlhttp=new XMLHttpRequest();
				}
				else // code for IE6, IE5
				{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function()
				{
					if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						//store responseText to variable
						var responseText = xmlhttp.responseText;
						
						//Check for fade token, fade=true is default
						var noFadeArray = responseText.split("<NOFADE>");
						var fade = true;
						if(noFadeArray.length > 1)
						{
							fade = false;
							responseText = noFadeArray[0]+noFadeArray[1];
						}
						
						//Check for SCRIPTCOMMAND token
						var callArray = responseText.split("<SCRIPTCOMMAND>");
						var HTMLtext = "";
						if(callArray.length > 1)
						{
							HTMLtext=AddToFront + callArray[0] + AddToBack;
							//document.getElementById(DivName).innerHTML = AddToFront + callArray[0] + AddToBack;
							var ret = eval(callArray[1]);
						}
						else
						{
							HTMLtext=AddToFront + responseText + AddToBack;
							//document.getElementById(DivName).innerHTML = AddToFront + xmlhttp.responseText + AddToBack; 
						}
						
						//Display the data
						$('#'+DivName).stop();
						$('#'+DivName).css('display','none');
						$('#'+DivName).html(HTMLtext);
						
						//If fade then fade the data in and out.
						if(fade==true)
						{
							$('#'+DivName).fadeIn(3000);
							$('#'+DivName).fadeOut(3000);
						}
						else
						{
							$('#'+DivName).css('display','block');
						}
					}
				}
				xmlhttp.open('POST', URL, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", parameters.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.send(parameters);
			}
			function UpdateUserControls(id)
			{
				document.getElementById('Results').innerHTML="";
				//document.getElementById('Results').style.display = "none";
				document.getElementById('DataFields').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				TableTopHTML = "<table id='DataForm'>"; 
				TableBottomHTML = "<tr><td></td><td></td><td><input type='submit' name='Save' value='Save'></td></tr></table>";
				WebRequest("ControlPanelServer.php?userfield=" + id,'DataFields',TableTopHTML,TableBottomHTML);
			}
			function UpdateROIStatistics()
			{
				document.getElementById('Results').style.display = "none";
				document.getElementById('roiStats').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				WebRequest("ControlPanelServer.php?userid=" + $('#users').find(":selected").val() + "&projectid=" + $('#projects').find(":selected").val() + "&roistatistics=true",'roiStats',"","");
			}
			function UpdateProjectControls(id,callback)
			{
				document.getElementById('Results').innerHTML="";
				//document.getElementById('Results').style.display = "none";
				document.getElementById('DataFields').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				TableTopHTML = "<table id='DataForm'>"; 
				TableBottomHTML = "<tr><td></td><td></td><td><input type='submit' name='Save' value='Save'></td></tr></table><div id='hiddenFields'></div>";
				WebRequest("ControlPanelServer.php?projectfield=" + id,'DataFields',TableTopHTML,TableBottomHTML);
				
				$(document).ready(function () {
				callback();
				});
			}
			function UpdateDataFieldControls(id)
			{
				document.getElementById('Results').innerHTML="";
				//document.getElementById('Results').style.display = "none";
				document.getElementById('DataFields').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				TableTopHTML = "<table id='DataForm'>"; 
				TableBottomHTML = "<tr><td></td><td></td><td><input type='submit' name='Save' value='Save'></td></tr></table>";
				WebRequest("ControlPanelServer.php?datafield=" + id,'DataFields',TableTopHTML,TableBottomHTML);
			}
			function UpdateDataSet(id)
			{
				document.getElementById('Results').innerHTML="";
				//document.getElementById('Results').style.display = "none";
				document.getElementById('DataFields').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				TableTopHTML = "<table id='DataForm'>"; 
				TableBottomHTML = "<tr><td></td><td></td><td><input type='submit' name='Save' value='Save'></td></tr></table>";
				WebRequest("ControlPanelServer.php?dataset=" + id,'DataFields',TableTopHTML,TableBottomHTML);
			}
			function DisplayPanel(name)
			{
				//document.getElementById('CenterPageRight').innerHTML="<div id='loadingimg' style='width:100px;height:100px;margin-left:100px;margin-right:100px;'><img src='./images/loading.gif'></div>"; 
				WebRequest("ControlPanelServer.php?DisplayPanel=" + name,'CenterPageContent',"","");
			}
			function get(DivName,Identity,Display,Verify,VerifyMessage)
			{
				VarDivName = DivName;
				VarIdentity = Identity;
				VarDisplay = Display;
				
				if(Verify == true)
				{
					YesNoDialog(DivName,Identity,Display,VerifyMessage);
				}
				else
				{
					var AllData = new Array();
					AllData.push(Identity+"=true");
					var obj = document.getElementById(DivName);
					for (i=0; i<obj.getElementsByTagName("input").length; i++) 
					{
						if (obj.getElementsByTagName("input")[i].type == "password") 
						{
							if(obj.getElementsByTagName("input")[i].value != "")
							{
								AllData.push(obj.getElementsByTagName("input")[i].name + "=" + MD5(obj.getElementsByTagName("input")[i].value));
							}
						}
						if (obj.getElementsByTagName("input")[i].type == "text") 
						{
							if(obj.getElementsByTagName("input")[i].name == "password")
							{
								if(obj.getElementsByTagName("input")[i].value != "")
								{
									AllData.push(obj.getElementsByTagName("input")[i].name + "=" + MD5(obj.getElementsByTagName("input")[i].value));
								}
							}
							else
							{
								AllData.push(obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value);
							}
						}
						if (obj.getElementsByTagName("input")[i].type == "checkbox") 
						{
							if (obj.getElementsByTagName("input")[i].checked) 
							{
								AllData.push(obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value);
							} 
							else 
							{
								AllData.push(obj.getElementsByTagName("input")[i].name + "=");
							}
						}
						if (obj.getElementsByTagName("input")[i].type == "radio") 
						{
						   if (obj.getElementsByTagName("input")[i].checked) 
						   {
								AllData.push(obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value);
						   }
						}
						if (obj.getElementsByTagName("input")[i].type == "hidden") 
						{
							AllData.push(obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value);
						}
					}
					var SelectTags = obj.getElementsByTagName('select');
					for (i=0;i<SelectTags.length;i++)
					{
						AllData.push(SelectTags[i].name + "=" + encodeURI(SelectTags[i].value));
					}
					var TextAreaTags = obj.getElementsByTagName('textarea');
					for (i=0;i<TextAreaTags.length;i++)
					{
						AllData.push(TextAreaTags[i].name + "=" + encodeURI(TextAreaTags[i].value));
					}
					
					PostWebRequest('<?php echo $URL; ?>/ControlPanelServer.php', AllData.join('&'),Display,'','');
				}
		   }
		   function YesNoDialog(DivName,Identity,Display,VerifyMessage)
		   {
			   document.getElementById('YesNoText').innerHTML = VerifyMessage;
			   document.getElementById('testme2').style.display = "block";
			   document.getElementById('testme').style.display = "block";
		   }
		   function HideYesNo()
		   {
				document.getElementById('testme2').style.display = "none";
				document.getElementById('testme').style.display = "none";
		   }
		   function AddField()
		   {
				//document.getElementById('DataSet').innerHTML = document.getElementById('DataSet').innerHTML + "<tbody id='DataSetFields'><tr><td style='text-align: right;'>Value: </td><td></td><td><input type='text' style='width: 145px;' name='first_name' value='' tiptitle='This specifies the first name of the user.'></td><td></td></tr></TBODY>";
				var tbody = document.getElementById('DataSetFields');
				var row = document.createElement("TR");
				row.setAttribute('id',FieldCounter);
				var td1 = document.createElement("TD");
				var td2 = document.createElement("TD");
				var td3 = document.createElement("TD");
				var td4 = document.createElement("TD");
				var input1 = document.createElement("INPUT");
				input1.setAttribute('type','text');
				input1.setAttribute('tiptitle','Set Data');
				input1.setAttribute('name','datavalue_' + FieldCounter);
				var removeElement = document.createElement("A");
				var minusImage = document.createElement("IMG");
				minusImage.setAttribute('src','./images/minus.png');
				minusImage.setAttribute('border','0px');
				minusImage.setAttribute('tiptitle','Remove Field');
				//removeElement.appendChild(document.createTextNode("remove"));
				removeElement.appendChild(minusImage);
				removeElement.setAttribute('href',"javascript:removeField(" + FieldCounter + ")");
				removeElement.setAttribute('style',"text-decoration:none;");
				
				td1.appendChild(document.createTextNode("Value: "));
				td1.setAttribute('style','text-align: right;');
				td3.appendChild(input1);
				td4.appendChild(removeElement);
				row.appendChild(td1);
				row.appendChild(td2);
				row.appendChild(td3);
				row.appendChild(td4);
				tbody.appendChild(row);
				
				FieldCounter++;
		   }
		   function removeField(FieldId)
		   {
				var p2 = document.getElementById(FieldId);
				p2.parentNode.removeChild(p2); 
		   }
		</script>
		<style type="text/css">
		#macSpacerTop
		{
			background-color:rgb(212,210,213);
			margin-left:2px;
			margin-right:2px;
			width:98%;
			float:left;
			height:2px;
		}
		#macSpacerBottom
		{
			background-color:rgb(239,237,240);
			margin-left:2px;
			margin-right:2px;
			width:98%;
			float:left;
			height:2px;
		}
		#ControlPanelMiniTitle
		{
			margin-left:4px;
			margin-top:10px;
			width:146px;
			text-decoration: underline;
			font-weight:bold;
			font-size:14px;
			float:left;
			text-align:left;
		}
		#ControlPanelLink a
		{
			width:137px;
			text-decoration:none;
			float:left;
			text-align:left;
			clear: both;
			color: black;
			margin-left: 10px;
			cursor:pointer;
		}
		#ControlPanelLink a:hover
		{
			color:blue;
			text-decoration:none;
			cursor:pointer;
		}
		#ControlPanelTop
		{
			color:#4B4B4B;
			height: 32px;
			float:left;
			width:153px;
			text-align:center;
			font-weight:bold;
			font-size:20px;
			padding-top:8px;
			border-bottom:1px solid #D4D4D4;
			display:inline;
		}
		#ControlPanelBottom
		{
			float:left;
			width:153px;
			height: 100%;
			display:inline;
			background-color: rgb(256,256,256);
			color: rgb(7,7,9);
			overflow:hidden;
		}
		#PanelTop
		{
			color:#4B4B4B;
			height: 32px;
			float:right;
			width:600px;
			text-align:center;
			font-weight:bold;
			font-size:20px;
			padding-top:8px;
			border-bottom:1px solid #D4D4D4;
			display:inline;
		}
		#PanelBottom
		{
			padding-top:10px;
			float:right;
			width:600px;
			height:100%;
			text-align: left;
			display:inline;
		}
		#spacer
		{
			width:100%;
			float:left;
			height:10px;
		}
		</style>
			<div id="CenterPageLeft">
				<div id='ControlPanelTop'>
				<b>Control Panel</b>
				</div>
				<div id='ControlPanelBottom'>
					<div id='ControlPanelMiniTitle'>User Controls:</div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(0)'>- Add Users</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(1)'>- Remove Users</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(2)'>- Edit Users</a></div>
					<div id='spacer'></div>
					<div id="ControlPanelMiniTitle">DataField Controls:</div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(3)'>- Add Fields</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(4)'>- Remove Fields</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(5)'>- Edit Fields</a></div>
					<div id='spacer'></div>
					<div id="ControlPanelMiniTitle">DataSet Controls:</div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(6)'>- Add Data Set</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(7)'>- Remove Data Set</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(8)'>- Edit Data Set</a></div>
					<div id='spacer'></div>
					<div id="ControlPanelMiniTitle">Project Controls:</div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(9)'>- Add Project</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(10)'>- Remove Project</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(11)'>- Edit Project</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(12)'>- Download Data</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(17)'>- Download Counts</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(18)'>- Download Users</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(13)'>- Upload Review</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(16)'>- Add User Marks</a></div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(15)'>- Clear Review</a></div>
					<div id='ControlPanelMiniTitle'>Statistics:</div>
					<div id='ControlPanelLink'><a OnClick='DisplayPanel(14)'>- ROI Stats</a></div>
					<div id='ControlPanelLink'><a href='UserStats.php'>- Old ROI Stats</a></div>
				</div>
			</div>
			<div id="CenterPageRight">
				<div id="CenterPageContent" style='float:left;width:100%;'>
					<h2>Welcome to the Control Panel</h2>
					<br>
					Click on the contols on the lefthand side to select your action.
				</div>
				<div id='Results' style='float:left;width:100%;border-bottom:1px solid #D4D4D4;border-top:1px solid #D4D4D4;display:none;'>
				</div>
			</div>
		</div>
		<div id="Footer"></div>
	</div>
</div>

</HTML>
