function marksDisplayed(userID)
{
	for(c=0;c<markerHolder.length;c++)
	{
		if(markerHolder[c].name == userID)
		{
			return true;
		}
	}
	return false;
}

function loadDropDown()
{
		$("#s3").dropdownchecklist( { firstItemChecksAll: true, width: 200, zIndex: 10000 
			,onItemClick: function(checkbox, selector)
				{
					var justChecked = checkbox.prop("checked");

					if(checkbox.prop("checked") == true)
					{
						if(checkbox.prop("value") == "all")
						{
							for( i = 0; i < selector.options.length; i++ )
							{
								selector.options[i].selected = true;
								//alert(selector.options[i].value);
								if (isNaN(selector.options[i].value) == false)
								{
									loadJSON(currentServer+'?action=getUsersImgMarks&pic='+CurrentImage+'&pid='+pid+'&userid='+selector.options[i].value,"Loading Image Marks");
								}
							}
						}
						else if(checkbox.prop("value") == "review_set")
						{
							//Check to see if review set is already loaded (This can happen if the user is reviewing a slide)
							if(marksDisplayed("review_set") == false)
							{
								//get review set.
								loadJSON(currentServer+'?action=getReviewImgMarks&pic='+CurrentImage+'&pid='+pid,"Loading Review Image Marks");
							}
						}
						else
						{
							//load specific user.
							loadJSON(currentServer+'?action=getUsersImgMarks&pic='+CurrentImage+'&pid='+pid+'&userid='+checkbox.prop("value"),"Loading Image Marks");
						}
					}
					else
					{
						if(checkbox.prop("value") == "all")
						{
							for( i = 0; i < selector.options.length; i++ )
							{
								selector.options[i].selected = false;
								if (isNaN(selector.options[i].value) == false)
								{
									//Remove Marker Set
									$('div[name='+'Marker_' + selector.options[i].value +']').remove();
									
									//Add colors back to color holder array.
									for(c=0;c<markerHolder.length;c++)
									{
										if(markerHolder[c].name == selector.options[i].value)
										{
											markerColors.splice(0,0,markerHolder[c].color);
											markerHolder.splice(c,c+1);
										}
									}
									
								}
							}
						}
						else
						{
							//Remove Marker Set
							$('div[name='+'Marker_' + checkbox.prop("value") +']').remove();
							
							for(c=0;c<markerHolder.length;c++)
							{
								if(markerHolder[c].name == checkbox.prop("value"))
								{
									//alert("C: " + c + "  |Color: " + markerHolder[c].color);
									markerColors.splice(0,0,markerHolder[c].color);
									markerHolder.splice(c,c+1);
								}
							}
						}
					}
				}
			}
			);
}

