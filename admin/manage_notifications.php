<?php include 'includes/header.php';
include 'includes/db.php'; ?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2>Manage Notifications</h2>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $message = $_POST['message'];
        if (!empty($title) && !empty($message)) {
            $stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $message);
            $stmt->execute();
        }
    }

    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $conn->query("DELETE FROM notifications WHERE id = $id");
    }

    $result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
    ?>

    <form method=" POST" class="mb-3">
        <input name="title" class="form-control mb-2" placeholder="Notification Title" required>
        <textarea name="message" class="form-control" placeholder="Enter notification..." required></textarea>
        <button class="btn btn-primary mt-2" type="submit">Add Notification</button>
    </form>

    <ul class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($row['title']) ?>:</strong> <?= htmlspecialchars($row['message']) ?>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger float-end">Delete</a>
            </li>
        <?php endwhile; ?>
    </ul>
</main>
<?php include 'includes/footer.php'; ?>