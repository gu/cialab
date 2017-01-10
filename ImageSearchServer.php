<?php include("SecurePage.php"); ?>
<?php include("GlobalVariables.php"); ?>
<?php include("GlobalFunctions.php"); ?>
<?php
Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_cialab);

if(isset($_GET["array"]) == true && $_GET['array'] == 'optomize')
{
	set_time_limit(0);
	Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_imgfeatures);

	echo "
		<style type='text/css'>
		#datatable table
		{
			border:1px solid black;
			border-collapse:collapse;
		}
		#datatable td
		{
			border:1px solid black;
			border-collapse:collapse;
			padding-right: 10px;
		}
		#datatable th
		{
			border:1px solid black;
			border-collapse:collapse;
			padding-right: 10px;
			font-weight: bold;
			text-align: center;
		}
		</style>
		";
	echo "<div id='datatable'>";

	$FeatureArray = array('mHue','mSaturation','mValue','stdHue','stdSaturation','stdValue','SPL_Mean','SPL_Variance','SPL_Skewness','SPL_Kurtosis','SPL_Maximum','SPL_Minimum','SPL_Energy','SPL_Entropy','Texture_Contrast','Texture_Correlation','Texture_Deviation','Texture_Energy','Texture_Entropy','Texture_Homogeneity','Texture_Mean','Texture_Entropy_Diff','Texture_Homogeneity_Diff','Texture_Mean_Text_Diff','Shape_Number_Of_Objects','Shape_IM1','Shape_IM2','Shape_IM3','Shape_IM4','Shape_IM5','Shape_IM6','Shape_IM7','Rows','Columns','SPL_rgb_mRed','SPL_rgb_mGreen','SPL_rgb_mBlue','SPL_rgb_stdRed','SPL_rgb_stdGreen','SPL_rgb_stdBlue','Texture_Contrast_pp','Texture_Correlation_pp','Texture_Deviation_pp','Texture_Energy_pp','Texture_Entropy_pp','Texture_Homogeneity_pp','Texture_Mean_pp','Texture_Entropy_Diff_pp','Texture_Homogeneity_Diff_pp','Texture_Mean_Text_Diff_pp','Texture_Contrast_R','Texture_Correlation_R','Texture_Deviation_R','Texture_Energy_R','Texture_Entropy_R','Texture_Homogeneity_R','Texture_Mean_R','Texture_Entropy_Diff_R','Texture_Homogeneity_Diff_R','Texture_Mean_Text_Diff_R','Texture_Contrast_B','Texture_Correlation_B','Texture_Deviation_B','Texture_Energy_B','Texture_Entropy_B','Texture_Homogeneity_B','Texture_Mean_B','Texture_Entropy_Diff_B','Texture_Homogeneity_Diff_B','Texture_Mean_Text_Diff_B','Texture_Contrast_G','Texture_Correlation_G','Texture_Deviation_G','Texture_Energy_G','Texture_Entropy_G','Texture_Homogeneity_G','Texture_Mean_G','Texture_Entropy_Diff_G','Texture_Homogeneity_Diff_G','Texture_Mean_Text_Diff_G');

	echo "<br>";
	echo "<TABLE>";
	echo "<tr><th>Feature</th><th>Average #Results</th><th>Average #Correct</th><th>Average %Correct</th><th>Select Count Percent</th><th>Total Count Percent</th><th>Select Rank Percent</th><th>Total Rank Percent</th><th>Consensus Percent</th><th>Select Average percent</th><th>Total Average percent</th></tr>";
	
	
	//$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean_R","Texture_Entropy_R","Texture_Mean","SPL_Mean","Texture_Mean_B","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G","SPL_rgb_mBlue");//,$FeatureArrayValue);
	
	

	//*******************Neuroblastoma
	
	/*
	$OtherVariables = array("NBType"=>"DOUBLE");
	//$ColorFeatureArray = array("Texture_Mean_R","Texture_Entropy_R","Texture_Entropy_G","Texture_Entropy_B","Texture_Mean","SPL_Mean","Texture_Mean_B","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G");
	//$ColorFeatureArray = array("Texture_Entropy_R","Texture_Homogeneity_Diff_R","Texture_Homogeneity_G","stdSaturation","Texture_Entropy_G","Texture_Homogeneity_R","SPL_Mean","Texture_Correlation_pp");
	//$ColorFeatureArray = array("Texture_Mean_R","Texture_Entropy_R","Texture_Mean","Texture_Deviation_R","Texture_Mean_B","Texture_Correlation_pp","Texture_Energy_R","SPL_Skewness","Texture_Deviation_pp","Texture_Entropy_Diff_pp");
	
	$ColorFeatureArray = array("Texture_Mean_R","Texture_Entropy_R","Texture_Mean","Texture_Deviation_R","Texture_Mean_B","Texture_Correlation_pp","Texture_Energy_R","Texture_Contrast_pp","Texture_Deviation_pp","Texture_Entropy_Diff_pp");
	
	
	//$ColorFeatureArray = array("Texture_Mean_R","Texture_Entropy_R","SPL_Mean","Texture_Mean","Texture_Deviation_R","Texture_Mean_B","Texture_Mean_G","Texture_Correlation_pp","Texture_Energy_R","mHue");

	
	//OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"NBType","neuroblastoma",$OtherVariables,true,50);
	
	for($x=0;$x<1;$x++)
	{
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"NBType","neuroblastoma",$OtherVariables,false,10,1);
	}
	*/
	
	/*
	foreach($FeatureArray as $FeatureArrayValue)
	{
		$ColorFeatureArray = array($FeatureArrayValue);
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"NBType","neuroblastoma",$OtherVariables,true,50);
	}
	*/
	
	//*******************Follicular Lymphoma
	
	//$ColorFeatureArray = array("Texture_Entropy_R","Texture_Deviation_R","Texture_Homogeneity_Diff_R","SPL_rgb_mRed","Texture_Mean_R","Texture_Mean_Text_Diff_R","Texture_Entropy_Diff_R","Texture_Contrast_R","Texture_Homogeneity_R");
	//$ColorFeatureArray = array("Texture_Entropy_R","Texture_Deviation_R","Texture_Homogeneity_Diff_R","SPL_rgb_mRed","Texture_Mean_R","Texture_Mean_Text_Diff_R","Texture_Entropy_Diff_R","Texture_Contrast_R","Texture_Homogeneity_R","SPL_Mean","Texture_Mean","SPL_rgb_mBlue","Shape_Number_Of_Objects","stdSaturation");
	
	
	$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean_R","Texture_Entropy_R","Texture_Entropy_G","Texture_Entropy_B","Texture_Mean","SPL_Mean","Texture_Mean_B","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G","SPL_rgb_mBlue");//,$FeatureArrayValue);
	$ColorFeatureArray = array("Texture_Entropy_R","Texture_Homogeneity_Diff_R","Texture_Homogeneity_G","stdSaturation","Texture_Entropy_G","Texture_Homogeneity_R","SPL_Mean","Texture_Mean_Text_Diff_R","Texture_Mean_R","Texture_Homogeneity");
	
	$OtherVariables = array("FLGrade" => "DOUBLE","average_cb" => "DOUBLE","olcay_grading" => "DOUBLE");
	
	for($x=0;$x<1;$x++)
	{
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"olcay_grading","follicular_lymphoma",$OtherVariables,true,50);
	}
	
	
	
	/*
	foreach($FeatureArray as $FeatureArrayValue)
	{
		$ColorFeatureArray = array($FeatureArrayValue);
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"olcay_grading","follicular_lymphoma",$OtherVariables,false,50);
	}
	*/
	
	//*******************Compare NB to FL
	
	
	//$OtherVariables = array("disease_type"=>"DOUBLE");
	//$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean_R","Texture_Entropy_R","Texture_Entropy_G","Texture_Entropy_B","Texture_Mean","SPL_Mean","Texture_Mean_B","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G","SPL_rgb_mBlue");
	//$ColorFeatureArray = array("Texture_Contrast_G","Texture_Entropy_Diff_G","Texture_Homogeneity_G","Texture_Homogeneity_Diff_G","Texture_Mean_Text_Diff_G","Texture_Entropy_G","Texture_Contrast");

	//echo "test";
	/*
	for($x=0;$x<1;$x++)
	{
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"disease_type","imagefeatures",$OtherVariables,true,1);
	}
	*/
	
	/*
	foreach($FeatureArray as $FeatureArrayValue)
	{
		$ColorFeatureArray = array($FeatureArrayValue);
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"disease_type","imagefeatures",$OtherVariables,false,50);
	}
	*/
	
	
	//*******************Search
	
	//OptimizeFeatures($ColorFeatureArray, $FeatureArray, true,"NBType","neuroblastoma",$OtherVariables,false,250);
	//$ColorFeatureArray, $FeatureArray, $TrimValues,$OptomizedFeature,$Table,$OtherVariables,$RemoveSample,$SampleSize
	
	/*
	foreach($FeatureArray as $FeatureArrayValue)
	{
		$ColorFeatureArray = array($FeatureArrayValue);
		
		OptimizeFeatures($ColorFeatureArray, $FeatureArray, false, 500, true,"olcay_grading","follicular_lymphoma",$OtherVariables);
	}
	*/
		
	echo "</TABLE>";
	echo "<div>";
}

