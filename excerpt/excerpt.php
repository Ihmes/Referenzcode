
<?php

  /*
   * From Helper-Class-Improve-Youtube-Data
   *
   *
   */

 	/* Improve viewCount, likeCount, dislikeCount */
 	function getYtStatistics($oConn, $sSql){

 		if (mysqli_num_rows($oQueryResult) > 0) {

 		    while($row = mysqli_fetch_assoc($oQueryResult)) {

             unset($sViewCount, $iLikeCount, $iDislikeCount, $iChannelTitle, $iChannelId);

 						$videoinfo = file_get_contents('https://www.googleapis.com/youtube/v3/videos?id='. $row['yID'] .'&part=snippet,status,contentDetails,statistics&part=statistics&key='. youtube_key);
 				   	$videoinfo = json_decode($videoinfo, true);

 				   	foreach ($videoinfo['items'] as $embed){

 				       $sViewCount= $embed['statistics']['viewCount'];
 				       $iLikeCount= $embed['statistics']['likeCount'];
 				       $iDislikeCount= $embed['statistics']['dislikeCount'];
 				       $iChannelTitle= $embed['snippet']['channelTitle'];
 				       $iChannelId= $embed['snippet']['channelId'];
 				   	} //foreach

           $sqlResponse = updatetYtStatistics($sViewCount,$iLikeCount, $iDislikeCount, $iChannelTitle, $iChannelId, $row['yID']);

 					if ($oConn->query($sSqlResponse) === TRUE) {
 					    echo "<br><strong>New record created successfully</strong><br>";
 					} else {
 					    echo "<br><strong>Error: " . $sqlResponse . "<br>" . $oConn->error. "</strong><br>";
 					} //if
 			} //while
 		} //if
 	} //function

 	/* Improve SD Default Images */
 	function ipvVideoTumbs(($oConn, $oQueryResult, $sImagePathSd){

 		if (mysqli_num_rows($oQueryResult) > 0) {

 		  while($row = mysqli_fetch_assoc($oQueryResult)) {

 				$sFilename = $sImagePathSd . $row['vTumbs'];
 				if (!file_exists($sFilename)){

             try {
 				      copy('http://img.youtube.com/vi/'. $row['yID'] .'/0.jpg', $sImagePathSd . ''. $row['vTumbs']);
             catch (Exception $e) {
                  die ($e->getMessage());
             } //try
 				} //if
 		  } //while
 		} //if
 	} // function



  /*
   * From helper Class-Packages
   *
   *
   */

  /* Download packages and unzip */
  function getUpdatePackages($sFileURL, $sFileName, $sMd5Check){

      $escape = escapeshellarg($sFileURL);
      exec("wget " . $escape);
      $bUnzip = false;

      if(!empty($sMd5Check)){

          if(md5_file($sFileName) == $sMd5Check)
              $bUnzip = true;
          else
              echo "Checksum not correct";
      } else {
          $bUnzip = true;
      }

      if($bUnzip) {

          exec(escapeshellcmd("tar -xzvf $sFileName"),$aOutput);
          print_r($aOutput);
      }
  }

  /* Copy directory*/
  function runFolderCopy($sSource, $sDest){

      if (!is_dir($sSource))
          return false;

      $sShellCommand = "cp -Rv $sSource $sDest";
      exec($sShellCommand, $aOutput);

      return $aOutput;
  }

  /* Delete directory */
  function runFolderDelete($sSource){

      if (!is_dir($sSource))
          return false;

      $sShellCommand = "rm -Rv $sSource";
      exec($sShellCommand,$aOutput);

      return $aOutput;

  }

?>
