<?php
include("connection.php");

$fnameError = $lnameError = $dobError = $emailError = $messageError = $phonenumberError = $fileError = "";
$fname = $lname = $dob = $email = $message = $phonenumber = $fileName = "";

if (isset($_POST['submit'])) {
    $isValid = true;

    // Retrieve form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $phonenumber = $_POST['phonenumber'];

    // Validate First Name
    if (empty($fname)) {
        $fnameError = "Please enter a valid first name.";
        $isValid = false;
    }

    // Validate Last Name
    if (empty($lname)) {
        $lnameError = "Please enter a valid last name.";
        $isValid = false;
    }

    // Validate Date of Birth
    if (empty($dob)) {
        $dobError = "Please enter your date of birth.";
        $isValid = false;
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Please enter a valid email address.";
        $isValid = false;
    }

    // Validate Message
    if (empty($message) || strlen($message) < 100) {
        $messageError = "Please enter a message with at least 100 characters.";
        $isValid = false;
    }

    // Validate Phone Number
    if (empty($phonenumber) || !preg_match('/^\d{10}$/', $phonenumber)) {
        $phonenumberError = "Please enter a valid phone number (10 digits).";
        $isValid = false;
    }

    // File upload handling
    $currentFolder = getcwd();
    $uploadFolder = "/uploads/";
    $uploadApproved = true;

    if (isset($_FILES['userFile'])) {
        $fileName = $_FILES['userFile']['name'];
        $fileSize = $_FILES['userFile']['size'];
        $fileTmpName = $_FILES['userFile']['tmp_name'];
        $fileType = $_FILES['userFile']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uploadPath = $currentFolder . $uploadFolder . basename($fileName);

        if ($uploadApproved) {
            if (!move_uploaded_file($fileTmpName, $uploadPath)) {
                $fileError = "Error uploading the file.";
                $isValid = false;
            }
        } else {
            $fileError = "Error uploading the file.";
            $isValid = false;
        }
    } else {
        $fileError = "Please select a file to upload.";
        $isValid = false;
    }

    // Insert form data into database if valid
    if ($isValid) {
        $sql = "INSERT INTO users (fname, lname, dob, email, message, phonenumber, userFile) 
                VALUES ('$fname', '$lname', '$dob', '$email', '$message', '$phonenumber', '$fileName')";
        $data = mysqli_query($conn, $sql);

        if ($data) {
            header('Location: display.php');
            exit; // Ensure no further output
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show text-center">
                    <a class="close" data-dismiss="alert" aria-label="close">×</a>
                    <strong>Error!</strong> Failed to submit data.
                  </div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Responsive Form</title>
    <style>
        body{
            background-color:whitesmoke;
        }
        
        input{
            height:30px;
            outline: none;
            border:none;
        }
        input[type="tel"]{
            width: 40px;
            margin-top:6px;
            padding:10px;
            border-radius: 50px;
        }
        textarea{
            width:40px;
        }
       .input-group-text{
        height: 30px

        }
        #phonenumber{
            border-radius: 5px;
            width: 40px;

        }
        .form-control {
            transition: border 0.4s ease;
        }

        .form-control:hover {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .input-group-text {
            transition: border 0.4s ease;
        }

        .input-group:hover .form-control,
        .input-group:hover .input-group-text {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="container m-6 mt-2" id="con" style="background: rgb(131,58,180);
background: -moz-linear-gradient(90deg, rgba(131,58,180,1) 19%, rgba(93,140,224,1) 68%, rgba(90,147,228,1) 72%, rgba(88,152,231,1) 75%, rgba(69,193,252,1) 100%);border-radius:5px;width:400px; height:600px;font-size:14px;font-weight:700;color:white;">
        <h2>Form</h2>
        <form action="#" method="POST" enctype="multipart/form-data" id="contactForm">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstname">First Name <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="firstname" name="fname" placeholder="Enter first name" value="<?php echo htmlspecialchars($fname); ?>" required>
                    <span id="fnameError" class="text-danger"><?php echo $fnameError; ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="lastname">Last Name<span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="lastname" name="lname" placeholder="Enter last name" value="<?php echo htmlspecialchars($lname); ?>" required>
                    <span id="lnameError" class="text-danger"><?php echo $lnameError; ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="dob">Date of Birth<span style="color:red">*</span></label>
                    <input type="date" class="form-control" name="dob" id="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
                    <span id="dobError" class="text-danger"><?php echo $dobError; ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email address<span style="color:red">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span id="emailError" class="text-danger"><?php echo $emailError; ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="message">Message<span style="color:red">*</span></label>
                <textarea class="form-control" id="message" rows="3" name="message" placeholder="Enter your message" required><?php echo htmlspecialchars($message); ?></textarea>
                <label for="message">(Maximum 100 characters)</label>
                <span id="messageError" class="text-danger"><?php echo $messageError; ?></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number<span style="color:red">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text m-1">+91</span>
                    </div>
                    <input type="tel" class="form-control" id="phonenumber" name="phonenumber" pattern="[0-9]{10}" placeholder="Enter phone number" value="<?php echo htmlspecialchars($phonenumber); ?>" required>
                    <span id="phonenumberError" class="text-danger"><?php echo $phonenumberError; ?></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="filetoupload">File Upload(pdf/jpeg/png/images/xlxs/doc)<span style="color:red">*</span></label>
                <input type="file" class="form-control-file" id="filetoupload" name="userFile" required>
                <span id="fileError" class="text-danger"><?php echo $fileError; ?></span>
            </div>
            <button type="submit" name="submit" id="submitButton" class="btn btn-primary my-3">Submit</button>
        </form>
    </div>
</body>
</html>