function OptimizeFeatures($ColorFeatureArray, $FeatureArray, $TrimValues,$OptomizedFeature,$Table,$OtherVariables,$RunAll,$SampleSize,$ResultNumber)
{
	$TableNameCopy = $Table;

	//Remove Previous TemporaryViewSample (Holds the random sample)
	$sql = "DROP VIEW `TempViewSample`;";
	$result = mysql_query($sql);// or die(mysql_error());
	
	if($RunAll == false)
	{
		$IdArray = array();
		$sql = "SELECT * FROM `" . $Table . "` ORDER BY RAND() LIMIT " . $SampleSize . ";";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			array_push($IdArray,$row['id']);
		}
		
		//$IdArray = array();
		//array_push($IdArray,'96');
		
		//Create a New TempViewSample
		$sql = "CREATE VIEW `TempViewSample` AS SELECT * FROM `" . $Table . "` WHERE `id` IN ('".join("','",$IdArray)."');";
		$result = mysql_query($sql) or die(mysql_error());
	}
	else
	{
		$sql = "CREATE VIEW `TempViewSample` AS SELECT * FROM `" . $Table . "`";
		$result = mysql_query($sql) or die(mysql_error());
	}
	
	//Load up the table results
	$sql = "SELECT * FROM `TempViewSample`;";
	$OptomizedTableResults = mysql_query($sql) or die(mysql_error());

	
	//$sql = $sql . "SELECT * FROM `neuroblastoma_sample`;";
	//$OptomizedTableResults = mysql_query($sql);
	
	
	$num_counter = array();
	$num_correct = 0;
	$num_rows = 0;
	
	$correct_select_count_decisions = array();//0; 
	//This counts the number of times the features searched for were able to
	//correctly catorgorize the image being processed based on the count of the
	//selected values returned after the search.
	
	$correct_select_rank_decisions = array();//0;
	//This counts the number of times the features searched for were able to
	//correctly catorgorize the image being processed based on the sum of the
	//rank of the selected values returned after the search.
	
	$correct_total_count_decisions = array();//0;
	//This counts the number of times the features searched for were able to
	//correctly catorgorize the image being processed based on the count of all
	//of the images matching the the same NeuroblastomaType while the search is being
	//processed. This is NOT based on the final search output.
	
	$correct_total_rank_decisions = array();//0;
	//This counts the number of times the features searched for were able to
	//correctly catorgorize the image being processed based on the sum of the rank of all
	//of the images matching the the same NeuroblastomaType while the search is being
	//processed. This is NOT based on the final search output.
	
	$correct_select_average_decisions = array();//0;
	//This is the average of the variables. a correct decision is determined by rounding
	//this number to the nearest number.
	
	$correct_total_average_decisions = array();//0;
	//This is the average of the variables. a correct decision is determined by rounding
	//this number to the nearest number.
	
	$consensus_decisions = array();//0;
	//This is the average of the 5 variables above
	
	
	
	while($OptomizedRow = mysql_fetch_array($OptomizedTableResults, MYSQL_ASSOC))
	{
	

		$Table = $TableNameCopy;
		//Remove Previous TemporaryView (Holds the table minus the random sample)

		$sql = "DROP VIEW `TempView`;";
		$result = mysql_query($sql);// or die(mysql_error());

		//Create a New TempView
		$sql = "CREATE VIEW `TempView` AS SELECT `".$Table."`.* FROM `".$Table."` WHERE `id` != '".$OptomizedRow['id']."'";
		$result = mysql_query($sql) or die(mysql_error());
		
		//echo $OptomizedRow['id'];
		
		//Change Table Name to Temp View
		$Table = "TempView";

		
		$DataArray = $OptomizedRow;
		$ResultsLimit = 2000;
		$StandardDeviationsAway = 10;
		$NumResultsArray = array();
		
		$ResultsArray = SearchDatabaseOptomized(array_unique($ColorFeatureArray),$DataArray,$Table,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptomizedFeature,$ResultNumber,$ResultNumber);
		
		//$ColorFeatureArray,$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptimizedFeature
		
		
		$result = $ResultsArray['result2']; //results is the array with the tiffname, rank, id, pngimage, NeuroblastomaType variables that matched all the variables serached by within one std
		$NumResultsArray = $ResultsArray['NumResultsArray']; //NumResultsArray is an array that contains a key for each feature searched for and the total number of results returned for that feature.
		$NumResultsCorrectArray = $ResultsArray['NumResultsCorrectArray']; 
		//NumResultsCorrectArray is an array that contains a key for each feature
		//and value of an array that hold the types of neuroblastoma 
		//returned the count of each type of the Neuroblastoma.
		
		$NeuroblastomaTotalTypeRankSum = $ResultsArray['TypeRankSum'];
		
		//print_r($NeuroblastomaTotalTypeRankSum);
		
		$NeuroblastomaTypeCount = array();
		$NeuroblastomaTypeRankSum = array();
	
		$num_rows = $num_rows + mysql_num_rows($result);
	
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			@$NeuroblastomaTypeCount[$row[$OptomizedFeature]] += 1;
			//print_r($row);
			@$NeuroblastomaTypeRankSum[$row[$OptomizedFeature]] += (array_sum($NumResultsArray) - $row['rank']); 
			// This was originally an error I made, because the rank goes from 1 being closest to around 200 being farthest
			// away it is not the largest total rank that determines if an item is correct or not. It would rather depend on 
			// the numer of items returned for that type and the ranking of each. Ie 2 objs of same type with ranks 5 and 6 would be
			// less than one object with a rank of 20. This throws off results. Therefore I need to subtract each rank from the total
			// then add them up to get an accurate meassure. 
			
			if($row[$OptomizedFeature]==$OptomizedRow[$OptomizedFeature])
			{
				$num_correct = $num_correct + 1;
			}
		}
		@$num_counter[$OptomizedRow[$OptomizedFeature]] += 1;
		$consensus = array();
		if ($OptomizedRow[$OptomizedFeature] == $row[$OptomizedFeature])
		{
			$num_correct += 1;
		}
		if(count($NeuroblastomaTypeCount) > 0)
		{
			$MaxNeuroblastomaType = array_search(max($NeuroblastomaTypeCount),$NeuroblastomaTypeCount);
			@$consensus[$MaxNeuroblastomaType] += 1;
			if ($OptomizedRow[$OptomizedFeature] == $MaxNeuroblastomaType)
			{
				@$correct_select_count_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
			}
		}
		if(count($NeuroblastomaTypeRankSum) > 0)
		{
			$MaxNeuroblastomaType = array_search(max($NeuroblastomaTypeRankSum),$NeuroblastomaTypeRankSum);
			@$consensus[$MaxNeuroblastomaType] += 1;
			if ($OptomizedRow[$OptomizedFeature] == $MaxNeuroblastomaType)
			{
				@$correct_select_rank_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
			}
			else
			{
				//echo "<BR>|".$OptomizedRow["TiffFileName"]."|<BR>";
			}
		}
		$CombinedResultsArray = array();
		foreach (array_keys($NumResultsCorrectArray) as $ObjValue)
		{
			foreach (array_keys($NumResultsCorrectArray[$ObjValue]) as $NeuroblastomaName)
			{
				@$CombinedResultsArray[$NeuroblastomaName] += $NumResultsCorrectArray[$ObjValue][$NeuroblastomaName];
			}
		}
		@$MaxNeuroblastomaType = array_search(max($CombinedResultsArray),$CombinedResultsArray);
		@$consensus[$MaxNeuroblastomaType] += 1;
		if ($OptomizedRow[$OptomizedFeature] == $MaxNeuroblastomaType)
		{
			@$correct_total_count_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
		}
		
		$CombinedResultsArray = array();
		foreach (array_keys($NeuroblastomaTotalTypeRankSum) as $ObjValue)
		{
			foreach (array_keys($NeuroblastomaTotalTypeRankSum[$ObjValue]) as $NeuroblastomaName)
			{
				@$CombinedResultsArray[$NeuroblastomaName] += $NeuroblastomaTotalTypeRankSum[$ObjValue][$NeuroblastomaName];
			}
		}
		//print_r($CombinedResultsArray);
		//echo "<br>".$OptomizedRow[$OptomizedFeature]."<br>";
		@$MaxNeuroblastomaType = array_search(max($CombinedResultsArray),$CombinedResultsArray);
		@$consensus[$MaxNeuroblastomaType] += 1;
		if ($OptomizedRow[$OptomizedFeature] == $MaxNeuroblastomaType)
		{
			//echo $OptomizedRow['NeuroblastomaType'] . " :<br>";
			//print_r($CombinedResultsArray);
			//echo "<br><br>";
			@$correct_total_rank_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
		}
		if(count($NeuroblastomaTypeCount) > 0)
		{
			$TotalCountOfObjects=0;
			$TotalSumOfObjects=0;
			foreach (array_keys($NeuroblastomaTypeCount) as $ObjValue)
			{
				@$TotalCountOfObjects += $NeuroblastomaTypeCount[$ObjValue];
				@$TotalSumOfObjects += ($ObjValue * $NeuroblastomaTypeCount[$ObjValue]);
			}
			
			@$average_result_tested = round($TotalSumOfObjects/$TotalCountOfObjects);
			@$consensus[$average_result_tested] += 1;

			if($OptomizedRow[$OptomizedFeature] == $average_result_tested)
			{
				@$correct_select_average_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
			}
		}
		if(count($CombinedResultsArray) > 0)
		{
			$TotalCountOfObjects=0;
			$TotalSumOfObjects=0;
			foreach (array_keys($CombinedResultsArray) as $ObjValue)
			{
				@$TotalCountOfObjects += $CombinedResultsArray[$ObjValue];
				@$TotalSumOfObjects += ($ObjValue * $CombinedResultsArray[$ObjValue]);
			}
			
			@$average_result_tested = round($TotalSumOfObjects/$TotalCountOfObjects);
			@$consensus[$average_result_tested] += 1;

			if($OptomizedRow[$OptomizedFeature] == $average_result_tested)
			{
				@$correct_total_average_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
			}
		}
		if ($OptomizedRow[$OptomizedFeature] == array_search(max($consensus),$consensus))
		{
			@$consensus_decisions[$OptomizedRow[$OptomizedFeature]] += 1;
		}
	}
	
	//This makes sure that the sub values are sorted in the same order eg (0,1,2)
	ksort($num_counter);
	
	$AverageNumResults = ($num_rows/array_sum($num_counter));
	$AverageNumCorrect = ($num_correct/array_sum($num_counter));
	@$AveragePerCorrect = (($num_correct/$num_rows) * 100);
	
	echo "<tr><td>".join(" , ",array_unique($ColorFeatureArray))."</td><td>".TrimFloat($AverageNumResults)."</td><td>".TrimFloat($AverageNumCorrect) ."</td><td>".TrimFloat($AveragePerCorrect)."</td>";
	PrintArrayResults($correct_select_count_decisions,$num_counter);
	PrintArrayResults($correct_total_count_decisions,$num_counter);
	PrintArrayResults($correct_select_rank_decisions,$num_counter);
	PrintArrayResults($correct_total_rank_decisions,$num_counter);
	PrintArrayResults($consensus_decisions,$num_counter);
	PrintArrayResults($correct_select_average_decisions,$num_counter);
	PrintArrayResults($correct_total_average_decisions,$num_counter);
	echo "</tr>";
}

