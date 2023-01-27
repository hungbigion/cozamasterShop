<!DOCTYPE html>
<html lang="en">
<?php
    include ('db-connect.php');
    include ('database.php');
    session_start();
    $account = (isset($_SESSION['account'])) ? $_SESSION['account']: [];
    $getdata = new Database ();
    $select_pd = $getdata ->select_pd();
	$select_pd_st1 = $getdata ->select_pd_st1();
	$select_pd_st2 = $getdata ->select_pd_st2();
	$select_pd_st3 = $getdata ->select_pd_st3();
    if(isset($account['IDacc'])) {
    $quantity_cart = $getdata ->quantity_of_cart($account['IDacc']);
    $select_cart= $getdata ->select_cart($account['IDacc']); 
	$select_total = $getdata ->select_total($account['IDacc']); 
    }
    
	// check out
	if (isset($_POST['checkout'])) {
		$idcart = $account['idcart'];
		$idacc = $account['IDacc'];
		$fullname = $account['fullname'];
		$phonenum = $account['phonenum'];
		$address = $account['address'];
		$dateout = date('Y-m-j');
		$detailbill = $_POST['detailbill'];
		$billtotal = $_POST['billtotal'];
		$checkout = mysqli_query($conn, "INSERT INTO `bill`
		(`idcart`, `IDacc`, `fullname`, `phonenum`, `address`, `dateout`, `detailbill`, `billtotal`)
		 VALUES ('$idcart','$idacc','$fullname','$phonenum','$address','$dateout','$detailbill','$billtotal')") or die('query failed');
	if ($checkout) {
		foreach ($select_cart as $cart){
			$pdquantity = $cart['pdquantity'];
			$qtcart = $cart['quantitycart'];
			$number = (integer)$pdquantity - (integer)$qtcart;
			$idproduct = $cart['idproduct'];
			mysqli_query($conn,"UPDATE `product` SET `pdquantity`= '$number' WHERE idproduct = '$idproduct'");
		}
		$delete =  mysqli_query($conn,"DELETE FROM `cartdetail` WHERE IDacc = '$idacc'");
		if ($delete) {
			$updatetotal =  mysqli_query($conn,"UPDATE `cart` SET `total`= 0 WHERE IDacc = '$idacc'");
            if ($updatetotal) {
            echo "<script>
			alert('Check Out success!Let's keep buying');
			</script>"; 
            header('location: myaccount.php');
            }
		}
	}
	}
	// delete product from cart
	if(isset($_GET['deletecartdetail'])) {
		$idcart = $account['idcart'];
        $idacc = $account['IDacc'];
		$delete_id = $_GET['deletecartdetail'];
        $delete_query = mysqli_query($conn, "DELETE FROM `cartdetail` WHERE idcartdetail = '$delete_id'") or die('query failed');
        if($delete_query){
			 $updatecart_query = mysqli_query($conn, 
                "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.IDacc = '$idacc')
                WHERE cart.IDacc = '$idacc'");
				header('location: shoping-cart.php');
        };
	}
	//update cart quantity
	if (isset($_POST['updatecart'])) {
         $idacc = $account['IDacc'];
		$num_product = $_POST['num-product'];
		$idcartdetail = $_POST['idcartdetail'];
		$idproduct = $_POST['idproduct'];
		$idcart = $account['idcart'];
		$update_query = mysqli_query($conn, "UPDATE `cartdetail` SET `quantitycart`='$num_product',
                        `totalpd` =  '$num_product' * (SELECT cartdetail.price FROM cartdetail WHERE cartdetail.idcartdetail = '$idcartdetail')
                        WHERE cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = '$idcart'");
		if ($update_query) {
			 $updatecart_query = mysqli_query($conn, 
                "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.IDacc = '$idacc')
                WHERE cart.IDacc = '$idacc'");
				header('location: shoping-cart.php');
		}
       
     
	}
    // addproduct to cart
            if (isset($_POST['addtcart'])){
            $idcart = $account['idcart'];
            $idacc = $account['IDacc'];
            $idproduct = $_POST['idproduct'];
            $namepd = $_POST['namepd'];
            $num_product = $_POST['num-product'];
            $price = $_POST['price'];
            $totalpd = (int)$num_product * (int)$price;
            $check_query =mysqli_query($conn,"SELECT * from cartdetail WHERE cartdetail.idproduct = '$idproduct'") ;
            if (mysqli_num_rows($check_query) > 0 ) {
                $update_query = mysqli_query($conn, "UPDATE `cartdetail` SET `quantitycart`=(
                        SELECT cartdetail.quantitycart FROM cartdetail WHERE 
                        cartdetail.idproduct = '$idproduct') + '$num_product',
                        `totalpd` = (
                        (SELECT cartdetail.quantitycart FROM cartdetail WHERE 
                        cartdetail.idproduct = '$idproduct')+ '$num_product') * (SELECT cartdetail.price FROM cartdetail WHERE cartdetail.idproduct = '$idproduct')
                        WHERE cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = '$idcart'");
                $updatecart_query = mysqli_query($conn, 
                "UPDATE `cart` SET `total` = (SELECT cart.total FROM cart WHERE cart.idcart = '$idcart') + 
                        (((SELECT cartdetail.quantitycart FROM cartdetail WHERE cartdetail.idproduct = '$idproduct')+('$num_product'))*(SELECT cartdetail.price FROM cartdetail WHERE cartdetail.idproduct =  '$idproduct'))
                        - (SELECT cartdetail.totalpd  FROM cartdetail WHERE cartdetail.idproduct = '$idproduct')
                        WHERE cart.idcart = '$idcart'");
                 echo "<script>
                            history.back();
                            </script>";
            }
            else {
                $insert_query = mysqli_query($conn, "INSERT INTO 
                `cartdetail`( `idcart`, `IDacc`, `idproduct`, `namepd`, `quantitycart`, `price`, `totalpd`) 
                VALUES ('$idcart','$idacc','$idproduct','$namepd','$num_product','$price',$totalpd)");
                $updatecart_query = mysqli_query($conn, "UPDATE `cart` SET 
                `total` = (SELECT cart.total FROM cart WHERE cart.idcart = '$idcart') + $totalpd
                            WHERE cart.idcart = '$idcart'");
                 echo "<script>
                            history.back();
                            </script>";
            }
        } 
    // sigin
    if (isset($_POST['signin'])) {
        $username = $_POST['uname'];
        $password = $_POST['psw'];
        $fullname = $_POST['fname'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $per = 0;
        $sql = "INSERT INTO account (username, password, fullname, email, phonenum, address,per) 
        VALUES('$username', '$password', '$fullname', '$email', '$phone', '$address', '$per')";
        $query = mysqli_query($conn,$sql);
        // header('location: home-02.php'); 
        echo 'Đăng ký tài khoản thành công';
        if ($query) {
        $sql1 = "INSERT INTO cart (IDacc, total) 
        VALUES((SELECT account.IDacc FROM account WHERE account.email = '$email' ), 0)";
        $query1 = mysqli_query($conn,$sql1); 
        header( "refresh:2;url=home-02.php" );
        }
    }
    // login
     if (isset($_POST['login'])){

        $email = $_POST['uname'];
        $password = $_POST['psw'];

        $sql = "SELECT * FROM account WHERE email='$email' AND password='$password'";
        $query = mysqli_query($conn,$sql);
        $data = mysqli_fetch_assoc($query);
        $check = mysqli_num_rows($query);
        if ($check==1) {
            $_SESSION['account'] = $data;
            if($data['per'] == 1) {
                  header('location: ../../darkpan-1.0.0/index.php');
            }
            else if ($data['per'] == 0) {
                header('location: home-02.php');
            }
            else if ($data['per'] == 2) {
                echo "<script>alert('tài khoản đã bị khóa ');</script>";
                unset($_SESSION['acount']);
            }
        }
        else {
            echo "<script>alert('TT tài khoản không đúng ');</script>";
        }
    }
    // login
    
?>

<head>
    <title>Shoping Cart</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="images/icons/favicon.png" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>

<body class="animsition">

    <!-- Header -->
    <header class="header-v2">
        <!-- Header desktop -->
        <div class="container-menu-desktop trans-03">
            <div class="wrap-menu-desktop">
                <nav class="limiter-menu-desktop p-l-45">

                    <!-- Logo desktop -->
                    <a href="#" class="logo">
                        <img src="images/icons/logo-01.png" alt="IMG-LOGO">
                    </a>

                    <!-- Menu desktop -->
                    <div class="menu-desktop">
                        <ul class="main-menu">
                            <li>
                                <a href="home-02.php">Home</a>
                            </li>
                            <li class="active-menu">
                                <a href="product.php">Shop</a>
                            </li>

                            <li>
                                <a href="about.php">About</a>
                            </li>

                            <li>
                                <a href="contact.php">Contact</a>
                            </li>
                            <?php if (isset($account['fullname'])) {?>
                            <li class="active-menu">
                                <a> <?php echo $account['fullname'] ?></a>
                                <ul class="sub-menu">
                                    <li><a href="myaccount.php">My account</a></li>
                                    <li><a href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                            <?php } else {?>
                            <li>
                                <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <form class="modal-dialog" method="post">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                                <h4 class="modal-title w-100 font-weight-bold">Login</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body mx-3">
                                                <div class="md-form mb-5">
                                                    <i class="fas fa-envelope"></i>
                                                    <input type="email" id="defaultForm-email" name="uname" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="defaultForm-email">Your email</label>
                                                </div>

                                                <div class="md-form mb-4">
                                                    <i class="fas fa-lock"></i>
                                                    <input type="password" id="defaultForm-pass" name="psw" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="defaultForm-pass">Your password</label>
                                                </div>

                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <!-- <button class="btn btn-default">Login</button> -->
                                                <input type="submit" value="Login" class="btn btn-default" name="login"
                                                    style="cursor: pointer;">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <a href="" data-toggle="modal" data-target="#modalLoginForm">
                                    Login
                                </a>
                            </li>
                            <!-- ============================== ======= -->
                            <li>
                                <div class="modal fade" id="modalRegisterForm" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <form method="post" class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                                <h4 class="modal-title w-100 font-weight-bold">Sign up</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body mx-3">
                                                <div class="md-form mb-5">
                                                    <i class="fas fa-user prefix grey-text"></i>
                                                    <input type="text" id="orangeForm-name" name="uname" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-name">Username</label>
                                                </div>
                                                <div class="md-form mb-4">
                                                    <i class="fas fa-lock prefix grey-text"></i>
                                                    <input type="password" id="orangeForm-pass" name="psw" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-pass">Your password</label>
                                                </div>
                                                <div class="md-form mb-5">
                                                    <i class="fas fa-user-edit"></i>
                                                    <input type="text" id="orangeForm-name" name="fname" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-name">Fullname</label>
                                                </div>
                                                <div class="md-form mb-5">
                                                    <i class="fas fa-phone-alt"></i>
                                                    <input type="text" id="orangeForm-name" name="phone" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-name">Phone</label>
                                                </div>
                                                <div class="md-form mb-5">
                                                    <i class="fas fa-envelope prefix grey-text"></i>
                                                    <input type="email" id="orangeForm-email" name="email" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-email">Your email</label>
                                                </div>

                                                <div class="md-form mb-5">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <input type="text" id="orangeForm-name" name="address" required
                                                        class="form-control validate">
                                                    <label data-error="wrong" data-success="right"
                                                        for="orangeForm-name">Address</label>
                                                </div>


                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <input type="submit" class="btn btn-deep-orange" value="Sign in"
                                                    style="cursor: pointer;" name="signin">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <a href="" data-toggle="modal" data-target="#modalRegisterForm">
                                    Register
                                </a>

                            </li>
                            <?php }?>
                        </ul>
                    </div>
                    <?php if (isset($account['fullname'])) {?>
                    <!-- Icon header -->
                    <div class="wrap-icon-header flex-w flex-r-m h-full">
                        <div class="flex-c-m h-full p-l-18 p-r-25 bor5">
                            <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 icon-header-noti js-show-cart"
                                data-notify="<?php foreach ($quantity_cart as $se ) {echo $se['slcart'];}?>">
                                <i class=" zmdi zmdi-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <?php } else {?>
                    <?php }?>
                </nav>
            </div>
        </div>

        <!-- Header Mobile -->
        <div class="wrap-header-mobile">
            <!-- Logo moblie -->
            <div class="logo-mobile">
                <a href="home-02.php"><img src="images/icons/logo-01.png" alt="IMG-LOGO"></a>
            </div>
            <?php if (isset($account['fullname'])) {?>
            <!-- Icon header -->
            <div class="wrap-icon-header flex-w flex-r-m h-full m-r-15">
                <div class="flex-c-m h-full p-lr-10 bor5">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 icon-header-noti js-show-cart"
                        data-notify="<?php foreach ($quantity_cart as $se ) {echo $se['slcart'];}?>">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <?php } else {?>
            <?php }?>


            <!-- Button show menu -->
            <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </div>
        </div>


        <!-- Menu Mobile -->
        <div class="menu-mobile">
            <ul class="main-menu-m">
                <li>
                    <a href="home-02.php">Home</a>
                    <span class="arrow-main-menu-m">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </span>
                </li>

                <li>
                    <a href="product.php">Shop</a>
                </li>

                <li>
                    <a href="shoping-cart.php" class="label1 rs1" data-label1="hot">Features</a>
                </li>

                <li>
                    <a href="blog.html">Blog</a>
                </li>

                <li>
                    <a href="about.html">About</a>
                </li>

                <li>
                    <a href="contact.php">Contact</a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Cart -->
    <div class="wrap-header-cart js-panel-cart">
        <div class="s-full js-hide-cart"></div>

        <div class="header-cart flex-col-l p-l-65 p-r-25">
            <div class="header-cart-title flex-w flex-sb-m p-b-8">
                <span class="mtext-103 cl2">
                    Your Cart
                </span>

                <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                    <i class="zmdi zmdi-close"></i>
                </div>
            </div>

            <div class="header-cart-content flex-w js-pscroll">
                <?php
                foreach ($select_cart as $se ) {
                ?>
                <ul class="header-cart-wrapitem w-full">
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-img">
                            <img src="../../darkpan-1.0.0/<?php echo $se['image']; ?>" alt="IMG">
                        </div>

                        <div class="header-cart-item-txt p-t-8">
                            <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
                                <?php echo $se['namepd'] ?>
                            </a>

                            <span class="header-cart-item-info">
                                <?php echo $se['quantitycart'] ?> x <?php echo $se['price'] ?> VND
                            </span>
                        </div>
                    </li>
                </ul>

                <?php }?>
                <div class="w-full">

                    <div class="w-full">
                        <div class="header-cart-total w-full p-tb-40">
                            Total:
                            <?php foreach ($select_total as $se ) {echo $se['total'];}?> VND
                        </div>
                    </div>
                    <div class="header-cart-buttons flex-w w-full">
                        <a href="shoping-cart.php"
                            class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                            View Cart
                        </a>

                        <a href="shoping-cart.php"
                            class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                            Check Out
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <?php if(isset($account['IDacc'])) {?>

    <!-- Shoping Cart -->
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-xl-12 m-lr-auto m-b-50">
                <div class="m-l-25 m-r--38 m-lr-0-xl">
                    <div class="wrap-table-shopping-cart">

                        <table class="table-shopping-cart">
                            <tr class="table_head">
                                <th class="column-1">Product</th>
                                <th class="column-2"></th>
                                <th class="column-3">Price</th>
                                <th class="column-4"></th>
                                <th class="column-5">Quantity</th>
                                <th class="column-6">Total</th>
                                <th class="column-7"></th>
                                <th class="column-8"></th>
                                <th class="column-9"></th>
                            </tr>
                            <?php
									foreach ($select_cart as $se ) {
							?>
                            <form method="post">
                                <tr class="table_row">
                                    <td class="column-1">
                                        <div class="how-itemcart1">
                                            <img src="../../darkpan-1.0.0/<?php echo $se['image']; ?>" alt="IMG">
                                        </div>
                                    </td>
                                    <td class="column-2"><?php echo $se['namepd'] ?></td>
                                    <td colspan="2" class="column-3"><?php echo $se['price'] ?> VND</td>
                                    <td class="column-5">
                                        <div style="display:none;" class="md-form">
                                            <span class="mtext-106 cl2">
                                                ID:
                                            </span>
                                            <input name="idcartdetail" value="<?php echo $se['idcartdetail']; ?>"
                                                type="text" class="form-control">
                                        </div>
                                        <div style="display:none;" class="md-form">
                                            <span class="mtext-106 cl2">
                                                ID:
                                            </span>
                                            <input name="idproduct" value="<?php echo $se['idproduct']; ?>" type="text"
                                                class="form-control">
                                        </div>
                                        <div style="display:none;" class="md-form">
                                            <input name="pdquantity" value="<?php echo $se['pdquantity']; ?>"
                                                type="text" id="stock" class="form-control">
                                        </div>
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div id="productdown"
                                                class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>
                                            <input id="numpd" class="mtext-104 cl3 txt-center num-product" type="number"
                                                name="num-product"
                                                value="<?php if ( $se['pdquantity'] < $se['quantitycart']) {echo $se['pdquantity'];}else { echo $se['quantitycart'];}?>">
                                            <div id="productup"
                                                class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class=" fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2" class="column-6"><?php echo $se['totalpd'] ?>VND</td>
                                    <td class=" column-8">

                                        <input value="Update" name="updatecart" type="submit"
                                            class="btn btn-default hov-btn3 p-lr-15 trans-04 pointer">
                                        <?php if ( $se['pdquantity'] < $se['quantitycart'] && $se['pdquantity'] != 0  ) {echo '<p style="color: #f44336;">Hit the update button </p>';
                                        }else {
                                        }?>
                                    </td>
                                    <td class="column-9">
                                        <?php if ( $se['pdquantity'] < $se['quantitycart'] && $se['pdquantity'] == 0  ) {echo '<p style="color: #f44336;">Hit the Delete button </p>';
                                        }else {
                                        }?>
                                        <a onclick="return confirm('are your sure you want to delete product from your cart?');"
                                            class="btn btn-sm btn-primary"
                                            href="shoping-cart.php?deletecartdetail=<?php echo $se['idcartdetail']?>"
                                            class="btn btn-default hov-btn3 p-lr-15 trans-04 pointer">Delete</a>
                                    </td>
                                </tr>
                            </form>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

            <form method="post" class="col-sm-10 col-lg-10 col-xl-9 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                    <h4 class="mtext-109 cl2 p-b-30">
                        Cart
                    </h4>

                    <div class="flex-w flex-t bor12 p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">
                                Subtotal:
                            </span>
                        </div>

                        <div class="size-209">
                            <span class="mtext-110 cl2">
                                <?php foreach ($select_total as $se ) {echo $se['total'];}?> VND
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                        <div class="size-208 w-full-ssm">
                            <span class="stext-110 cl2">
                                Receiver's information :
                            </span>
                        </div>
                        <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                            <div class="p-t-15">
                                <span class="stext-110 cl2">
                                    Name :
                                </span>
                                <div class="bor8 bg0 m-b-12">
                                    <span class="stext-110 cl2">
                                        <?php echo $account['fullname']; ?>
                                    </span>
                                    <input name="fullname" style="display: none;" type="text"
                                        value="<?php echo $account['fullname']; ?>"
                                        class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="state">
                                </div>
                            </div>
                            <div class="p-t-15">
                                <span class="stext-110 cl2">
                                    Phone :
                                </span>
                                <div class="bor8 bg0 m-b-12">
                                    <span class="stext-110 cl2">
                                        <?php echo $account['phonenum']; ?>
                                    </span>
                                    <input name="phonenum" style="display: none;" type="text"
                                        value="<?php echo $account['phonenum']; ?>"
                                        class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="state"
                                        placeholder="0879332174">
                                </div>
                            </div>
                            <div class="p-t-15">
                                <span class="stext-110 cl2">
                                    Address :
                                </span>
                                <div class="form-group">
                                    <span class="stext-110 cl2">
                                        <?php echo $account['address']; ?>
                                    </span>
                                    <textarea name="address" style="display: none;" class="form-control"
                                        id="exampleFormControlTextarea3" rows="7"><?php echo $account['address']; ?>
									</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                        <div class="size-208 w-full-ssm">
                            <span class="stext-110 cl2">
                                Order details :
                            </span>
                        </div>
                        <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                            <div class="p-t-15">
                                <div class="form-group">
                                    <textarea name="detailbill" style="display: none;" class="form-control"
                                        id="exampleFormControlTextarea3"
                                        rows="7"> <?php foreach ($select_cart as $se ) { echo "Name: ".$se['namepd']."  QT: ".$se['quantitycart']."  TT: ".$se['totalpd']." VND    "; } ?></textarea>
                                    <span class="stext-110 cl2">
                                        <?php foreach ($select_cart as $se ) { echo "-  Name:&nbsp; ".$se['namepd']." &nbsp;  QT:&nbsp; ".$se['quantitycart']." &nbsp;  TT:&nbsp; ".$se['totalpd']."&nbsp;VND. <br>   "; } ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-t-27 p-b-33">
                        <div class="size-208">
                            <span class="mtext-101 cl2">
                                Total:
                            </span>
                        </div>
                        <div class="size-209 p-t-1">
                            <span class="mtext-110 cl2">
                                <?php foreach ($select_total as $se ) {echo $se['total'];}?> VND
                            </span>
                            <input style="display: none;" value="<?php echo date('Y-m-j'); ?>" type="date"
                                name="dateout" class="mtext-110 cl2">
                            <input style="display: none;"
                                value="<?php foreach ($select_total as $se ) {echo $se['total'];}?>" type="text"
                                name="billtotal" class="mtext-110 cl2">
                        </div>
                    </div>
                    <?php
                    $numbig = 0;
                    $numsmall = 0;
                    foreach ($select_cart as $se ) {
                    if ( $se['pdquantity'] < $se['quantitycart']) {
                       $numbig = $numbig + 1; 
                    }else {
                        $numsmall = $numsmall + 1;
                    }
                    }
                    if ($numbig > 0) {
                    echo '<p style="color: #f44336;">Quantity is not enough to fulfill the order!!!</p>';
                    }
                    else if ($numbig == 0) {?>
                    <input id="check" name="checkout" type="submit" value="Proceed to Checkout"
                        class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                    <?php } ?>
                </div>
            </form>

        </div>
    </div>
    <?php } else {?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>You are not logged in! </strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php }?>





    <!-- Footer -->
    <footer class="bg3 p-t-75 p-b-32">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3 p-b-50">
                    <h4 class="stext-301 cl0 p-b-30">
                        Categories
                    </h4>

                    <ul>
                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Women
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Men
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Shoes
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Watches
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3 p-b-50">
                    <h4 class="stext-301 cl0 p-b-30">
                        Help
                    </h4>

                    <ul>
                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Track Order
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Returns
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                Shipping
                            </a>
                        </li>

                        <li class="p-b-10">
                            <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                FAQs
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3 p-b-50">
                    <h4 class="stext-301 cl0 p-b-30">
                        GET IN TOUCH
                    </h4>

                    <p class="stext-107 cl7 size-201">
                        Any questions? Let us know in store at 8th floor, 379 Hudson St, New York, NY 10018 or call
                        us
                        on (+1) 96 716 6879
                    </p>

                    <div class="p-t-27">
                        <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                            <i class="fa fa-facebook"></i>
                        </a>

                        <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                            <i class="fa fa-instagram"></i>
                        </a>

                        <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                            <i class="fa fa-pinterest-p"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 p-b-50">
                    <h4 class="stext-301 cl0 p-b-30">
                        Newsletter
                    </h4>

                    <form>
                        <div class="wrap-input1 w-full p-b-4">
                            <input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email"
                                placeholder="email@example.com">
                            <div class="focus-input1 trans-04"></div>
                        </div>

                        <div class="p-t-18">
                            <button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                                Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-t-40">
                <div class="flex-c-m flex-w p-b-18">
                    <a href="#" class="m-all-1">
                        <img src="images/icons/icon-pay-01.png" alt="ICON-PAY">
                    </a>

                    <a href="#" class="m-all-1">
                        <img src="images/icons/icon-pay-02.png" alt="ICON-PAY">
                    </a>

                    <a href="#" class="m-all-1">
                        <img src="images/icons/icon-pay-03.png" alt="ICON-PAY">
                    </a>

                    <a href="#" class="m-all-1">
                        <img src="images/icons/icon-pay-04.png" alt="ICON-PAY">
                    </a>

                    <a href="#" class="m-all-1">
                        <img src="images/icons/icon-pay-05.png" alt="ICON-PAY">
                    </a>
                </div>

                <p class="stext-107 cl6 txt-center">
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    Copyright &copy;<script>
                    document.write(new Date().getFullYear());
                    </script> All rights reserved | Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a
                        href="https://colorlib.com" target="_blank">Colorlib</a> &amp; distributed by <a
                        href="https://themewagon.com" target="_blank">ThemeWagon</a>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

                </p>
            </div>
        </div>
    </footer>


    <!-- Back to top -->
    <div class="btn-back-to-top" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <i class="zmdi zmdi-chevron-up"></i>
        </span>
    </div>

    <!--===============================================================================================-->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/select2/select2.min.js"></script>
    <script>
    var qtc = document.getElementById("numpd");
    var qtstock = document.getElementById("stock");
    let btn = document.getElementById("productup");
    let btnd = document.getElementById("productdown");
    btn.addEventListener("click", function() {
        var finalqt = Number(qtc.value) + 1;
        if (finalqt >= Number(qtstock.value)) {
            btn.style.display = "none";
        }
    });
    btnd.addEventListener('click', function() {
        var finalqt = Number(qtc.value) - 1;
        if (finalqt < Number(qtstock.value)) {
            btn.style.display = "flex";
        }
    });
    qtc.addEventListener("change", function() {
        alert(qtc.value);
    });
    </script>
    <script>
    $(".js-select2").each(function() {
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    })
    </script>
    <!--===============================================================================================-->
    <script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script>
    $('.js-pscroll').each(function() {
        $(this).css('position', 'relative');
        $(this).css('overflow', 'hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });

        $(window).on('resize', function() {
            ps.update();
        })
    });
    </script>
    <!--===============================================================================================-->


</body>

</html> })
});
</script>
<!--===============================================================================================-->
<script src="js/main.js"></script>

</body>

</html>