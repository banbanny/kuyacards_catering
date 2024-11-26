<?php

	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "catering_db";
	if(!$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
	{
		die("failed to connect!");
	}


	function check_login($conn)
    {
        if(isset($_SESSION['userID']))
        {
            $id = $_SESSION['userID'];
            $query = "select * from users where userID = '$id' limit 1";
            $result = mysqli_query($conn,$query);
            
            if($result && mysqli_num_rows($result)>0)
            {
                $user_data = mysqli_fetch_assoc($result);
                return $user_data;
            }
        }else {
            //direct to login
            header("Location: index.php");
            die;
        }
    }