function OptomizedResultsProcessor($OptimizedDatabaseResult,$OptomizedFeature)
{
	$result = $OptimizedDatabaseResult['result2'];
	$NumResultsCorrectArray = $OptimizedDatabaseResult['NumResultsCorrectArray']; 
	$TotalTypeRankSum = $OptimizedDatabaseResult['TypeRankSum'];
	//print_r($OptimizedDatabaseResult['TypeRankSum']);
	$SelectCount = array();
	$SelectRank = array();
	
	$resultArray = array();
	
	
	while($row = mysql_fetch_array($OptimizedDatabaseResult['result3'], MYSQL_ASSOC))
	{
		$resultArray[] = $row;
		@$SelectCountArray[$row[$OptomizedFeature]] += 1;
	}
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		@$SelectCount[$row[$OptomizedFeature]] += 1;
		@$SelectRank[$row[$OptomizedFeature]] += (array_sum($NumResultsArray) - $row['rank']); 
	}
	if(count($SelectCount) > 0)
	{
		$Decision["SelectRank"] = array_search(max($SelectRank),$SelectRank);
		$Decision["SelectCount"] = array_search(max($SelectCount),$SelectCount);
	}

	$CombinedResultsArray = array();
	foreach (array_keys($NumResultsCorrectArray) as $ObjValue)
	{
		foreach (array_keys($NumResultsCorrectArray[$ObjValue]) as $Value)
		{
			@$CombinedResultsArray[$Value] += $NumResultsCorrectArray[$ObjValue][$Value];
		}
	}
	@$Decision["TotalCount"] = array_search(max($CombinedResultsArray),$CombinedResultsArray);

	
	$CombinedResultsArray = array();
	foreach (array_keys($TotalTypeRankSum) as $ObjValue)
	{
		foreach (array_keys($TotalTypeRankSum[$ObjValue]) as $Value)
		{
			@$CombinedResultsArray[$Value] += $TotalTypeRankSum[$ObjValue][$Value];
		}
	}

	@$Decision["TotalRank"] = array_search(max($CombinedResultsArray),$CombinedResultsArray);

	if(count($SelectCount) > 0)
	{
		$TotalCountOfObjects=0;
		$TotalSumOfObjects=0;
		foreach (array_keys($SelectCount) as $ObjValue)
		{
			@$TotalCountOfObjects += $SelectCount[$ObjValue];
			@$TotalSumOfObjects += ($ObjValue * $SelectCount[$ObjValue]);
		}
		
		@$Decision["SelectAverage"] = round($TotalSumOfObjects/$TotalCountOfObjects);
	}
	
	if(count($CombinedResultsArray) > 0)
	{
		$TotalCountOfObjects=0;
		$TotalSumOfObjects=0;
		foreach (array_keys($CombinedResultsArray) as $ObjValue)
		{
			@$TotalCountOfObjects += $CombinedResultsArray[$ObjValue];
			@$TotalSumOfObjects += ($ObjValue * $CombinedResultsArray[$ObjValue]);
		}
		
		@$Decision["TotalAverage"] = round($TotalSumOfObjects/$TotalCountOfObjects);
	}
	
	$Decision["Consensus"] = round(array_sum($Decision)/count($Decision));
	//$Decision["NumberOfRows"] = $Number_Of_Rows;
	$Decision["SelectCountArray"] = $SelectCountArray;
	$Decision["ResultArray"] = $resultArray;
	
	return $Decision;
}


