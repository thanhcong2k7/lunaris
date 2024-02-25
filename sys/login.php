<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	session_start();
	unset($_SESSION["userwtf"]);
	unset($_SESSION["wrongpwd"]);
	if (isset($_POST["meo"])){
		if(isset($_POST["pwd"])){
			$myfile = fopen("logs.txt", "a") or die("Internal system error! Please contact the administrator here: nhocpeacock@gmail.com. Error code: LOG_NF");
			$servername = "localhost";
			$database = "lunaris_test";
			$username = "root";
			$password = "";
			// Create connection
			$conn = mysqli_connect($servername, $username, $password, $database);
			// Check connection
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			fwrite($myfile,"[".date("l d/m/Y H:i:s")." GMT+7]: MySQL Connected successfully, input=".$_POST["meo"]." ".$_POST["pwd"]."\n");
			$res = $conn->query("SELECT * FROM user WHERE username='".$_POST["meo"]."';");
			if ($res){
				if (mysqli_num_rows($res)==0){
					$res2 = $conn->query("SELECT * FROM user WHERE email='".$_POST["meo"]."';");
					if ($res2){
						if (mysqli_num_rows($res2)==0){
							$_SESSION["wrongpwd"]=1;
							header("Location: ../pages/sign-in.php");
						} else if (mysqli_num_rows($res2)>1){
							fwrite($myfile,"[".date("l d/m/Y H:i:s")." GMT+7]: Duplicated queries at username=".$_["meo"]."\n");
							die("Something went wrong with your credentials in our database! Please file a report and send to the administrator through this email and we'll recover your account: nhocpeacock@gmail.com. Thanks!");
							header("Location: ../");
						} else {
							while($row=$res2->fetch_assoc()){
								if($row["pwd"]==MD5($_POST["pwd"]))
									$_SESSION["userwtf"]=$row["userID"];
								else {
									$_SESSION["wrongpwd"]=1;
									fwrite($myfile,"[".date("l d/m/Y H:i:s")." GMT+7]: Wrong password phase 2\n");
								}
							}
						}
						header("Location: ../pages/sign-in.php");
					}
				} else if (mysqli_num_rows($res)>1){
					fwrite($myfile,"[".date("l d/m/Y H:i:s")." GMT+7]: Duplicated queries at username=".$_["meo"]."\n");
					die("Something went wrong with your credentials in our database! Please file a report and send to the administrator through this email and we'll recover your account: nhocpeacock@gmail.com. Thanks!");
				} else {
					while($row=$res->fetch_assoc()){
						if($row["pwd"]==MD5($_POST["pwd"])){
							$_SESSION["userwtf"]=$row["userID"];
							header("Location: ../");
						} else {
							$_SESSION["wrongpwd"]=1;
							fwrite($myfile,"[".date("l d/m/Y H:i:s")." GMT+7]: Wrong password phase 1\n");
						}
					}
					header("Location: ../pages/sign-in.php");
				}
			}
			fclose($myfile);
			mysqli_close($conn);
		}
	}
?>