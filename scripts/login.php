<?php

require("../lib/libauth.php");
require("../lib/libmysql.php");

connectToMySQL();

if (isset($_POST['username']))
{

        $authKey = loadAuth(0);

        $userQuery = "SELECT * FROM users WHERE Username='{$_POST['username']}'";
        $userResource = mysql_query($userQuery);
        $time = "";
        $token = "";
        while ($row = mysql_fetch_assoc($userResource))
        {

                $time = $row['Time'];
                $token = $row['Password'];

        }

	
        $expectedPass = hash_hmac("sha256", "{$_POST['password']} {$time}", $authKey);
        if ($expectedPass == $token)
        {

                setcookie( "token", $token, time() +  3600, "/");
		echo "1";

        } else {

		echo "0";

	}

} else {

	echo "0";

}

?>
