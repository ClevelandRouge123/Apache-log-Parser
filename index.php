<html>
	<head>
		<title>Apache log parser</title>
		<style>
			table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
			}
			
			td, th {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
			}
			
			tr:nth-child(even) {
			background-color: #dddddd;
			}
		</style>
	</head>
	<body>
		<?php
			if ( isset($_POST["submit"]) ) {
				if ( isset($_FILES["file"])) {
					//if there was an error uploading the file
					if ($_FILES["file"]["error"] > 0) {
						echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
					}
					else {
						//Print file details
						echo "Upload: " . $_FILES["file"]["name"] . "<br />";
						echo "Type: " . $_FILES["file"]["type"] . "<br />";
						echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
						echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
						
						//if file already exists
						if (file_exists("upload/" . $_FILES["file"]["name"])) {
							echo $_FILES["file"]["name"] . " already exists. ";
						}
						else {
							//Store file in directory "upload" with the name of "uploaded_file.txt"
							$storagename = "uploaded_file.csv";
							move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
							echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";
						}
					}
					} else {
					echo "No file selected <br />";
				}
			}
		?>
		<table width="600">
			<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
				
				<tr>
					<td width="20%">Select file</td>
					<td width="80%"><input type="file" name="file" id="file" /></td>
				</tr>
				
				<tr>
					<td>Submit</td>
					<td><input type="submit" name="submit" /></td>
				</tr>
				
			</form>
		</table>
		<?php 
			//	$file = fopen("upload/uploaded_file.csv","r");
			//print_r(fgetcsv($file));
			//$x = 0;
			//	while (($line = fgetcsv($file)) !== FALSE) {
			$fh = fopen("upload/uploaded_file.csv",'r') or die($php_errormsg);
			$i = 1;
			//$requests = array();
			
			//$line[0] = '1004000018' in first iteration
			// print_r($line);
			$pattern = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) "([^"]*)" "([^"]*)"$/';			//foreach($line as $row){
			echo"	<table style='width: 100%;'>
			<tr>
			<th>remote host</th>
			<th>log name</th>
			<th>user</th>
			<th>time</th>
			<th>method</th>
			<th>request</th>
			<th>protocol</th>
			<th>status</th>
			<th>bytes</th>
			<th>referer</th>
			<th>useragent</th>
			</tr>";
			while (! feof($fh)) {
				
				// $x++;
				if ($s = trim(fgets($fh,16384))) {
					
					if (preg_match($pattern,$s,$matches)) {
						list($whole_match,$remote_host,$logname,$user,$time,
						$method,$request,$protocol,$status,$bytes,$referer,
						$user_agent) = $matches;       
						//$requests[$request]++;
						
						echo"
						<tr>
						<td>".$remote_host."</td>
						<td>".$logname."</td>
						<td>".$user."</td>
						<td>".$time."</td>
						<td>".$method."</td>
						<td>".$request."</td>
						<td>".$protocol."</td>
						<td>".$status."</td>
						<td>".$bytes."</td>
						<td>".$referer."</td>
						<td>".$user_agent."</td>
						</tr>
						";
						}else {
						// complain if the line didn't match the pattern 
						error_log("Can't parse line $i: $s");
					}
					$i++;
					
					//echo "'.$row.'<br>";
					
				}
			}
			echo"</table>";
			fclose($fh) or die($php_errormsg);
			//	arsort($requests);
			
			/*	foreach ($requests as $request => $accesses) {
				printf("%6d   %s\n",$accesses,$request);
				echo "'.$accesses.'<br>";
				}
			*///	}
			//fclose($file);
		?>
	</body>
</html>