<?php
// Include the database connection (make sure the path is correct)
include "conn.php"; // Ensure conn.php is in the same directory

// Initialize variables
$name = $email = $id_number = $course = "";
$nameErr = $emailErr = $id_numberErr = $courseErr = "";
$successMessage = "";

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = $_POST["name"];
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
        // Check if the email format is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate ID number
    if (empty($_POST["id_number"])) {
        $id_numberErr = "ID number is required";
    } else {
        $id_number = $_POST["id_number"];
    }

    // Validate course
    if (empty($_POST["course"])) {
        $courseErr = "Course is required";
    } else {
        $course = $_POST["course"];
    }

    // If no errors, insert into the database
    if (empty($nameErr) && empty($emailErr) && empty($id_numberErr) && empty($courseErr)) {
        try {
            // Prepare the SQL query to insert the user
            $query = "INSERT INTO users (name, email, id_number, course) VALUES (:name, :email, :id_number, :course)";
            $stmt = $conn->prepare($query);

            // Bind the parameters to the statement
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_number', $id_number);
            $stmt->bindParam(':course', $course);

            // Execute the query
            $stmt->execute();

            // Redirect to tableshoe.php after successful insertion
            header("Location: tableshoe.php");
            exit();
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert User</title>
    <!-- Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 100%;
            max-width: 500px;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-submit {
            border-radius: 8px;
        }
        .alert {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 form-container">
            <h2 class="form-title">STUDENT INFO RECORD</h2>

            <!-- Display success message -->
            <?php if ($successMessage) { ?>
                <div class="alert alert-success mt-3"><?php echo $successMessage; ?></div>
            <?php } ?>

            <!-- User Input Form -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" value="<?php echo $name;?>" placeholder="Enter your name">
                    <small class="text-danger"><?php echo $nameErr;?></small>
                </div>

                <div class="form-group">
                    <input type="text" name="email" class="form-control" value="<?php echo $email;?>" placeholder="Enter your email">
                    <small class="text-danger"><?php echo $emailErr;?></small>
                </div>

                <div class="form-group">
                    <input type="text" name="id_number" class="form-control" value="<?php echo $id_number;?>" placeholder="Enter your ID number">
                    <small class="text-danger"><?php echo $id_numberErr;?></small>
                </div>

                <div class="form-group">
                    <input type="text" name="course" class="form-control" value="<?php echo $course;?>" placeholder="Enter your course">
                    <small class="text-danger"><?php echo $courseErr;?></small>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
