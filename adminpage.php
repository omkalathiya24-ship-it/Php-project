<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header('location:homepage.php');
    exit;
}

if (isset($_POST['add_coffee'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    $target = "images/" . basename($image);
    move_uploaded_file($image_tmp, $target);

    $insert = mysqli_query($conn, "INSERT INTO coffee (name, price, image) VALUES ('$name', '$price', '$image')");
    $message[] = $insert ? 'Coffee added successfully!' : 'Failed to add coffee.';
}

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM coffee WHERE id = '$delete_id'");
    header('location:adminpage.php');
    exit;
}

if (isset($_POST['update_coffee'])) {
    $id = intval($_POST['coffee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $price = floatval($_POST['update_price']);

    if (!empty($_FILES['update_image']['name'])) {
        $image = $_FILES['update_image']['name'];
        $image_tmp = $_FILES['update_image']['tmp_name'];
        $target = "images/" . basename($image);
        move_uploaded_file($image_tmp, $target);
        mysqli_query($conn, "UPDATE coffee SET name='$name', price='$price', image='$image' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE coffee SET name='$name', price='$price' WHERE id='$id'");
    }

    $message[] = 'Coffee updated successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .form-container, .coffee-table {
            margin: 20px auto;
            max-width: 800px;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"] {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
        }

        .message {
            background: #f2f2f2;
            padding: 10px;
            margin: 10px;
            color: #333;
        }

        .btn, .option-btn, .delete-btn {
            padding: 5px 10px;
            color: #fff;
            background-color: #555;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }

        .btn:hover, .option-btn:hover, .delete-btn:hover {
            opacity: 0.9;
        }

        .delete-btn {
            background: red;
        }

        .option-btn {
            background: orange;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        img {
            max-height: 80px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">The Lexii - Admin</div>
    <div class="nav-links">
        <a href="homepage.php">User Homepage</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '<div class="message">' . $msg . '</div>';
    }
}
?>

<div class="form-container">
    <h2>Add New Coffee</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Coffee Name" required>
        <input type="number" name="price" step="0.01" placeholder="Coffee Price" required>
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" name="add_coffee" value="Add Coffee" class="btn">
    </form>
</div>

<div class="coffee-table">
    <h2>Manage Coffee</h2>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
$coffee_result = mysqli_query($conn, "SELECT * FROM coffee");

if (mysqli_num_rows($coffee_result) > 0) {
    while ($row = mysqli_fetch_assoc($coffee_result)) {
?>
        <tr>
            <td><img src="images/<?php echo $row['image']; ?>" alt=""></td>
            <td><?php echo $row['name']; ?></td>
            <td>₹<?php echo number_format($row['price'], 2); ?></td>
            <td>
                <form method="post" enctype="multipart/form-data" style="display:inline;">
                    <input type="hidden" name="coffee_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="update_name" value="<?php echo $row['name']; ?>" required>
                    <input type="number" name="update_price" step="0.01" value="<?php echo $row['price']; ?>" required>
                    <input type="file" name="update_image" accept="image/*">
                    <input type="submit" name="update_coffee" value="Update" class="option-btn">
                </form>
                <a href="adminpage.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this coffee?');">Delete</a>
            </td>
        </tr>
<?php
    }
} else {
    echo '<tr><td colspan="4">No coffee items found.</td></tr>';
}
?>
        </tbody>
    </table>
</div>

</body>
</html>
