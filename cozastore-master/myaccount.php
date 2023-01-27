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
    $select_bill = $getdata ->select_bill($account['IDacc']);
    }
    // change user information 
    if (isset($_POST['change'])){
        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        $phonenum = $_POST['phonenum'];
        $address = $_POST['address'];
        $idacc = $account['IDacc'];
        $email = $_POST['email'];
         $sql = "SELECT  account.IDacc,account.username,account.fullname
                ,account.phonenum,account.address,account.email, account.password ,cart.idcart ,per
                FROM account 
                INNER JOIN cart
                ON account.IDacc = cart.IDacc
                WHERE account.IDacc != '$idacc' AND (email='$email' OR phonenum = '$phonenum')";
        $query = mysqli_query($conn,$sql);
        $data = mysqli_fetch_assoc($query);
        $check = mysqli_num_rows($query);
        if ($check == 0) {
         $change =  mysqli_query($conn,"UPDATE `account` SET `username`='$username',`fullname`='$fullname',`email`='$email',`phonenum`='$phonenum',`address`='$address' WHERE IDacc = '$idacc'");
        if ($change) {
            echo "<script>
			alert('Update infor success! login in again to apply the new changes');
			</script>";
            header('location: logout.php');
        }
        else {
            echo "<script>
			alert(' Failed !');
			</script>";
           header('location: logout.php');
        } 
        }
        else {
            echo "<script>alert('Đã tồn tại tài khoản ');</script>";
            header( "refresh:2;url=myaccount.php" );
        }

    }
	// check out
	if (isset($_POST['checkout'])) {
		$idcart = $account['idcart'];
		$idacc = $account['IDacc'];
		$fullname = $account['fullname'];
		$phonenum = $account['phonenum'];
		$address = $account['address'];
		$dateout = $_POST['dateout'];
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
			mysqli_query($conn,"UPDATE `cart` SET `total`= 0 WHERE IDacc = '$idacc'");
			header('location: shoping-cart.php');
		}
		
	}
	}
	// delete product from cart
	if(isset($_GET['deletecartdetail'])) {
		$idcart = $account['idcart'];
		$delete_id = $_GET['deletecartdetail'];
        $delete_query = mysqli_query($conn, "DELETE FROM `cartdetail` WHERE idcartdetail = $delete_id ") or die('query failed');
        if($delete_query){
			 $updatecart_query = mysqli_query($conn, 
                "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.idcart = '$idcart')
 				WHERE cart.idcart = '$idcart'");
				header('location: shoping-cart.php');
        }else{
        header('location:widget.php');
        };
	}
	//update cart quantity
	if (isset($_POST['updatecart'])) {
		$num_product = $_POST['num-product'];
		$idcartdetail = $_POST['idcartdetail'];
		$idproduct = $_POST['idproduct'];
		$idcart = $account['idcart'];
		$update_query = mysqli_query($conn, "UPDATE `cartdetail` SET `quantitycart`='$num_product',
                        `totalpd` =  '$num_product' * (SELECT cartdetail.price FROM cartdetail WHERE cartdetail.idcartdetail = '$idcartdetail')
                        WHERE cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = '$idcart'");

		if ($update_query) {
			 $updatecart_query = mysqli_query($conn, 
                "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.idcart = '$idcart')
 				WHERE cart.idcart = '$idcart'");
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
        $sql = "SELECT  account.IDacc,account.username,account.fullname
                ,account.phonenum,account.address,account.email, account.password ,cart.idcart ,per
                FROM account 
                INNER JOIN cart
                ON account.IDacc = cart.IDacc
                WHERE email='$email' OR phonenum = '$phone'";
        $query = mysqli_query($conn,$sql);
        $data = mysqli_fetch_assoc($query);
        $check = mysqli_num_rows($query);
        if ($check==0) {
        $sql = "INSERT INTO account (username, password, fullname, email, phonenum, address,per) 
        VALUES('$username', '$password', '$fullname', '$email', '$phone', '$address', '$per')";
        $query = mysqli_query($conn,$sql);
        // header('location: home-02.php'); 
        echo 'Đăng ký tài khoản thành công';
        if ($query) {
        $sql1 = "INSERT INTO cart (IDacc, total) 
        VALUES((SELECT account.IDacc FROM account WHERE account.email = '$email' ), 0)";
        $query1 = mysqli_query($conn,$sql1); 
        echo "<script>alert('Đăng ký thành công !');</script>";
        header( "refresh:2;url=home-02.php" );
        }
        }
        else {
            echo "<script>alert('Đã tồn tại tài khoản ');</script>";
            header( "refresh:2;url=home-02.php" );
        }

    }
    // login
     if (isset($_POST['login'])){
        $email = $_POST['uname'];
        $password = $_POST['psw'];
        $sql = "SELECT  account.IDacc,account.username,account.fullname
                ,account.phonenum,account.address,account.email, account.password ,cart.idcart ,per
                FROM account 
                INNER JOIN cart
                ON account.IDacc = cart.IDacc
                WHERE email='$email' AND password='$password'";
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
                header('location: home-02.php');
            }
        }
        else {
            echo "<script>alert('TT tài khoản không đúng ');</script>";
        }
    }
    // login
    
?>

<head>
    <title>Your Account</title>
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
                                <a href="blog.php">Blog</a>
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
                    <a href="shoping-cart.php" class="label1 rs1" data-label1="hot">Your Cart</a>
                </li>

                <li>
                    <a href="blog.php">Blog</a>
                </li>

                <li>
                    <a href="about.php">About</a>
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
                        <form method="post" class="col-sm-10 col-lg-10 col-xl-10 m-lr-auto m-b-50">

                            <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                                <div class="size-208 w-full-ssm">
                                    <h4 class="mtext-109 cl2 p-b-30">
                                        Your information
                                    </h4>
                                </div>
                                <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                                    <div class="p-t-15">
                                        <span class="stext-110 cl2">
                                            Username :
                                        </span>
                                        <div class="bor8 bg0 m-b-12">
                                            <input name="username" type="text"
                                                value="<?php echo $account['username']; ?>"
                                                class="stext-111 cl8 plh3 size-111 p-lr-15">
                                        </div>
                                    </div>
                                    <div class="p-t-15">
                                        <span class="stext-110 cl2">
                                            Name :
                                        </span>
                                        <div class="bor8 bg0 m-b-12">
                                            <input name="fullname" type="text"
                                                value="<?php echo $account['fullname']; ?>"
                                                class="stext-111 cl8 plh3 size-111 p-lr-15">
                                        </div>
                                    </div>
                                    <div class="p-t-15">
                                        <span class="stext-110 cl2">
                                            Email :
                                        </span>
                                        <div class="bor8 bg0 m-b-12">
                                            <input name="email" type="email" value="<?php echo $account['email']; ?>"
                                                class="stext-111 cl8 plh3 size-111 p-lr-15">
                                        </div>
                                    </div>
                                    <div class="p-t-15">
                                        <span class="stext-110 cl2">
                                            Phone :
                                        </span>
                                        <div class="bor8 bg0 m-b-12">
                                            <input name="phonenum" type="text"
                                                value="<?php echo $account['phonenum']; ?>"
                                                class="stext-111 cl8 plh3 size-111 p-lr-15">
                                        </div>
                                    </div>
                                    <div class="p-t-15">
                                        <span class="stext-110 cl2">
                                            Address :
                                        </span>
                                        <div class="form-group">
                                            <textarea name="address" class="form-control"
                                                id="exampleFormControlTextarea3" rows="7"><?php echo $account['address']; ?>
									</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input name="change" type="submit" value="Save changes"
                                class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                    </div>
                    </form>

                    <div class="table-responsive">
                        <div class="size-208 w-full-ssm">
                            <h4 class="mtext-109 cl2 p-b-30">
                                Checked Order
                            </h4>
                        </div>
                        <!--Table-->
                        <table class="table table-striped">

                            <!--Table head-->
                            <thead>
                                <tr>
                                    <th>ID Bill</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Date Out</th>
                                    <th>Detail Order</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <!--Table head-->

                            <!--Table body-->
                            <tbody>
                                <?php
							foreach ($select_bill as $se ) {
							?>
                                <tr>
                                    <th scope="row"><?php echo $se['idbill'] ?></th>
                                    <td><?php echo $se['fullname'] ?></td>
                                    <td><?php echo $se['phonenum'] ?></td>
                                    <td><?php echo $se['address'] ?></td>
                                    <td><?php echo $se['dateout'] ?></td>
                                    <td><?php echo $se['detailbill'] ?></td>
                                    <td><?php echo $se['billtotal'] ?>VND</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



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
        if (finalqt == Number(qtstock.value)) {
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
    <script src="js/main.js"></script>

</body>

</html> })
});
</script>
<!--===============================================================================================-->
<script src="js/main.js"></script>

</body>

</html>