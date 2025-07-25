<form action='index.php' >
	<button name='submit'>Go To DJLand</button>
</form>
<h1>Tables Created</h1>
<table class='table-striped'>
	<th>File</th><th>Ran</th><th>Command</th><th>Errors</th>
<?php
	require($_SERVER['DOCUMENT_ROOT'].'/newdjland/config.php');
	require($_SERVER['DOCUMENT_ROOT'].'/newdjland/app/headers/run_sql.php');

	$db_connection = new mysqli($db['address'], $db['username'], $db['password']);
	
	// Check if database exists
	$check_db = $db_connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'djland'");
	if($check_db->num_rows == 0) {
		$create_schema = 'CREATE SCHEMA djland';
		$schema_result = $db_connection->query($create_schema);
		echo "<tr><td>Create Schema</td>";
		echo "<td>".($schema_result ? 1 : 0)."/1</td>";
		echo "<td>".$create_schema."</td>";
		echo "<td>".($schema_result ? $db_connection->error : '')."</td></tr>";
	} else {
		echo "<tr><td>Create Schema</td>";
		echo "<td>1/1</td>";
		echo "<td>Database 'djland' already exists</td>";
		echo "<td></td></tr>";
	}

	$db_connection = new mysqli($db['address'], $db['username'], $db['password'],"djland");

	//foreach on the data_structures folder.
	$data_structure_path = $_SERVER['DOCUMENT_ROOT'].'/newdjland/setup/database_structures';
	$data_structures = scandir($data_structure_path);

	foreach($data_structures as $key=>$data_structure){
		if(isSQL($data_structure)) {

			$response = run_sql_file($data_structure_path."/".$data_structure,$db_connection);
			foreach($response as $result){
				echo "<tr><td>".$data_structure."</td>";
				echo "<td>".$result['success']."/".$result['total']."</td>";
				echo "<td>".$result['command']."</td>";
				echo "<td>".join(',',$result['error'])."</td></tr>";
			}
		}
	}

	//foreach on the defaults folder
	$defaults_path = $_SERVER['DOCUMENT_ROOT'].'/newdjland/setup/defaults';
	$defaults = scandir($defaults_path);

	foreach($defaults as $key=>$default){
		if(isSQL($default)) {

			$response = run_sql_file($defaults_path."/".$default,$db_connection);
			foreach($response as $result){
				echo "<tr><td>".$default."</td>";
				echo "<td>".$result['success']."/".$result['total']."</td>";
				echo "<td>".$result['command']."</td>";
				echo "<td>".join(',',$result['error'])."</td></tr>";
			}
		}
	}

?>
</table>
