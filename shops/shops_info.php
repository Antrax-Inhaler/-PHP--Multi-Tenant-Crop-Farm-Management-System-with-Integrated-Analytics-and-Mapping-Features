<?php
// vendor.php
// Include your database connection file

$vendor_id = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 0;

$sql_vendor = "SELECT * FROM vendor_list WHERE id = $vendor_id";
$result_vendor = $conn->query($sql_vendor);

$sql_products = "SELECT * FROM product_list WHERE vendor_id = $vendor_id";
$result_products = $conn->query($sql_products);

if ($result_vendor->num_rows > 0):
    $vendor = $result_vendor->fetch_assoc();
    ?>
    <div class="vendor-details">
        <h2><?= htmlspecialchars($vendor['shop_name']) ?></h2>
        <p>Contact: <?= htmlspecialchars($vendor['contact']) ?></p>
        <img src="<?= htmlspecialchars($vendor['avatar']) ?>" alt="Avatar" class="img-fluid">
        <!-- Add more vendor details here -->
    </div>

    <div class="products-list">
        <h3>Products</h3>
        <?php if ($result_products->num_rows > 0): ?>
            <ul>
                <?php while ($product = $result_products->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($product['name']) ?> - <?= htmlspecialchars($product['price']) ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No products found for this vendor.</p>
        <?php endif; ?>
    </div>
<?php
else:
    echo "Vendor not found.";
endif;

$conn->close();
?>
