<?php 
    //https://stackoverflow.com/questions/8311320/how-to-change-the-session-timeout-in-php
    ini_set('session.gc_maxlifetime', 3600);
    session_set_cookie_params(3600);
    session_start(); 

    $now = time();
    if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
        // this session has worn out its welcome; kill it and start a brand new one
        session_unset();
        session_destroy();
        session_start();
    }

    // either new or old, it should live at most for another hour
    $_SESSION['discard_after'] = $now + 3600;
?>
<!DOCTYPE html>
<html>
<title>Check Out</title>
<head>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: auto;
        }

        #navbar ul {
            position: fixed;
            list-style-type: none;
            margin: auto;
            padding: 0;
            overflow: hidden;
            padding: 14px 16px;
            background-color: black;
            text-decoration: none;
            top: 0;
            width: 100%;
        }

        #navbar li {
            float: left;
        }

        #navbar li a {
            color: white;
            text-align: center;
            padding-top: 40px;
            font-size: 40px;
        }

        .companyName {
            font-style: italic;
        }

        h1 {
            margin-top: 111px;
            width: 100%;
            text-align: center;
            font-size: 274%;
        }

        .button {
            font-size: 111%;
            background-color: navy;
            color: whitesmoke;
            border-radius: 11px;
            cursor: pointer;
        }

        h3 {
            width: 100%;
            text-align: center;
        }

        .error { 
            color: red; 
        }

        #form {
            margin: auto;
            width: 44%;
            padding: 11px;
        }
    </style>
</head>

<body>
    <div id="navbar">
        <ul>
            <li><a class='companyName'>Herzt-UTS</a></li>
            <li style="padding-left: 31%;"><a>Car Rental Center</a></li>
        </ul>
    </div>
    <div>
        <h1>Check Out</h1>
        <div id="form">
            <?php 
                if(isset($_POST["booking"])) { 
                    if(!empty($_POST["fname"]) && !empty($_POST["lname"]) && !empty($_POST["address1"]) && !empty($_POST["city"]) && !empty($_POST["postcode"]) && !empty($_POST["email"])) {
                        sendMail();
                    } else if (empty($_POST["fname"]) || empty($_POST["lname"]) || empty($_POST["address1"]) || empty($_POST["city"]) || empty($_POST["postcode"])) {
                        if(empty($_POST["fname"])) { $fnameError = "First name is required!"; }
                        if(empty($_POST["lname"])) { $lnameError = "Last name is required!"; }
                        if(empty($_POST["address1"])) { $addressError = "Address is required!"; }
                        if(empty($_POST["city"])) { $cityError = "City is required!"; }
                        if(empty($_POST["postcode"])) { $postcodeError = "Postcode is required!"; }
                        if(empty($_POST["email"])) { $emailError = "Email is required!"; }
                        else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                            $emailError = "Invalid email!"; 
                            echo "<script> document.getElementById('email').focus();</script>";
                        }
                    }
                } 
            ?>
            <form action="CheckOut.php" method="POST">
                <h3>Customer Details and Payment</h3>
                <table>
                    <tr><span class="error">Please fill in your details. * indicates required field</span></tr>
                    <tr><td>First Name: </td>
                        <td><input name="fname" type="text" autofocus value="<?php echo $_POST["fname"];?>">
                        <span class="error">* <?php echo $fnameError;?></span>
                    </td></tr>
                    <tr><td>Last Name: </td>
                        <td><input name="lname" type="text" value="<?php echo $_POST["lname"];?>">
                        <span class="error">* <?php echo $lnameError;?></span>
                    </td></tr>
                    <tr><td>Email address: </td>
                        <td><input id="email" name="email" type="text" value="<?php echo $_POST["email"];?>">
                        <span class="error">* <?php echo $emailError;?></span>
                    </td></tr>
                    <tr><td>Mailing Address 1: </td>
                        <td><input name="address1" type="text" value="<?php echo $_POST["address1"];?>">
                        <span class="error">* <?php echo $addressError;?></span>
                    </td></tr>
                    <tr><td>Mailing Address 2: </td>
                        <td><input name="address2" type="text" value="<?php echo $_POST["address2"];?>">
                    </td></tr>
                    <tr><td>City: </td>
                        <td><input name="city" type="text" value="<?php echo $_POST["city"];?>">
                        <span class="error">* <?php echo $cityError;?></span>
                    </td></tr>
                    <tr><td>State: </td>
                        <td><select name="state">
                                <option value="NSW">New South Wales</option>
                                <option value="VIC">Victoria </option>
                                <option value="QLD">Queensland</option>
                                <option value="ACT">Australia Capital Territory</option>
                                <option value="NT">Northern Territory</option>
                                <option value="SA">South Australia</option>
                                <option value="TAS">Tasmania</option>
                                <option value="WA">West Australia</option>
                            </select>
                        <span class="error">*</span>
                    </td></tr>
                    <tr><td>Postcode: </td>
                        <td><input name="postcode" type="text" value="<?php echo $_POST["postcode"];?>">
                        <span class="error">* <?php echo $postcodeError;?></span>
                    </td></tr>
                    <tr><td>Payment Type: </td>
                        <td><select name="payment">
                                <option value="visa">Visa</option>
                                <option value="mastercard">Mastercard</option>
                                <option value="ae">American Express</option>
                                <option value="jcb">JCB</option>
                            </select>
                        <span class="error">*</span>
                    </td></tr>
                    <tr><td><p id="totalBill"></p></td></tr>
                    <tr><td colspan="2"><input class ="button" name="backToMainPage" type="button" value="Continue Selection" onclick="location.replace('index.html'); sessionStorage.removeItem('Total');">
                    <input class ="button" name="booking" type="submit" value="Booking"></td></tr>
                    <tr><input name="totalBillInput" id="totalBillInput" style="display: none;" value=""></tr>
                </table>
            </form>
        </div>
    </div>
    <div id="details"></div>
