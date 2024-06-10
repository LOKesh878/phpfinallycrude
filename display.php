<?php 
include ("connection.php");
$query = "SELECT *, @rownum := @rownum + 1 AS row_number 
          FROM users, (SELECT @rownum := 0) r
          ORDER BY id ASC"; // Order by id in ascending order
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style1.css">
    <style>
        /* Custom CSS for table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 6px 12px;
            margin: 2px;
        }
        .btn a {
            text-decoration: none;
            color: #fff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
    </style>
    <title>Form Data Display</title>
</head>
<body>
    <div class="container mt-5">
        <button class="btn btn-primary my-5"><a href="form.php" class="text-light" style="text-decoration:none;">Add User</a></button>
        <table class="table">
            <thead>
                <tr>
                    <th>Sr.no</th>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Phone Number</th>
                    <th>File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = $row['id'];
                        $fname = $row['fname'];
                        $lname = $row['lname'];
                        $dob = $row['dob'];
                        $email = $row['email'];
                        $message = $row['message'];
                        $phonenumber = $row['phonenumber'];
                        $filename = $row['userFile'];

                        // Assuming the uploaded files are stored in the 'uploads' folder
                        $filePath = 'uploads/' . $filename;

                        echo '<tr> 
                            <td>' . $id . '</td>
                            <td>' . $fname . '</td>
                            <td>' . $lname . '</td>
                            <td>' . $dob . '</td>
                            <td>' . $email . '</td>
                            <td>' . $message . '</td>
                            <td>' . $phonenumber . '</td>
                            <td><img src="' . $filePath . '" alt="' . $filename . '" width="100" height="100"></td>
                            <td>
                                <button class="btn btn-primary">
                                    <a href="update.php?updateid='. $id .'" class="text-light text-decoration-none">Edit</a>
                                </button>
                                <button class="btn btn-danger">
                                    <a href="delete.php?deleteid=' . $id . '" class="text-light text-decoration-none">Delete</a>
                                </button>
                            </td>
                        </tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
