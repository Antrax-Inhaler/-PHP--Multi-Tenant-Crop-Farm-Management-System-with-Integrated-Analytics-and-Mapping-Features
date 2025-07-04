<style>
  #cover-image{
    width:calc(100%);
    height:50vh;
    object-fit:cover;
    object-position:center center;
  }
</style>
<hr>

<?php 
// Fetch current vendor ID
$vendor_id = $_settings->userdata('id');

// Fetch the vendor details
$vendor_query = $conn->query("SELECT * FROM vendor_list WHERE id = '{$vendor_id}' AND delete_flag = 0");
$vendor = $vendor_query->fetch_assoc();

// Fetch commission data for the current vendor
$commission_query = $conn->query("SELECT * FROM vendor_commissions WHERE vendor_id = '{$vendor_id}'");
?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?php echo $vendor['shop_name']; ?> - Monthly Commissions</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Total Sales</th>
              <th>Total Commission</th>
              <th>Month</th>
              <th>Paid</th>
              <th>Products</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $commission_query->fetch_assoc()): ?>
              <?php
                $total_sales = $row['total_sales'];
                $total_commission = $row['total_commission'];
                $month = $row['month'];
                $paid = $row['paid'];
              ?>
              <tr>
                <td><?php echo number_format($total_sales, 2); ?></td>
                <td><?php echo number_format($total_commission, 2); ?></td>
                <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                <td><?php echo $paid ? 'Yes' : 'No'; ?></td>
                <td>
                  <a href="javascript:void(0)" 
                     class="view-products" 
                     data-vendor-id="<?php echo $vendor_id; ?>" 
                     data-month="<?php echo $month; ?>">
                    View Products
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Product List Modal -->
<div class="modal fade" id="productListModal" tabindex="-1" role="dialog" aria-labelledby="productListModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productListModalLabel">Product List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Quantity Sold</th>
              <th>Total Sales</th>
            </tr>
          </thead>
          <tbody id="productListContent">
            <!-- Data will be loaded dynamically -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).on("click", ".view-products", function () {
  const vendorId = $(this).data("vendor-id");
  const month = $(this).data("month");

  // Clear previous data
  $("#productListContent").html("");

  // Fetch product data
  $.ajax({
    url: "commission/fetch_vendor_products.php", // Backend script to fetch products
    method: "POST",
    data: { vendor_id: vendorId, month: month },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.length > 0) {
        data.forEach(item => {
          $("#productListContent").append(`
            <tr>
              <td>${item.name}</td>
              <td>${item.quantity_sold}</td>
              <td>${item.total_sales.toFixed(2)}</td>
            </tr>
          `);
        });
      } else {
        $("#productListContent").html("<tr><td colspan='3'>No products sold for this month.</td></tr>");
      }
      $("#productListModal").modal("show");
    },
    error: function () {
      alert("Failed to fetch product data.");
    }
  });
});

</script>
<script>
function printReceipt() {
  const receiptContent = document.getElementById('receiptContent').innerHTML;
  const newWindow = window.open('', '_blank');
  newWindow.document.write(receiptContent);
  newWindow.document.close();
  newWindow.print();
}

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('print-receipt')) {
    const vendor = e.target.dataset.vendor;
    const month = e.target.dataset.month;
    const sales = e.target.dataset.sales;
    const commission = e.target.dataset.commission;

    const invoiceNumber = `INV-${Math.floor(Math.random() * 1000000)}`;

    const receiptHtml = `
      <div style="text-align: center; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Naujan Farmers Association Ecommerce System</h2>
        <h4>Commission Receipt</h4>
        <p>Invoice Number: <strong>${invoiceNumber}</strong></p>
        <p>Vendor: <strong>${vendor}</strong></p>
        <p>Month: <strong>${month}</strong></p>
        <p>Total Sales: <strong>₱${parseFloat(sales).toFixed(2)}</strong></p>
        <p>Total Commission: <strong>₱${parseFloat(commission).toFixed(2)}</strong></p>
        <hr>
        <p>Thank you for being a part of our community!</p>
      </div>
    `;

    document.getElementById('receiptContent').innerHTML = receiptHtml;
    $('#receiptModal').modal('show');
  }
});
</script>
