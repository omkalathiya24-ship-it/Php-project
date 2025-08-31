<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = [];

if ($_POST['action'] === 'add') {
    $name = mysqli_real_escape_string($conn, $_POST['coffee_name']);
    $price = floatval($_POST['coffee_price']);
    $image = mysqli_real_escape_string($conn, $_POST['coffee_image']);
    $qty   = intval($_POST['coffee_quantity']);

    $check = mysqli_query($conn, "SELECT * FROM cart WHERE name='$name' AND user_id='$user_id'");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['quantity'] + $qty;
        mysqli_query($conn, "UPDATE cart SET quantity='$new_qty' WHERE pid='{$row['pid']}'");
        $response = ['status' => 'success', 'message' => 'Updated quantity'];
    } else {
        mysqli_query($conn, "INSERT INTO cart(user_id,name,price,image,quantity) VALUES('$user_id','$name','$price','$image','$qty')");
        $response = ['status' => 'success', 'message' => 'Added to cart'];
    }
}

if ($_POST['action'] === 'update') {
    $pid = intval($_POST['pid']);
    $qty = intval($_POST['quantity']);
    mysqli_query($conn, "UPDATE cart SET quantity='$qty' WHERE pid='$pid' AND user_id='$user_id'");
    $response = ['status' => 'success', 'message' => 'Cart updated'];
}

if ($_POST['action'] === 'delete') {
    $pid = intval($_POST['pid']);
    mysqli_query($conn, "DELETE FROM cart WHERE pid='$pid' AND user_id='$user_id'");
    $response = ['status' => 'success', 'message' => 'Item removed'];
}

if ($_POST['action'] === 'delete_all') {
    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");
    $response = ['status' => 'success', 'message' => 'Cart cleared'];
}

echo json_encode($response);
exit;
?>