function PrintArrayResults($ResultsArray,$TotalCounter)
{
	echo "<td>";
	echo "<table><tr>";
	foreach (array_keys($TotalCounter) as $value)
	{
		echo "<td>type ".$value.": ";
		if (isset($ResultsArray[$value]) == true)
		{
			echo TrimFloat(($ResultsArray[$value]/ $TotalCounter[$value]*100));
		}
		else
		{
			echo "0.00";
		}
		echo "</td>";
	}
	echo "</tr><tr><td>";
	if(count($ResultsArray) > 0)
	{
	echo TrimFloat(array_sum($ResultsArray) / array_sum($TotalCounter) * 100);
	}
	echo "</td></tr></table>";
	echo "</td>";
}

if(isset($_FILES["SearchFile"]) == true || isset($_POST["FileName"]) == true)
{
	
	$ResultNumber=20;
	$DecisionNumber=3;
	
	if(isset($_POST["result_number"]) && is_numeric ($_POST["result_number"]) && $_POST["result_number"] < 100)
	{
		$ResultNumber = mysql_prep($_POST["result_number"]);
	}
	if(isset($_POST["decision_number"]) && is_numeric ($_POST["decision_number"]) && $_POST["decision_number"] < 100)
	{
		$DecisionNumber = mysql_prep($_POST["decision_number"]);
	}
	
	$TimeToUploadFiles = 0.0;
	$TimeToExecuteMATLAB = 0.0;
	$TimeToProcessImage = 0.0;
	$TimeToConvertImage = 0.0;
	$TimeToSearchDatabase = 0.0;
	$TotalTime = 0.0;
	
	$target_path = "./uploads/";
	$RemoteUploadPath = "./uploads/";
	
	$out = array();
	exec('cd', $out);
	$wwwPath = $out[0];
	$uniserverPath = rtrim($wwwPath,"\\www");
	
	$DatabaseApp = $uniserverPath . "\\database_app\\DatabaseAppMatlabR2009b\\DatabaseApp2.exe";
	
	if(isset($_POST["matlab_version"]) && $_POST["matlab_version"] == "matlabr2009b")
	{
		$DatabaseApp = $uniserverPath . "\\database_app\\DatabaseAppMatlabR2009b\\DatabaseApp2.exe";
	}
	else if(isset($_POST["matlab_version"]) && $_POST["matlab_version"] == "matlabr2010a")
	{
		$DatabaseApp = $uniserverPath . "\\database_app\\DatabaseAppMatlabR2010a\\DatabaseApp2.exe";
	}
	
	
	$LocalUploadPath = $uniserverPath . "\\www\\uploads\\";
	
	if($_POST["FileName"] == "")
	{
		$time_start = microtime(true);
		//$RemoteUploadPath = $RemoteUploadPath . basename( $_FILES['SearchFile']['name']); 
		$FileName = explode(".",$_FILES["SearchFile"]["name"]);
		$UniqueFileId = md5($_FILES["SearchFile"]["name"]);
		$FileExtension = $FileName[count($FileName)-1];
		//echo $UniqueFileId;
		$RemoteUploadPath = $RemoteUploadPath . $UniqueFileId . "." . $FileExtension; 

		if(move_uploaded_file($_FILES['SearchFile']['tmp_name'], $RemoteUploadPath)) 
		{
			//echo "The file ".  basename( $_FILES['SearchFile']['name']). " has been uploaded";
		} 
		else
		{
			//echo "There was an error uploading the file, please try again!";
		}
		$TimeToUploadFiles = (microtime(true) - $time_start);
	}
	else
	{
		$FileName = explode(".",$_POST["FileName"]);
		$UniqueFileId  = $FileName[0];
		$FileExtension = $FileName[1];
	}
	
	
	$time_start = microtime(true);
	
	exec("\"" . $DatabaseApp . "\" " . "\"" . $LocalUploadPath . $UniqueFileId . "." . $FileExtension . "\"", $result);
	
	//echo "\"" . $DatabaseApp . "\" " . "\"" . $LocalUploadPath . $UniqueFileId . "." . $FileExtension . "\"";
	
	//print_r($result);
	$TimeToExecuteMATLAB = (microtime(true) - $time_start);
	
	if (count($result) < 2)
	{
		echo "ERROR";
		unlink($RemoteUploadPath);
	}
	else
	{
		$DataArray = array();

		for($x=0;$x<((count($result)/2));$x++)
		{
			$DataArray[$result[$x*2] ] = $result[$x*2+1];
		}
		
		$TimeToProcessImage = ($DataArray['ImageProccessTime'] / 1000);
		$TimeToConvertImage = ($DataArray['ImageConversionTime'] / 1000);
		
		Connect_To_DB($db_server_official, $db_user_official, $db_pwd_official, $db_imgfeatures);
		//Connect_To_DB("127.0.0.1", "root", "root", "test");

		$NameArray = array_keys($DataArray);

		$sql = "";

		////////////////// SELECT FEATURES TO SEARCH BY
		
		//$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean_R","Texture_Entropy_R","Texture_Mean","SPL_Mean","Texture_Mean_B","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G");
		//$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean_R","Texture_Entropy_R","Texture_Entropy_G","Texture_Entropy_B","Texture_Mean","SPL_Mean","Texture_Mean_B","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy_R","mHue","Texture_Mean_G","SPL_rgb_mBlue");
		
		$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean","Texture_Entropy","SPL_Mean","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy","mHue","SPL_rgb_mBlue");
		
		$ResultsLimit = 200;
		$StandardDeviationsAway = 1;
		$NumResultsArray = array();
		
		///////////////// Determine Disease
		if(isset($_POST["disease_selection"]) && is_numeric ($_POST["disease_selection"]) && $_POST["disease_selection"] != -1)
		{
			$sql = "SELECT * FROM `disease` WHERE `id` = '".filter_var($_POST["disease_selection"],FILTER_SANITIZE_NUMBER_INT)."';";
		}
		else
		{
			$ColorFeatureArray = array("Texture_Contrast_G","Texture_Entropy_Diff_G","Texture_Homogeneity_G","Texture_Homogeneity_Diff_G","Texture_Mean_Text_Diff_G","Texture_Entropy_G","Texture_Contrast");
			$OtherVariables = array("disease_type"=>"DOUBLE");
			$OptomizedFeature = "disease_type";
			$time_start = microtime(true);
			$ResultsArray = SearchDatabaseOptomized(array_unique($ColorFeatureArray),$DataArray,"imagefeatures",$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptomizedFeature,20,3);
			$Decisions = OptomizedResultsProcessor($ResultsArray,"disease_type");
			
			$sql = "SELECT * FROM `disease` WHERE `id` = '".$Decisions['SelectCount']."';";
		}
		$tempArray = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		$DiseaseName = $tempArray["disease"];
		
		
		$OtherVariables = array($tempArray["optomized_feature"] => $tempArray["optomized_feature_type"]);
		$ColorFeatureArray = explode(",", $tempArray["feature_array"]);
		$TableName = $tempArray["table_name"];
		$OptomizedFeature = $tempArray["optomized_feature"];
		
		$ResultsArray = array();
		$ResultsArray = SearchDatabaseOptomized(array_unique($ColorFeatureArray),$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptomizedFeature,$ResultNumber,$DecisionNumber);
		
		$Decisions = OptomizedResultsProcessor($ResultsArray,$OptomizedFeature);
		$tempDiseaseType = $tempArray["disease_type"];
		$sql = "SELECT * FROM `disease_type_".$tempDiseaseType."` WHERE `id` = '".$Decisions['SelectCount']."';";
		//echo $sql;
		$tempArray = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC);
		$DiseaseType = $tempArray["type"];
		
		$sql = "SELECT * FROM `disease_type_".$tempDiseaseType."`;";
		$tempResult = mysql_query($sql);
		$diseaseTypeArray = array();
		$diseaseArrayResults = array();
		
		while($row = mysql_fetch_array($tempResult, MYSQL_ASSOC))
		{
			@$diseaseTypeArray[$row["id"]]= $row["type"];
			@$diseaseArrayResults[$row["type"]] = $Decisions["SelectCountArray"][$row["id"]];
		}
		//echo "<br>Type: ".$DiseaseType."<br>";
		
		//$ColorFeatureArray = array("SPL_rgb_mRed","Texture_Mean","Texture_Entropy","SPL_Mean","SPL_rgb_mGreen","Texture_Correlation_pp","Texture_Energy","mHue","SPL_rgb_mBlue");
		//$ResultsArray = SearchDatabase($ColorFeatureArray,$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables);
		//$result = $ResultsArray['result'];
		$ResultArray = $Decisions['ResultArray'];
		$NumResultsArray = $ResultsArray['NumResultsArray'];
		
		
		/*
		$ResultsArray = SearchDatabaseOptomized(array_unique($ColorFeatureArray),$DataArray,$Table,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptomizedFeature);
		
		//$ColorFeatureArray,$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptimizedFeature
		
		
		$result = $ResultsArray['result']; //results is the array with the tiffname, rank, id, pngimage, NeuroblastomaType variables that matched all the variables serached by within one std
		$NumResultsArray = $ResultsArray['NumResultsArray']; //NumResultsArray is an array that contains a key for each feature searched for and the total number of results returned for that feature.
		$NumResultsCorrectArray = $ResultsArray['NumResultsCorrectArray']; 
		*/
		
		
		$TimeToSearchDatabase = (microtime(true) - $time_start);

		//echo "<div id='RightPanel' style='float:left;width:353px;'>";
		//echo print_r($NumResultsArray);
		
		$TotalTime = $TimeToSearchDatabase + $TimeToExecuteMATLAB + $TimeToUploadFiles;

		
		
		//Results Table
		//print_r();
		
		echo "<TABLE style='text-align:left;margin-top:10px;'>";
		echo "<tr><td style='width:250px;'><TABLE style='float: left;'>";
		echo "<tr><td>Search Image:</td></tr>";
		echo "<tr><td><a target='NEWPAGE' href='http://localhost/viewimg.php?filename=" . $UniqueFileId . ".png" . "'><img border='0px' src='http://localhost/viewimg.php?filename=" . $UniqueFileId . ".png" . "&width=200&height=200'></a></td>";	
		echo "</tr>";
		echo "<tr><td>";
		echo "<TABLE style='float:left;width:100%'>";
		echo "<tr><td style='text-align:right;margin-right:10px;'>Disease: </td><td><b>".$DiseaseName."</b></td></tr>";
		echo "<tr><td style='text-align:right;margin-right:10px;'>Type: </td><td><b>".$DiseaseType."</b></td></tr>";
		echo "</TABLE>";
		echo "</td></tr>";
		echo "</TABLE></td>";
		echo "<td style='width:500px;'>";
		$ColorNumberArray = PrintGraph($diseaseArrayResults,$diseaseTypeArray);
		echo "</td>";
		//echo "<td style='width:10px;'></td>";
		//echo "<td style='width:370px;'><div id='datatable' style='padding-right:10px;'>";
		/*
		//Time Table
		echo "<TABLE style='float:right;'>";
		echo "<tr><td>Upload Image Time: </td><td>".TrimFloat($TimeToUploadFiles)." Seconds</td></tr>";
		echo "<tr><td>Execute Matlab Time: </td><td>".TrimFloat($TimeToExecuteMATLAB)." Seconds</td></tr>";
		echo "<tr><td style='font-size:'60%'> - Convert Image Time: </td><td>".TrimFloat($TimeToConvertImage)." Seconds</td></tr>";
		echo "<tr><td style='font-size:'60%'> - Proccess Image Time: </td><td>".TrimFloat($TimeToProcessImage)." Seconds</td></tr>";
		echo "<tr><td>Database Search Time: </td><td>".TrimFloat($TimeToSearchDatabase)." Seconds</td></tr>";
		echo "<tr><td><b>Total Time: </b></td><td><b>".TrimFloat($TotalTime)." Seconds</b></td></tr>";
		echo "</TABLE>";
		*/
		
		//Disease Table
		/*echo "<TABLE style='float:right;'>";
		echo "<tr><td>Disease: </td><td>".$DiseaseName."</td></tr>";
		echo "<tr><td>Type: </td><td>".$DiseaseType."</td></tr>";
		echo "</TABLE>";
		
		echo "</div></td></tr>";*/
		echo "</TABLE>";
		
		
		
		echo "
			<style type='text/css'>
			#datatable table
			{
				border:1px solid black;
				border-collapse:collapse;
			}
			#datatable td
			{
				border:1px solid black;
				border-collapse:collapse;
				padding-right: 10px;
			}
			#ImagesContainer
			{
				width: 100%;
				margin-left: 5px;
				margin-right: 5px;
				float: left;
				margin-top: 10px;
			}
			#ImageContainer
			{
				float: left;
				padding: 2px;
				height: 112px;
				width: 180px;
				text-align: center;
				display: inline-block;
				padding-bottom: 60px;
			}
			</style>
			";
		
		echo "<div id='ImagesContainer'>";
		$BinDump = array();
		$RankDump = array();
		$counter = -1;
		
		//while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		foreach($ResultArray as $row)
		{
			if($counter != $row["counter"])
			{
				echo "<div id='split' style='clear:both;float:left;width:735px;height:20px;padding:4px;text-align:left;border-bottom:1px solid #D4D4D4;margin-bottom:10px;margin-top:10px;'>Match ".$row["counter"]." Features :</div>";
				$counter = $row["counter"];
			}
			//FLGrade average_cb olcay_grading
			//@$BinDump[$row['olcay_grading']] = $BinDump[$row['olcay_grading']] + 1;
			//@$RankDump[$row['olcay_grading']] = $RankDump[$row['olcay_grading']] + (array_sum($NumResultsArray) - $row['rank']);
			$percent_correct = TrimFloat(((((array_sum($NumResultsArray))-($row['rank']-$row["counter"]))/(array_sum($NumResultsArray)))*100));
			
			/*
			echo "<br>";
			echo "Num: " . array_sum($NumResultsArray);
			echo "Rank: " . $row['rank'];
			echo "Total: " . array_sum($NumResultsArray);
			echo "<BR>";
			*/
			
			//echo $row['TiffFileName'];
			//$array = explode("\"",$row['TiffFileName']);
			echo "<div id='ImageContainer'><div id='imgObj' style='display: table-cell; height: 112px; width: 180px; vertical-align: middle;'>";
			//echo "<td>" . $row['TiffFileName'] . "<br><br>Rank: ".$row['rank']."<br><br>Percent Match: " . TrimFloat(((((array_sum($NumResultsArray))-$row['rank'])/(array_sum($NumResultsArray)))*100)) . " %</td>";
			echo "<a target='NEWPAGE' href='http://localhost/viewimg.php?id=" . $row['id'] . "&db=".$TableName."'><img border='0px' src='http://localhost/viewimg.php?id=" . $row['id'] . "&width=180&height=112&db=".$TableName."'></a>";	
			echo "<div id='ColorBoxContainer' style='width:180px;float:left;text-align:center;position:block;'><div id='ColorBox' style='background-color:#".$ColorNumberArray[$row[$OptomizedFeature]].";width:18px; height:18px; float:left;'></div><b>".$diseaseTypeArray[$row[$OptomizedFeature]]."</b><br>" . $percent_correct . "%</div>";
			$red = 0;
			$green = 0;
			$blue = 6;
			if($percent_correct > 50.0)
			{
				$green = 255;
				$red = round(255*(abs(100-$percent_correct))/50);
			}
			if($percent_correct == 50.0)
			{
				$green = 255;
				$red = 255;
			}
			if($percent_correct < 50.0)
			{
				$green = round(255*(abs(100-(100-$percent_correct)))/50);
				$red = 255;
			}
			
			echo "<div id='pbarwrapper' style='width:168px;margin-left:5px;margin-right:5px;border: 1px solid black;float:left;'><div id='pbar' style='background-color:rgb(".$red.",".$green.",6);width:".$percent_correct."%;height:10px;'></div></div>";

			echo "</div></div>";
		}

		echo "</div>";
		
	}
}
function PrintGraph($ArrayData,$NumberArray)
{
	$NumberOfDivisions = 5;
	$GraphWidth = 380;
	$GraphHeight = 190;
	$ColorArray = Array("FF0000","FFFF00","00CC00","009999","0099FF","0000FF","9900CC","FF0099");
	$NumberElements = array_sum($ArrayData);
	$NumberObjects = count($ArrayData);
	$number_of_widgets = ($NumberObjects * 4) + ($NumberObjects + 1);
	$widget_percentage = TrimFloat((100 / $number_of_widgets));

	echo "<style type='text/css'>
		#yaxis
		{
			float:right;
			height:".($GraphHeight + 1)."px;
			text-align:right;
			margin-bottom: 20px;
		}
		#yaxisbox
		{
			float:right;
			clear:right;
			padding-right: 5px;
			height:".(TrimFloat($GraphHeight/$NumberOfDivisions)-1)."px;
			border-top:1px solid black;
			text-align:right;
		}
		#graphwrappertext
		{
			float: left;
			height:".($GraphHeight + 1)."px;
		}
		#graphwhole
		{
			float: right;
			margin-top: 10px;
			margin-bottom: 30px;
			margin-right: 20px;
		}
		#graphwrapper
		{
			float: right;
			width:".($GraphWidth + 1)."px;
			height:".($GraphHeight + 1)."px;
			border-left:1px solid black;
			border-bottom:1px solid black;
		}
		#graph
		{
			float: left;
			width:".($GraphWidth)."px;
			height:".($GraphHeight)."px;
		}
		#barwrapper
		{
			float:left;
			height: 100%;
			width: ".($widget_percentage*4)."%;
		}
		#barbottom
		{
			text-align:center;
			float: left;
			border:1px solid black;
			width: 100%;
		}
		#bartop
		{
			float: left;
			width: 100%;
		}
		#spacer
		{
			float: left;
			height: 100%;
			width: ".($widget_percentage)."%;
		}
		#test2
		{
			font-size: 12px;
			margin-top:".TrimFloat($GraphHeight/$NumberOfDivisions/2/2)."px;
			position:relative;
			text-align:right;
			top:-".(TrimFloat($GraphHeight/$NumberOfDivisions/2))."px;
			padding-right:4px;
			height:".(TrimFloat($GraphHeight/$NumberOfDivisions)-TrimFloat($GraphHeight/$NumberOfDivisions/2/2))."px;
		}
		#textbottomcontainer
		{
			float: right;
			width:".($GraphWidth)."px;
		}
		#textbottom
		{
			width: ".($widget_percentage*4)."%;
			float: left;
			text-align:centered;
		}
		#spacebottom
		{
			width: ".($widget_percentage)."%;
			float: left;
			height: 100%;
			display:block;
			text-align:centered;
		}
		#bartext
		{
			text-align:center;
			font-size: 12px;
		}
		</style>
		";
		//$ColorArray
		
	echo "<div id='graphwhole'>";
	echo "<div id='graphwrappertext'>";
	echo "<div id='graphwrapper'><div id='graph'>";
	$counter = 0;

	foreach(array_keys($NumberArray) as $value)
	{
		$NumberArray[$value] = $ColorArray[$counter];
		$counter = $counter + 1;
	}
	
	$counter = 0;
	foreach(array_keys($ArrayData) as $value)
	{
		echo "<div id='spacer'>&nbsp;</div>";
		echo "<div id='barwrapper'>";
		echo "<div id='bartop' style='height:".(100-floor($ArrayData[$value]/(max($ArrayData))*100))."%;'></div>";
		echo "<div id='barbottom' style='height:".(floor($ArrayData[$value]/(max($ArrayData))*100))."%;background-color:#".$ColorArray[$counter].";'>";
		echo $ArrayData[$value];
		echo "</div>";
		echo "<div id='bartext'>".$value."</div>";
		echo "</div>";
		$counter = $counter + 1;
	}
	echo "<div id='spacer'>&nbsp;</div>";

	echo "</div></div>";

	echo "<div id='yaxis'>";
	for($x=$NumberOfDivisions;$x>0;$x--)
	{
		echo "<div id='yaxisbox' style='width:4px;'></div>";
	}
	echo "</div>";

	echo "<div id='test' style='float:right;'>";
	for($x=$NumberOfDivisions;$x>0;$x--)
	{
		echo "<div id='test2'>".(TrimFloat(max($ArrayData)/$NumberOfDivisions)*$x)."</div>";
	}
	echo "</div>";
	echo "</div>";
	echo "</div>";
	return $NumberArray;
}

