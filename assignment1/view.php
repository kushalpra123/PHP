<?php include 'includes/header.php'; ?>
<?php include 'database.php'; ?>

<h2 class="mb-4">Student List</h2>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Grade</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT name, grade, email FROM students ORDER BY id DESC");
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['name'])."</td>
                            <td>".htmlspecialchars($row['grade'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3' class='text-center'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
