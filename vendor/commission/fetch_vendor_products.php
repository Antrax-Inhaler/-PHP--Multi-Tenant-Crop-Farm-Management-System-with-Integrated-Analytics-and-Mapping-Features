<?php
require_once('./../../config.php');

$vendor_id = $_POST['vendor_id'];
$month = $_POST['month'];

// SQL query to fetch product breakdown
$query = "
    SELECT 
        pl.name AS product_name,
        SUM(oi.quantity) AS quantity_sold,
        SUM(oi.price * oi.quantity) AS total_sales
    FROM 
        order_list ol
    JOIN 
        order_items oi ON ol.id = oi.order_id
    JOIN 
        product_list pl ON oi.product_id = pl.id
    WHERE 
        ol.vendor_id = ? 
        AND DATE_FORMAT(ol.date_created, '%Y-%m') = ? 
        AND ol.status = 4
    GROUP BY 
        pl.id
    ORDER BY 
        total_sales DESC;
";

// Prepare and execute query
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $vendor_id, $month);
$stmt->execute();
$result = $stmt->get_result();

// Prepare response data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'name' => $row['product_name'],
        'quantity_sold' => $row['quantity_sold'],
        'total_sales' => $row['total_sales']
    ];
}

// Return JSON response
echo json_encode($data);
?>