<script>
    document.getElementById("totalBill").innerText = "You are required to pay $" + sessionStorage.getItem("Total");
    document.getElementById("totalBillInput").value = sessionStorage.getItem("Total");
    <?php 
        // This function evaluate the PHP scripts before getting the contents of the file
        // https://stackoverflow.com/questions/13028229/php-evaluate-code-before-getting-file-content
        function get_include_contents($filename){
            if(is_file($filename)){
              ob_start();
              include $filename;
              $contents = ob_get_contents();
              ob_end_clean();
              return $contents;
            }
            return false;
        }

        function sendMail() {
            if(!empty($_POST["fname"]) && !empty($_POST["lname"]) && !empty($_POST["address1"]) && !empty($_POST["city"]) && !empty($_POST["postcode"]) && !empty($_POST["email"])) {
                $subject = "Your reservation at Hertz-UTS";
                $message = get_include_contents('MailTemplate.html');
                //https://stackoverflow.com/questions/22580380/how-to-include-a-call-to-a-file-in-php-mail-function#new-answer
                $parts_to_mod = array("FIRSTNAME", "LASTNAME", "TOTAL");
                $replace_with = array($_POST["fname"], $_POST["lname"], $_POST["totalBillInput"]);
                for($i=0; $i<count($parts_to_mod); $i++){
                    $message = str_replace($parts_to_mod[$i], $replace_with[$i], $message);
                }
                $header  = "MIME-Version: 1.0" . "\r\n";
                $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";                     
                $header .= "From: donotreply@hertz-uts.com.au" . "\r\n";
                $header .= "To: ".$_POST["email"]."\r\n";
                mail((string)$_POST["email"], $subject, $message, $header);
                session_unset();
                session_destroy();
                echo "<script>
                        sessionStorage.clear();
                        localStorage.clear();
                        location.replace('index.html');
                        alert('A confirmation of your reservation has been sent to your email!');
                    </script>";
            }
        }
    ?>
</script>
</body>
</html>