jQuery.fn.center = function () 
{
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

function DialogBox(Message,isYesNo,yesFunction,noFunction)
{
	//Hide select, This is a bug in IE 6 that puts a select argument on the highest z-index
	//Therefore hide object when displaying dialog boxes
	$('#project_selection').css('visibility','hidden');

	if($('#data_selection'))
	{
		$('#data_selection').css('visibility','hidden');
	}
	
	//Set CSS for Blackout Box
	//$('#blackout').css('display','inline');
	$('#blackout').css('opacoty',.5);
	$('#blackout').css('width',$(document).width());
	$('#blackout').css('height',$(document).height());
   
	//Set CSS and create infobox for info box
	var infoBox = jQuery('<div/>');
	infoBox.attr('id','infoBox');
	infoBox.css('width','600px');
	infoBox.css('overflow','visible');
	infoBox.css('zIndex','40002');
	//infoBox.css('display','block');
	infoBox.css('textAlign','center');
	infoBox.css('position','relative');
	infoBox.css('top','0px');
	//infoBox.css('left','50%');
	infoBox.css('padding-top','0px');
	infoBox.css('margin-right','auto');
	infoBox.css('margin-left','auto');
	infoBox.css('background','white');
	infoBox.css('border','solid 1px black');
	infoBox.css('padding','10px');
	infoBox.css('fontFamily','Arial');
	
	//Create Message Window
	var dialogMessage = jQuery('<div/>');
	dialogMessage.attr('id','dialogMessage');
	dialogMessage.css('zIndex','40003');
	dialogMessage.css('position','relative');
	dialogMessage.css('width','600px');
	dialogMessage.css('fontSize','16px');
	//dialogMessage.css('display','block');
	dialogMessage.append(Message);

	//Add Message to Box
	infoBox.append(dialogMessage);
	
	
	//Check if YesNo box or Ok box, then add the appropriate buttons
	if(isYesNo == true)
	{
		var yesButton = CreateButton('yesButton','Yes');
		yesButton.bind('click',yesFunction);
		infoBox.append(yesButton);
		
		var noButton = CreateButton('noButton','No');
		noButton.bind('click',noFunction);
		infoBox.append(noButton);
	}
	else
	{
		if(projectType == 6)
		{
			infoBox.css('top','200px');
		}
		else
		{
			var okButton = CreateButton('okButton','Ok');
			okButton.click(function(event){HideDialog();});
			infoBox.append(okButton);
		}
	}
	$('#infobar-inner').append(infoBox);
}

function CreateButton(idValue,textValue)
{
	var buttonObj = jQuery('<input/>');
	buttonObj.attr('id',idValue);
	buttonObj.attr('type','button');
	buttonObj.attr('value',textValue);
	buttonObj.css('fontSize','14px');
	buttonObj.css('height','30px');
	buttonObj.css('width','120px');
	buttonObj.css('display','inline');
	//buttonObj.css('float','left');
	buttonObj.css('display','block');
	buttonObj.css('marginLeft','auto');
	buttonObj.css('marginRight','auto');
	buttonObj.css('margin-top','10px');
	return buttonObj;
}

function HideDialog()
{
	if($('#data_selection'))
	{
		$('#data_selection').css('visibility','visible');
	}
	$('#project_selection').css('visibility','visible');
	$('#blackout').css('display','none');
	$('#infoBox').remove();
}


function createMarker(userid_value,color_value,x_point_value,y_point_value,r_value,zIndex_value,opacity)
{	
	var offset = $('#pointer_div').offset();
	
	//Create Object
	var Marker = jQuery('<div/>');
	
	//Set Attributes
	Marker.attr('name','Marker_'+ userid_value);
	
	//Set CSS
	if(r_value == 0)
	{
		Marker.css('background',color_value);
		Marker.css('font-size','0px');
		Marker.css('height','6px');
		Marker.css('width','6px');
		Marker.css('position','absolute');
		Marker.css('left',(x_point_value - 4));
		Marker.css('top',(y_point_value - 4 + 50));
		Marker.css('zIndex',zIndex_value);
		Marker.css('border','1px solid black');
		Marker.css('opacity',opacity);
	}
	
	if(r_value > 0)
	{
		Marker.css('color',color_value);
		Marker.css('position','absolute');
		Marker.css('left',(x_point_value - (r_value)));
		Marker.css('top',(y_point_value - (r_value) + 50));
		Marker.css('zIndex',zIndex_value);
		Marker.css('opacity',opacity);
		Marker.css('height',r_value*2);
		Marker.css('width',r_value*2);
		Marker.css('background-repeat', 'no-repeat');
		Marker.css('background-image','url(bigcircle.gif)');
		Marker.css('background-size','100%');
		Marker.css('background-size','100%');
		Marker.css('cursor','pointer');
		
		Marker.append("<div class='letter' style='position:absolute; display:block; margin:0px; left:-" + (r_value*2*0.15) + "px; top:-" + (r_value*2*0.6) + "px;font-family:Courier;font-size:" + (r_value*4) + "px;text-align: center;font-weight:bold;color:#5eff00;'>O<div>");
		
		var Cover = jQuery('<div/>');
		Cover.css('position','absolute');
		Cover.css('left','-12%');
		Cover.css('top','-12%');
		Cover.css('zIndex',(zIndex_value+2));
		Cover.css('height','124%');
		Cover.css('width','124%');
		Cover.css('cursor','pointer');
		
		Marker.append(Cover);
	}
	
	//Set Functions
	if(r_value == 0)
	{
		if(projectType == 8 || projectType == 9 || projectType == 10)
		{
			Marker.mouseover(function(){$(this).css('background','blue');});
		}
		else
		{
			Marker.mouseover(function(){$(this).css('background','red');});
		}
		Marker.mouseout(function(){$(this).css('background',color_value);});
	}
	
	if(r_value > 0)
	{
		Marker.mouseover(function(){$(this).css('background-image','url(bigcircle1.gif)');$(this).css('color','red');});
		Marker.mouseout(function(){$(this).css('background-image','url(bigcircle.gif)');$(this).css('color','#5eff00');});
	}
	
	Marker.click(function(event)
	{
		if(projectType == 8 || projectType == 9 || projectType == 10)
		{
			if(color_value == "#00FF00") //green
			{
				posCount--;
				RvRCounts[0]--;
				acneCounts[0]--;
			}
			else if(color_value == "#FF0000") //red
			{
				negCount--;
				RvRCounts[1]--;
				acneCounts[1]--;
			}
			else if(color_value == "#0000FF") //blue
			{
				RvRCounts[2]--;
				acneCounts[2]--;
			}
			else if(color_value == "#000000") //black
			{
				RvRCounts[3]--;
				acneCounts[3]--;
			}
			else if(color_value == "#FF99FF") //pink
			{
				RvRCounts[4]--;
				acneCounts[4]--;
			}
			else if(color_value == "#CC00FF") //purple
			{
				RvRCounts[5]--;
				acneCounts[5]--;
			}
		}
		
		removeMarker(event,$(this));
	});
	
	//Set Properties
	Marker.prop('x_point',x_point_value);
	Marker.prop('y_point',y_point_value);
	Marker.prop('r',r_value);
	
	return Marker;
}

function updateCBCount()
{
	if(markerArray[userid] == null)
	{
		markerArray[userid] = new Array();
	}
	document.getElementById('centroblasts').innerHTML = getCBCount();
}

function printMarkerPoints()
{
	var tempStr = '';
	var pointArray = markerArray[userid];
	for (var x=0;x<pointArray.length;x++)
	{
		tempStr = tempStr + "X: " + pointArray[x][0] + " Y: " + pointArray[x][1] + "<br>";
	}
	document.getElementById('PrintArea').innerHTML = tempStr;
}

function dump(arr,level,useBR) 
{
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				if(useBR == true)
				{
					dumped_text += level_padding + "'" + item + "' ...<br>";
				}
				else
				{
					dumped_text += level_padding + "'" + item + "' ...\n";
				}
				dumped_text += dump(value,level+1,useBR);
			} else {
				if(useBR == true)
				{
					dumped_text += level_padding + "'" + item + "' => '" + value + "'<br>";
				}
				else
				{
					dumped_text += level_padding + "'" + item + "' => '" + value + "'\n";
				}
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

function Filter()
{
	if($("#Filter").length > 0)
	{
		$("#Filter").remove();
	}
	else
	{
		var Filter = jQuery('<div/>');
		
		Filter.attr('id','Filter');
		//Set CSS
		Filter.css('background','black');
		Filter.css('fontSize','0px');
		Filter.css('height','100%');
		Filter.css('width','100%');
		Filter.css('display','block');
		Filter.css('zIndex','0');
		Filter.css('opacity',.3);

		$("#pointer_div").append(Filter);
	}
	
	/*
	var infoBox2 = jQuery('<div/>');
	infoBox2.attr('id','test2');
	infoBox2.css('width','400px');
	infoBox2.css('height','200px');
	infoBox2.css('position','relative');
	infoBox2.css('backgroundColor','red');
	infoBox2.css('marginLeft','auto');
	infoBox2.css('marginRight','auto');
	infoBox2.css('zIndex','40003');
	infoBox2.css('fontSize','12px');
	infoBox2.css('textAlign','center');
	infoBox2.css('color','black');
	infoBox2.append("TEST");
	$('#infobar-inner').append(infoBox2);
	
	alet('test');
	*/
}
