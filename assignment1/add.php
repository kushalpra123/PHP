<?php include 'includes/header.php'; ?>
<?php include 'database.php'; ?>

<?php
$name = $grade = $email = "";
$name_err = $grade_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }
    if (empty(trim($_POST["grade"]))) {
        $grade_err = "Please enter a grade.";
    } else {
        $grade = trim($_POST["grade"]);
    }
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($name_err) && empty($grade_err) && empty($email_err)) {
        $stmt = $conn->prepare("INSERT INTO students (name, grade, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $grade, $email);
        $stmt->execute();
        $stmt->close();
        header("Location: view.php");
        exit();
    }
}
?>

<div class="card mx-auto" style="max-width: 500px;">
    <div class="card-body">
        <h2 class="card-title mb-4">Add Student</h2>
        <form action="add.php" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>">
                <div class="invalid-feedback"><?php echo $name_err; ?></div>
            </div>
            <div class="form-group">
                <label>Grade</label>
                <input type="text" name="grade" class="form-control <?php echo (!empty($grade_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($grade); ?>">
                <div class="invalid-feedback"><?php echo $grade_err; ?></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                <div class="invalid-feedback"><?php echo $email_err; ?></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add Student</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
