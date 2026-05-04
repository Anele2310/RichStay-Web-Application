<?php
// handler.php

session_start();
ob_start();

// --- Database connection ---
$host = "localhost";      
$user = "root";           
$pass = "";               
$db   = "rental_portal";  

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Detect which action the form wants ---
$action = $_POST['action'] ?? '';

switch ($action) {

    /*REGISTER NEW USER*/
    case "register":
        $firstName = $_POST['firstName'];
        $lastName  = $_POST['lastName'];
        $email     = $_POST['email'];
        $userType  = $_POST['userType']; // student or landlord
        $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
       
 // check if email already exists
        $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
        echo "<script>alert('Email already registered. Please log in instead.'); window.location.href='indexru.html';</script>";
    exit();
}
$check->close();


$sql = "INSERT INTO users (first_name, last_name, email, user_type, password)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $firstName, $lastName, $email, $userType, $password);

if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name'] = $firstname; //to store the user's firstname for greeting("Hi Mark!)
            $_SESSION['user_type'] = $userType;
            

            // Redirect after signup
            if ($userType == "student") {
                header("Location: studentru.php");
            } else {
                header("Location: ownerru.php");
            }
            exit();
        }

        $stmt->close();
        break;

    /*LOGIN USER*/
    case "login":
        $email    = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT user_id, first_name, password, user_type FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $first_name, $hashed_password, $userType);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['name'] = $first_name;
                $_SESSION['user_type'] = $userType;

                // Redirect based on role
                if (empty($userType) || strtolower($userType) == "student") {
                    header("Location: studentru.php");
                } elseif (strtolower($userType) == "landlord") {
                header("Location: ownerru.php"); 
                } else {
                echo "<script>alert('Unknown user type'); window.location.href='indexru.html';</script>";
            }
                exit();
           
        }else{
               echo "<script>alert('Incorrect password'); window.location.href = 'indexru.html';</script>"; //alerts when the password is wrong
        }

       }else {
               echo "<script>alert('Email not found'); window.location.href='indexru.html';</script>";//When no account matches the entered email
       }

        $stmt->close();
        break;

    /*ADD PROPERTY (Landlord)*/
    case "add_property":
        $title       = $_POST['title'];
        $type        = $_POST['propertyType'];
        $rent        = $_POST['rent'];
        $bedrooms    = $_POST['bedrooms'];
        $bathrooms   = $_POST['bathrooms'];
        $sqm         = $_POST['squareMeters'];
        $address     = $_POST['address'];
        $area        = $_POST['area'];
        $distance    = $_POST['distance'];
        $description = $_POST['description'];

        $sql = "INSERT INTO properties1
                (title, property_type, rent, bedrooms, bathrooms, square_meters, address, area, distance, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiiisssss", $title, $type, $rent, $bedrooms, $bathrooms, $sqm, $address, $area, $distance, $description);

        $stmt->execute();
        $stmt->close();
        break;

    /*APPLY FOR PROPERTY*/
    case "apply_property":
        $fullName   = $_POST['fullName'];
        $phone      = $_POST['phone'];
        $institution= $_POST['institution'];
        $studentId  = $_POST['studentId'];
        $income     = $_POST['income'];
        $incomeSrc  = $_POST['incomeSource'];

        $incomeProofPath = "uploads/" . basename($_FILES['incomeProof']['name']);
        $idCopyPath      = "uploads/" . basename($_FILES['idCopy']['name']);

        move_uploaded_file($_FILES['incomeProof']['tmp_name'], $incomeProofPath);
        move_uploaded_file($_FILES['idCopy']['tmp_name'], $idCopyPath);

        $sql = "INSERT INTO applications 
                (fullName, phone, institution, studentId, income, incomeSource, incomeProof, idCopy)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdsss", $fullName, $phone, $institution, $studentId, $income, $incomeSrc, $incomeProofPath, $idCopyPath);

        $stmt->execute();
        $stmt->close();
        break;

    /*FORGOT PASSWORD*/
    case "forgot_password":
        $email = trim($_POST['email']);

        // check if email exists
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // generate temporary password
            $tempPass = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'), 0, 8);
            $hashedPass = password_hash($tempPass, PASSWORD_DEFAULT);

            // update password in database
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $hashedPass, $email);
            $update->execute();

            // prepare email
            $subject = "Your RichStay Temporary Password";
            $message = "Hi,\n\nYour temporary password is: $tempPass\n\nPlease log in and change it immediately.\n\n- RichStay Team";
            $headers = "From: no-reply@richstay.com";

            // send email (if server supports mail)
            if (mail($email, $subject, $message, $headers)) {
                echo "<script>alert('A temporary password has been sent to your email.'); window.location='indexru.html';</script>";
            } else {
                echo "<script>alert('Temporary password generated, but email could not be sent. Please contact support.'); window.location='indexru.html';</script>";
            }

        } else {
            echo "<script>alert('No account found with that email address.'); window.history.back();</script>";
        }
        break;


    /*DEFAULT*/
    default:
        echo " Invalid request.";
        break;
}

$conn->close();
ob_end_flush
?>
