<?php 
include ("connection.php");

// Fetch user data to pre-fill the form
if (isset($_GET['updateid']) && filter_var($_GET['updateid'], FILTER_VALIDATE_INT)) {
    $id = $_GET['updateid'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo 'User not found';
        exit;
    }
} else {
    echo 'Invalid ID';
    exit;
}

$errors = [];

if (isset($_POST['submit'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $dob = trim($_POST['dob']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $phonenumber = trim($_POST['phonenumber']);
    $currentFolder = getcwd();
    $uploadFolder = "/uploads/";

    // Validation
    if (empty($fname)) $errors[] = "First name is required.";
    if (empty($lname)) $errors[] = "Last name is required.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($message)) $errors[] = "Message is required.";
    if (!preg_match('/^[0-9]{10}$/', $phonenumber)) $errors[] = "Phone number should be 10 digits.";

    // File upload handling
    $fileName = $_FILES['userFile']['name'];
    $fileTmpName = $_FILES['userFile']['tmp_name'];

    if ($fileName) {
        $uploadApproved = true;
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($fileTmpName);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errors[] = "Only JPG, PNG, and GIF files are allowed.";
            $uploadApproved = false;
        }

        if ($uploadApproved) {
            // Delete previous file if exists
            $previousFile = $currentFolder . $uploadFolder . $row['userFile'];
            if (file_exists($previousFile)) {
                unlink($previousFile);
            }

            // Move new file to upload folder
            $uploadPath = $currentFolder . $uploadFolder . basename($fileName);
            if (!move_uploaded_file($fileTmpName, $uploadPath)) {
                $errors[] = "Error uploading the file.";
            }
        }
    } else {
        // If no new file uploaded, retain the previous file name
        $fileName = $row['userFile'];
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET fname=?, lname=?, dob=?, email=?, message=?, phonenumber=?, userFile=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $fname, $lname, $dob, $email, $message, $phonenumber, $fileName, $id);
        $data = $stmt->execute();

        if ($data) {
            header('Location: display.php');
            echo "<script>alert('Updated Successfully')</script>";
        } else {
            echo "Update failed: " . $stmt->error;
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
    <title>Update Form</title>
</head>
<body>
<div class="container mt-5">
        <h2>Update Form</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="fname" placeholder="Enter first name" value="<?php echo htmlspecialchars($row['fname']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lname" placeholder="Enter last name" value="<?php echo htmlspecialchars($row['lname']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" id="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" rows="3" name="message" placeholder="Enter your message" required><?php echo htmlspecialchars($row['message']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">+91</span>
                    </div>
                    <input type="tel" class="form-control" id="phone" name="phonenumber" placeholder="Enter phone number" value="<?php echo htmlspecialchars($row['phonenumber']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="filetoupload">File Upload</label>
                <input type="file" class="form-control-file" id="filetoupload" name="userFile">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
