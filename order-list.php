<?php 
require('layout/header.php'); 
require('layout/left-sidebar-long.php'); 
require('layout/topnav.php'); 
require('layout/left-sidebar-short.php'); 

require('../backends/connection-pdo.php');

// Fetching orders with associated food details
$sql = 'SELECT orders.order_id, orders.user_name, orders.timestamp, orders.Qty, orders.Status, food.fname 
        FROM orders 
        LEFT JOIN food ON orders.food_id = food.id';
$query = $pdoconn->prepare($sql);
$query->execute();
$arr_all = $query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = 'UPDATE orders SET Status = :status WHERE order_id = :order_id';
    $update_query = $pdoconn->prepare($update_sql);
    $update_query->execute([':status' => $new_status, ':order_id' => $order_id]);

    $_SESSION['msg'] = "Order status updated successfully!";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<div class="section white-text" style="background: #B35458;">

    <div class="section">
        <h3>Orders</h3>
    </div>

    <?php
    if (isset($_SESSION['msg'])) {
        echo '<div class="section center" style="margin: 5px 35px;">
                <div class="row" style="background: red; color: white;">
                    <div class="col s12">
                        <h6>' . $_SESSION['msg'] . '</h6>
                    </div>
                </div>
              </div>';
        unset($_SESSION['msg']);
    }
    ?>
    
    <div class="section center" style="padding: 20px;">
        <table class="centered responsive-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Food Name</th>
                    <th>Timestamp</th>
                    <th>Qty</th>
                    <th>Status</th>
                    
                </tr>
            </thead>

            <tbody>
                <?php foreach ($arr_all as $key) { ?>
                <tr>
                    <td><?php echo $key['order_id']; ?></td>
                    <td><?php echo $key['user_name']; ?></td>
                    <td><?php echo $key['fname']; ?></td>
                    <td><?php echo $key['timestamp']; ?></td>
                    <td><?php echo $key['Qty']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $key['order_id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php if ($key['Status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Complete" <?php if ($key['Status'] == 'Complete') echo 'selected'; ?>>Complete</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require('layout/about-modal.php'); ?>
<?php require('layout/footer.php'); ?>
