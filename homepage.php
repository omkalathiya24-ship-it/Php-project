<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header('location:homepage.php'); 
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <style>
        .message { background: #f2f2f2; padding: 10px; margin: 10px 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        .delete-btn, .option-btn, .btn { padding: 5px 10px; background: red; color: #fff; text-decoration: none; border: none; cursor: pointer; }
        .disabled { pointer-events: none; opacity: 0.5; }
        html { scroll-behavior: smooth; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<header>
    <div class="logo">The Lexii</div>
    <div class="nav-links">
        <a href="#menu">Menu</a>
        <a href="#cart">Cart</a>
        <a href="logout.php">Logout</a>
    </div>
    <div style="text-align:left; padding:15px;">
        <p style="font-size:15px; font-weight:bold;">
            Hello  
            <?php 
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
                $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
                if ($row = mysqli_fetch_assoc($query)) {
                    echo $row['firstName'] . ' ' . $row['lastName'];
                }
            }
            ?>
        </p>
    </div>
</header>

<h1 class="heading" id="menu">Available Coffee</h1>

<div class="product-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
<?php
$items_per_page = 4;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1);
$offset = ($page - 1) * $items_per_page;

$total_items_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM coffee");
$total_items = mysqli_fetch_assoc($total_items_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

$coffee_query = mysqli_query($conn, "SELECT * FROM coffee LIMIT $offset, $items_per_page");

if (mysqli_num_rows($coffee_query) > 0) {
    while ($coffee = mysqli_fetch_assoc($coffee_query)) {
?>
    <form onsubmit="return addToCart(this);" style="border: 1px solid #ccc; padding: 15px; width: 200px;">
        <img src="images/<?php echo $coffee['image']; ?>" alt="" width="100%">
        <h3><?php echo $coffee['name']; ?></h3>
        <p>Price: ₹<?php echo number_format($coffee['price'], 2); ?></p>
        <input type="hidden" name="coffee_name" value="<?php echo $coffee['name']; ?>">
        <input type="hidden" name="coffee_price" value="<?php echo $coffee['price']; ?>">
        <input type="hidden" name="coffee_image" value="<?php echo $coffee['image']; ?>">
        <input type="number" name="coffee_quantity" min="1" value="1">
        <input type="submit" value="Add to Cart" class="btn">
    </form>
<?php
    }
} else {
    echo "<p>No coffee items found.</p>";
}
?>
</div>

<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($page > 1): ?>
        <a href="homepage.php?page=<?php echo $page - 1; ?>" class="btn">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="homepage.php?page=<?php echo $i; ?>" class="btn <?php echo ($i == $page) ? 'disabled' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="homepage.php?page=<?php echo $page + 1; ?>" class="btn">Next</a>
    <?php endif; ?>
</div>

<div class="shopping-cart" id="cart">
    <h1 class="heading">Cart</h1>
    <div id="cart-container">
        <?php include "cart_display.php"; ?>
    </div>
</div>

<script>
function addToCart(form) {
    let formData = new FormData(form);
    formData.append('action', 'add');

    fetch("cart_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadCart();
    });
    return false; // prevent reload
}

function updateCart(pid, qty) {
    let formData = new FormData();
    formData.append('action', 'update');
    formData.append('pid', pid);
    formData.append('quantity', qty);

    fetch("cart_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadCart();
    });
}

function removeFromCart(pid) {
    let formData = new FormData();
    formData.append('action', 'delete');
    formData.append('pid', pid);

    fetch("cart_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadCart();
    });
}

function clearCart() {
    let formData = new FormData();
    formData.append('action', 'delete_all');

    fetch("cart_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadCart();
    });
}

function loadCart() {
    fetch("cart_display.php")
    .then(res => res.text())
    .then(html => {
        document.getElementById("cart-container").innerHTML = html;
    });
}
</script>

</body>
</html>
