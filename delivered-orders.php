<?php require 'public/admin-inventory.php';?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/jpeg" href="assets/images/mang-macs-logo.jpg" sizes="70x70">
    <link rel="stylesheet" href="assets/css/main.css" type="text/css">
    <title>Delivered Orders</title>
</head>

<body>
    <div class="grid-container">
        <!--Navigation-->
        <header class="nav-container">
            <h5>Delivery Orders (<?php echo date('F d, Y')?>)</h5>
            <ul class="nav-list">
                <?php include 'assets/template/admin/navbar.php' ?>
            </ul>
        </header>
        <!--Sales' Categories-->
        <main class="main-container">
            <section>
                <article>
                    <div class="table-responsive table-container">
                        <div class="filter-date">
                            <h3>
                                <a href="dashboard.php" title="Back"><i class="fa fa-arrow-circle-left"></i></a>
                            </h3>
                            <form method="GET">
                                <label>From Date:</label>
                                <input type="date" name="startDate" value="<?php  echo $_GET['startDate']?>">&emsp;
                                <label>To Date:</label>
                                <input type="date" name="endDate" value="<?php  echo $_GET['endDate']?>">&emsp;
                                <button type="submit" class="btn btn-primary">
                                    Filter <i class="fa fa-filter" aria-hidden="true"></i>
                                </button> 
                            </form>
                        </div><br>
                        <table id="example" class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Ordered Date</th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Variation</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Add Ons</th>
                                    <th scope="col">Add Ons Price</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col">Completed Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    require 'public/connection.php';
                                    $totalAddOns = 0;
                                    $totalAmount = 0;
                                    //display specific date order completed and received deliver orders method
                                    if(isset($_GET['startDate']) && isset($_GET['endDate'])){   
                                        $orderCompleted = "Order Completed";
                                        $orderReceived = "Order Received";        
                                        $startDate = $_GET['startDate'];
                                        $endDate = $_GET['endDate'];
                                        $orderType = "Deliver";
                                        echo $connect->error;
                                        $getTotalOrder = $connect->prepare("SELECT tblorderdetails.order_number,tblorderdetails.required_date,
                                        tblcustomerorder.customer_name,tblorderdetails.product_name,tblorderdetails.product_variation,
                                        tblorderdetails.quantity,tblorderdetails.price,tblorderdetails.price * tblorderdetails.quantity as 'subtotal',
                                        tblorderdetails.add_ons,tblorderdetails.add_ons_fee * tblorderdetails.quantity as 'add_ons_fee',
                                        tblorderdetails.order_status,tblorderdetails.order_type,tblorderdetails.completed_time
                                        FROM tblorderdetails LEFT JOIN tblcustomerorder ON tblorderdetails.order_number = tblcustomerorder.order_number
                                        WHERE tblorderdetails.order_status IN (?,?) AND tblorderdetails.completed_time BETWEEN (?) AND (?) 
                                        HAVING tblorderdetails.order_type=?
                                        ORDER BY required_date ASC");
                                        echo $connect->error;
                                        $getTotalOrder->bind_param('sssss',$orderCompleted,$orderReceived,$startDate,$endDate,$orderType);
                                        $getTotalOrder->execute();
                                        $getTotalOrder->bind_result($orderNumber,$requiredDate,$customerName,$product,$variation,$quantity,$price,$subtotal,$addOns,$addOnsFee,$orderStatus,$orderType,$completedTime);
                                       
                                        if($getTotalOrder){
                                            
                                            while($getTotalOrder->fetch()){
                                               $totalAddOns += $addOnsFee;
                                               $totalAmount += $subtotal;
                                               
                                                ?>
                                                <tr>
                                                    <td><?= $orderNumber?></td>
                                                    <td><?= $requiredDate?></td>
                                                    <td><?= $customerName?></td>
                                                    <td><?= $product?></td>
                                                    <td><?= $variation?></td>
                                                    <td><?= $quantity?></td>
                                                    <td><?= $price?>.00</td>
                                                    <td><?= $addOns?></td>
                                                    <td><?= $addOnsFee?>.00</td>
                                                    <td><?= $subtotal + $addOnsFee?>.00</td>
                                                    <td><?= $completedTime?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else{
                                            echo "No Records Found";
                                        }
                                    
                                    } else{
                                        //display current date order completed and received deliver orders method
                                        $date = date('Y-m-d');
                                        $orderCompleted = "Order Completed";
                                        $orderReceived = "Order Received";
                                        $orderType = "Deliver";
                                        $getTotalOrder = $connect->prepare("SELECT tblorderdetails.order_number,tblorderdetails.required_date,
                                        tblcustomerorder.customer_name,tblorderdetails.product_name,tblorderdetails.product_variation,
                                        tblorderdetails.quantity,tblorderdetails.price,tblorderdetails.price * tblorderdetails.quantity as 'subtotal',
                                        tblorderdetails.add_ons,tblorderdetails.add_ons_fee * tblorderdetails.quantity as 'add_ons_fee',
                                        tblorderdetails.order_status,tblorderdetails.order_type,tblorderdetails.completed_time
                                        FROM tblorderdetails LEFT JOIN tblcustomerorder ON tblorderdetails.order_number = tblcustomerorder.order_number
                                        WHERE  tblorderdetails.order_status IN (?,?) AND STR_TO_DATE(tblorderdetails.completed_time,'%Y-%m-%d')=?
                                        HAVING  tblorderdetails.order_type=?
                                        ORDER BY required_date ASC");
                                        $getTotalOrder->bind_param('ssss',$orderCompleted,$orderReceived,$date,$orderType);
                                        $getTotalOrder->execute();
                                        $getTotalOrder->bind_result($orderNumber,$requiredDate,$customerName,$product,$variation,$quantity,$price,$subtotal,$addOns,$addOnsFee,$orderStatus,$orderType,$completedTime);
                                        if($getTotalOrder){
                                            while($getTotalOrder->fetch()){
                                                $totalAddOns += $addOnsFee;
                                                $totalAmount += $subtotal; 
                                                ?>
                                                <tr>
                                                    <td><?= $orderNumber?></td>
                                                    <td><?= $requiredDate?></td>
                                                    <td><?= $customerName?></td>
                                                    <td><?= $product?></td>
                                                    <td><?= $variation?></td>
                                                    <td><?= $quantity?></td>
                                                    <td><?= $price?>.00</td>
                                                    <td><?= $addOns?></td>
                                                    <td><?= $addOnsFee?>.00</td>
                                                    <td><?= $subtotal + $addOnsFee?>.00</td>
                                                    <td><?= $completedTime?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else{
                                            echo "No Records Found";
                                        }
                                    
                                    }
                                 ?>
                            </tbody>

                          <tfoot>
                              <tr>
                                    <td colspan="8"></td>
                                    <td><b>Total Sales:  </b></td>
                                    <td><b>₱ <?= $totalAddOns +  $totalAmount?>.00</b></td>
                                    <td></td>
                              </tr>
                          </tfoot>

                        </table>
                    </div>
                </article>
            </section>
        </main>
        <!--Sidebar-->
        <?php include 'assets/template/admin/sidebar.php'?>
    </div>
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/sidebar-menu-active.js"></script>
    <script src="assets/js/table.js"></script>
</body>

</html>