<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
include "header.php";
include "db.php";

// Handle adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    $role = trim($_POST["role"]);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
}

// Handle editing a user
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $role = trim($_POST["role"]);

        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        $stmt->execute();
        header("Location: manage_users.php");
        exit();
    }
}

// Handle deleting a user
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}

// Fetch users from the database
$result = $conn->query("SELECT * FROM users");
?>

<style>
.manage-users-container {
    max-width: 900px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
}

.manage-users-container h2 {
    text-align: center;
    margin-bottom: 30px;
}

.manage-users-container form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 30px;
}

.manage-users-container input,
.manage-users-container select,
.manage-users-container button {
    padding: 10px 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

.manage-users-container button {
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: background 0.2s;
}

.manage-users-container button:hover {
    background-color: #0056b3;
}

.manage-users-container table {
    width: 100%;
    border-collapse: collapse;
}

.manage-users-container th,
.manage-users-container td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.manage-users-container th {
    background: #f7f7f7;
}

.manage-users-container .btn-group a {
    display: inline-block;
    padding: 6px 12px;
    font-size: 14px;
    margin-right: 5px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
}

.btn-edit {
    background-color: #ffc107;
}

.btn-delete {
    background-color: #dc3545;
}

@media screen and (max-width: 600px) {
    .manage-users-container {
        padding: 15px;
    }

    .manage-users-container th,
    .manage-users-container td {
        font-size: 14px;
    }
}
</style>

<div class="manage-users-container">
    <h2>Manage Users</h2>

    <!-- Add User Form -->
    <form method="post">
        <input type="text" name="name" required placeholder="User Name">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Password">
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>

    <!-- User List Table -->
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["id"]); ?></td>
                <td><?= htmlspecialchars($row["name"]); ?></td>
                <td><?= htmlspecialchars($row["email"]); ?></td>
                <td><?= htmlspecialchars($row["role"]); ?></td>
                <td class="btn-group">
                    <a href="manage_users.php?edit=<?= $row["id"]; ?>" class="btn-edit">Edit</a>
                    <a href="manage_users.php?delete=<?= $row["id"]; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit User Form -->
    <?php if (isset($_GET['edit'])): ?>
        <h3 style="margin-top: 40px;">Edit User</h3>
        <form method="post">
            <input type="text" name="name" required placeholder="User Name" value="<?= htmlspecialchars($user['name']); ?>">
            <input type="email" name="email" required placeholder="Email" value="<?= htmlspecialchars($user['email']); ?>">
            <select name="role" required>
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <button type="submit" name="update_user">Update User</button>
        </form>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