//imagefeatures
function SearchDatabaseOptomized($ColorFeatureArray,$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables,$OptimizedFeature,$ResultNumber,$DecisionNumber)
{
	$ResultNumber = mysql_prep($ResultNumber);
	$DecisionNumber = mysql_prep($DecisionNumber);
	//print_r($OtherVariables);
	$NumResultsArray = array();
	$sql =  "
	CREATE TEMPORARY TABLE `TempTable`
	(
	`id` INT NOT NULL,
	`rank` INT NOT NULL,
	`counter` INT NOT NULL,
	`TiffFileName` VARCHAR(200) NOT NULL";

	foreach ($ColorFeatureArray as $value)
	{
		$sql = $sql . ",`" . $value . "` DOUBLE";
	}
	
	foreach (array_keys($OtherVariables) as $value)
	{
		$sql = $sql . ",`" . $value . "` " . $OtherVariables[$value] . "";
	}

	$sql = $sql . ") ENGINE=MEMORY;";
	$result = mysql_query($sql) or die(mysql_error());

	//echo "<BR><BR>" . $sql . "<BR><BR>";

	///////////////// INSERT VALUES INTO TEMPORARY TABLE
	foreach ($ColorFeatureArray as $value)
	{
		///////////// GATHER ID's AND rank to determine if they need to be inserted or updated
		$idArray = array();
		$idCounterArray = array();
		$sql = "SELECT `id`,`rank`,`counter` FROM `TempTable`;";
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$idArray[$row['id']] = $row['rank'];
			$idCounterArray[$row['id']] = $row['counter'];
		}
		
		$StandardDeviation = 0;
		$sql = "SELECT STDDEV_POP(`".$value."`) FROM `" . $TableName . "`;";
		$result = mysql_query($sql) or die(mysql_error());
		$SD = mysql_fetch_array($result, MYSQL_ASSOC);
		$StandardDeviation = $SD["STDDEV_POP(`".$value."`)"];
		
		
		$sql =  "	
		(
			SELECT `id`, `TiffFileName` , `" . $value . "` , `" . implode("` , `",array_keys($OtherVariables)) . "`
			FROM `" . $TableName . "`
			WHERE `" . $value . "` > '" . $DataArray[$value] . "'
			AND `".$value."` < '".($DataArray[$value] + ($StandardDeviation * $StandardDeviationsAway)) . "' 
			ORDER BY `" . $value . "` ASC 
			LIMIT ".$ResultsLimit."
		)
		UNION 
		(
			SELECT `id`, `TiffFileName` , `" . $value . "` , `" . implode("` , `",array_keys($OtherVariables)) . "`
			FROM `" . $TableName . "`
			WHERE `" . $value . "` <= '" . $DataArray[$value] . "'
			AND `".$value."` > '".($DataArray[$value] - ($StandardDeviation * $StandardDeviationsAway)) . "' 
			ORDER BY `" . $value . "` DESC 
			LIMIT ".$ResultsLimit."
		)
		ORDER BY ABS( `" . $value . "` - '" . $DataArray[$value] . "' )
		LIMIT ".$ResultsLimit.";
		";
		//echo $sql;
		$rank = 1;
		$result = mysql_query($sql) or die(mysql_error());
		$NumResultsArray[$value] = mysql_num_rows($result);

		$TypeCount = array();
		$TypeRankSum = array();
		
		//echo "<BR><BR>" . $sql . "<BR><BR>";
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			@$TypeCount[$row[$OptimizedFeature]] += 1;
			@$TypeRankSum[$row[$OptimizedFeature]] = $TypeRankSum[$row[$OptimizedFeature]] + ($NumResultsArray[$value] - $rank);
			//echo "<br><br>".($NumResultsArray[$value] - $rank)."<br><br>";
			
			if(array_key_exists($row['id'],$idArray) == true)
			{
				$sql = "UPDATE `TempTable` SET `rank`='".($rank+$idArray[$row['id']])."', `" . $value . "`='".$row[$value]."', `counter` = '".(1+$idCounterArray[$row['id']])."' WHERE `id`='".$row['id']."';";
			}
			else
			{
				$sql = "INSERT INTO `TempTable` (`id`,`rank`,`counter`,`TiffFileName`,`" . $value . "`,`" . implode("`,`",array_keys($OtherVariables)) . "`) VALUES ('".$row['id']."','" . $rank . "','1','".$row['TiffFileName']."','".$row[$value]."'";
				foreach (array_keys($OtherVariables) as $variable)
				{
					$sql = $sql . ",'".$row[$variable]."'";
				}
				$sql = $sql . ")";
			}
			$result2 = mysql_query($sql) or die(mysql_error());
			$rank = $rank + 1;
		}
		
		$sql = "SELECT MAX(`" . $value . "`) FROM `TempTable`";
		$result3 = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result3, MYSQL_ASSOC);
		$MaxValue = $row["MAX(`" . $value . "`)"];
		
		$sql = "UPDATE `TempTable` SET `" . $value . "`='".$MaxValue."' WHERE `" . $value . "` IS NULL; ";
		$result3 = mysql_query($sql) or die(mysql_error());
			
		//print_r($TypeRankSum);
		$NumResultsCorrectArray[$value] = $TypeCount;
		$RankingsOfResultsArray[$value] = $TypeRankSum;
	}

	///////////////// SELECT VALUES FROM TABLE
	$sql = "SELECT * FROM `TempTable` WHERE `" . join("` IS NOT NULL AND `",$ColorFeatureArray) . "` IS NOT NULL ORDER BY `rank` ASC;";
	//$sql = "SELECT * FROM `TempTable` ORDER BY `rank` ASC;";
	//$sql = "SELECT * FROM `table_1087` ORDER BY `counter` DESC, (`rank` / `counter`) ASC;";
	$result = mysql_query($sql) or die(mysql_error());
	
	$sql = "SELECT * FROM `TempTable` ORDER BY `counter` DESC, (`rank` / `counter`) ASC LIMIT ".$DecisionNumber.";";
	$result2 = mysql_query($sql) or die(mysql_error());
	
	$sql = "SELECT * FROM `TempTable` ORDER BY `counter` DESC, (`rank` / `counter`) ASC LIMIT ".$ResultNumber.";";
	//$sql = "SELECT * FROM `TempTable` ORDER BY `counter` DESC, (`rank` / MAX(`counter`)) ASC LIMIT 12;";
	$result3 = mysql_query($sql) or die(mysql_error());
	
	$ReturnValues = array("result" => $result, "NumResultsArray" => $NumResultsArray, "NumResultsCorrectArray" => $NumResultsCorrectArray, "TypeRankSum" => $RankingsOfResultsArray,"result2"=>$result2,"result3"=>$result3);
	$sql = "DROP TABLE `TempTable`";
	$result = mysql_query($sql) or die(mysql_error());
	return $ReturnValues;
}

