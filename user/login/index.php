<?php
    session_start();
    // prepare variables
    $success = false;
    $message = "";
    $firstName = "";
    $lastName = "";
    $displayName = "";
    $emailAddress = "";
    $accountId = 0;

    $mysqli = new mysqli("localhost", "3ding", "W7vDdRMYrR9wq5wz", "3ding");
    if($mysqli->connect_error) {
        exit('Error connecting to database'); //Should be a message a typical user could understand in production
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $userId = $mysqli->real_escape_string($_POST["userId"]);
    $password = $_POST["pass"];

    $stmt = $mysqli->prepare("SELECT *  FROM users WHERE userName = ?");
    $stmt->bind_param("s", $_POST['userId']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) {
        $success = false;
        $message = "Konto nicht gefunden";
    } else {
        while($row = $result->fetch_assoc()) {
            $pass = $row['pass'];
            if ($pass != $_POST['pass']){
                $success = false;
                $message = "Passwort falsch";
            } else {
                $success = true;
                $accountId = $row['ID'];
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $displayName = $row['displayName'];
                $emailAddress = $row['emailAddress'];
            }
        }
    }
    $stmt->close();
    $_SESSION['login'] = $success;
    $_SESSION['displayName'] = $displayName;
    $_SESSION['firstName'] = $firstName;
    $_SESSION['lastName'] = $lastName;
    $_SESSION['emailAddress'] = $emailAddress;
    $_SESSION['accountId'] = $accountId;
?>
{
    "success": <?= ($success  ? "true" : "false") ?>,
    "message": "<?= $message ?>",
    "firstName" : "<?= $firstName ?>",
    "lastName": "<?= $lastName ?>",
    "displayName": "<?= $displayName ?>",
    "emailAddress": "<?= $emailAddress ?>"
}