<?php
		$dimension = $_POST['dimenson'];
		$file_name = "";
		if ($dimension == 3) {
			// Call python and return generate json file
			$file_name = "dim3.json";
		}
		else if ($dimension == 4) {
			// Call python and return generate json file
			$file_name = "dim4.json";		
		}
		else if ($dimension == 5) {
			// Call python and return generate json file
			$file_name = "dim5.json";
		}		
		echo $file_name;
?>