function SearchDatabase($ColorFeatureArray,$DataArray,$TableName,$ResultsLimit,$StandardDeviationsAway,$OtherVariables)
{
	$NumResultsArray = array();
	$sql =  "
	CREATE TEMPORARY TABLE `TempTable`
	(
	`id` INT NOT NULL,
	`rank` INT NOT NULL,
	`TiffFileName` VARCHAR(200) NOT NULL";

	foreach ($ColorFeatureArray as $value)
	{
		$sql = $sql . ",`" . $value . "` DOUBLE";
	}
	
	foreach (array_keys($OtherVariables) as $value)
	{
		$sql = $sql . ",`" . $value . "` " . $OtherVariables[$value] . "";
	}

	$sql = $sql . ") ENGINE=MEMORY;";
	$result = mysql_query($sql) or die(mysql_error());

	//echo "<BR><BR>" . $sql . "<BR><BR>";

	///////////////// INSERT VALUES INTO TEMPORARY TABLE
	foreach ($ColorFeatureArray as $value)
	{
		///////////// GATHER ID's AND rank to determine if they need to be inserted or updated
		$idArray = array();
		$sql = "SELECT `id`,`rank` FROM `TempTable`;";
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$idArray[$row['id']] = $row['rank'];
		}
		
		$StandardDeviation = 0;
		$sql = "SELECT STDDEV_POP(`".$value."`) FROM `" . $TableName . "`;";
		$result = mysql_query($sql) or die(mysql_error());
		$SD = mysql_fetch_array($result, MYSQL_ASSOC);
		$StandardDeviation = $SD["STDDEV_POP(`".$value."`)"];
		
		
		$sql =  "	
		(
			SELECT `id`, `TiffFileName` , `" . $value . "` , `" . implode("` , `",array_keys($OtherVariables)) . "`
			FROM `" . $TableName . "`
			WHERE `" . $value . "` > '" . $DataArray[$value] . "'
			AND `".$value."` < '".($DataArray[$value] + ($StandardDeviation * $StandardDeviationsAway)) . "' 
			ORDER BY `" . $value . "` ASC 
			LIMIT ".$ResultsLimit."
		)
		UNION 
		(
			SELECT `id`, `TiffFileName` , `" . $value . "` , `" . implode("` , `",array_keys($OtherVariables)) . "`
			FROM `" . $TableName . "`
			WHERE `" . $value . "` <= '" . $DataArray[$value] . "'
			AND `".$value."` > '".($DataArray[$value] - ($StandardDeviation * $StandardDeviationsAway)) . "' 
			ORDER BY `" . $value . "` DESC 
			LIMIT ".$ResultsLimit."
		)
		ORDER BY ABS( `" . $value . "` - '" . $DataArray[$value] . "' )
		LIMIT ".$ResultsLimit.";
		";
		$rank = 1;
		$result = mysql_query($sql) or die(mysql_error());
		$NumResultsArray[$value] = mysql_num_rows($result);
		
		//echo "<BR><BR>" . $sql . "<BR><BR>";
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if(array_key_exists($row['id'],$idArray) == true)
			{
				$sql = "UPDATE `TempTable` SET `rank`='".($rank+$idArray[$row['id']])."', `" . $value . "`='".$row[$value]."' WHERE `id`='".$row['id']."';";
			}
			else
			{
				$sql = "INSERT INTO `TempTable` (`id`,`rank`,`TiffFileName`,`" . $value . "`,`" . implode("`,`",array_keys($OtherVariables)) . "`) VALUES ('".$row['id']."','" . $rank . "','".$row['TiffFileName']."','".$row[$value]."'";
				foreach (array_keys($OtherVariables) as $variable)
				{
					$sql = $sql . ",'".$row[$variable]."'";
				}
				$sql = $sql . ")";
			}
			$result2 = mysql_query($sql) or die(mysql_error());
			$rank = $rank + 1;
		}
		
	}

	///////////////// SELECT VALUES FROM TABLE
	$sql = "SELECT * FROM `TempTable` WHERE `" . join("` <> '0' AND `",$ColorFeatureArray) . "` <> '0' ORDER BY `rank` ASC;";
	$result = mysql_query($sql) or die(mysql_error());
	$ReturnValues = array("result" => $result, "NumResultsArray" => $NumResultsArray);
	return $ReturnValues;
}

?>