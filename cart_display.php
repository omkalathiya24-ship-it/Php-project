<?php
include("connect.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$user_id = $_SESSION['user_id'];
$grand_total = 0;
$cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");

echo '<table>
<thead>
<tr>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Quantity</th>
<th>Total Price</th>
<th>Action</th>
</tr>
</thead>
<tbody>';

if (mysqli_num_rows($cart_query) > 0) {
    while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
        $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
        $grand_total += $sub_total;

        echo '<tr>
            <td><img src="images/'.$fetch_cart['image'].'" class="coffee-image" width="50"></td>
            <td>'.$fetch_cart['name'].'</td>
            <td>₹'.number_format($fetch_cart['price'], 2).'</td>
            <td>
                <input type="number" min="1" value="'.$fetch_cart['quantity'].'" onchange="updateCart('.$fetch_cart['pid'].', this.value)">
            </td>
            <td>₹'.number_format($sub_total, 2).'</td>
            <td><button onclick="removeFromCart('.$fetch_cart['pid'].')" class="delete-btn">Remove</button></td>
        </tr>';
    }
} else {
    echo '<tr><td colspan="6">No item added</td></tr>';
}

echo '<tr class="table-bottom">
    <td colspan="4" style="text-align:right;"><strong>Grand Total:</strong></td>
    <td><strong>₹'.number_format($grand_total, 2).'</strong></td>
    <td>
        <button onclick="clearCart()" class="delete-btn '.($grand_total > 0 ? '' : 'disabled').'">Delete All</button>
    </td>
</tr>';

echo '</tbody></table>';

echo '<div class="cart-btn" style="margin-top:20px;">
    <a href="#" class="btn '.($grand_total > 0 ? '' : 'disabled').'">Proceed to Checkout</a>
</div>';
?>
