<!DOCTYPE html>
<html lang="en">
<?php 
	include ('db-connect.php');
    include ('database.php');
    session_start();
    $account = (isset($_SESSION['account'])) ? $_SESSION['account']: [];
    $getdata = new Database ();
    $select_pd_ex = $getdata ->select_pd_ex($_GET['idproduct']);
	$select_pd_st_ex = $getdata ->select_pd_st_ex($_GET['idstyle']);
    if(isset($account['IDacc'])) {
    $quantity_cart = $getdata ->quantity_of_cart($account['IDacc']);
    $select_cart= $getdata ->select_cart($account['IDacc']); 
    $select_total = $getdata ->select_total($account['IDacc']);  
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
            $check_query =mysqli_query($conn,"SELECT * from cartdetail WHERE cartdetail.idproduct = '$idproduct' AND cartdetail.IDacc = '$idacc'") ;
            if (mysqli_num_rows($check_query) > 0 ) {
                $update_query = mysqli_query($conn, "UPDATE `cartdetail` SET `quantitycart`=(
                        SELECT cartdetail.quantitycart FROM cartdetail WHERE 
                        cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = $idcart) + '$num_product',
                        `totalpd` = (
                        (SELECT cartdetail.quantitycart FROM cartdetail WHERE 
                        cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = $idcart)+ '$num_product') * 
                        (SELECT cartdetail.price FROM cartdetail WHERE cartdetail.idproduct = '$idproduct'  AND cartdetail.idcart = $idcart)
                        WHERE cartdetail.idproduct = '$idproduct' AND cartdetail.idcart = $idcart");
                if ($update_query) {
                    $updatecart_query = mysqli_query($conn, "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.IDacc = '$idacc')
                        WHERE cart.IDacc = '$idacc'");
                        echo "<script>
                        history.back();
                        </script>";
                }

            }
            else {
                $insert_query = mysqli_query($conn, "INSERT INTO 
                `cartdetail`( `idcart`, `IDacc`, `idproduct`, `namepd`, `quantitycart`, `price`, `totalpd`) 
                VALUES ($idcart,'$idacc','$idproduct','$namepd','$num_product','$price',$totalpd)");
                if ($insert_query) {
                    $updatecart_query = mysqli_query($conn, "UPDATE `cart` SET `total` = (SELECT SUM(cartdetail.totalpd) FROM cartdetail WHERE cartdetail.IDacc = '$idacc')
                        WHERE cart.IDacc = '$idacc'");
                         echo "<script>
                        history.back();
                        </script>";
                }
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
    <title>Product Detail</title>
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



    <!-- Product Detail -->
    <?php
	foreach ($select_pd_ex as $se ) {
	?>
    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb">
                                <div class="item-slick3" data-thumb="../../darkpan-1.0.0/<?php echo $se['image']; ?>">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="../../darkpan-1.0.0/<?php echo $se['image']; ?>" alt="IMG-PRODUCT">

                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                            href="../../darkpan-1.0.0/<?php echo $se['image']; ?>">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            <?php echo $se['namepd']; ?>
                        </h4>

                        <span class="mtext-106 cl2">
                            <?php echo $se['price']; ?> VND
                        </span>




                        <div class="p-t-33">
                            <div class="flex-w flex-r-m p-b-10">
                                <form method="post" class="size-204 flex-w flex-m respon6-next">
                                    <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                                        Product Detail
                                    </h4>
                                    <div class="md-form">
                                        <span class="mtext-106 cl2">
                                            Material
                                        </span>
                                        <input disabled value="<?php echo $se['materialname']; ?>" type="text"
                                            id="form1" class="form-control">
                                    </div>
                                    <br></br>
                                    <div class="md-form">
                                        <span class="mtext-106 cl2">
                                            Style
                                        </span>
                                        <input disabled value="<?php echo $se['namestyle']; ?>" type="text" id="form1"
                                            class="form-control">
                                    </div>
                                    <div class="md-form">
                                        <span class="mtext-106 cl2">
                                            Quantity in stock: <?php echo $se['pdquantity']; ?>
                                        </span>
                                        <input style="display:none;" name="pdquantity"
                                            value="<?php echo $se['pdquantity']; ?>" type="text" id="stock"
                                            class="form-control">
                                    </div>
                                    <div style="display:none;" class="md-form">
                                        <span class="mtext-106 cl2">
                                            ID:
                                        </span>
                                        <input name="idproduct" value="<?php echo $se['idproduct']; ?>" type="text"
                                            class="form-control">
                                    </div>
                                    <div style="display:none;" class="md-form">
                                        <span class="mtext-106 cl2">
                                            name:
                                        </span>
                                        <input name="namepd" value="<?php echo $se['namepd']; ?>" type="text"
                                            class="form-control">
                                    </div>
                                    <div style="display:none;" class="md-form">
                                        <span class="mtext-106 cl2">
                                            Price:
                                        </span>
                                        <input name="price" value="<?php echo $se['price']; ?>" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                        <div id="productdown"
                                            class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-minus"></i>
                                        </div>

                                        <input id="numpd" class="mtext-104 cl3 txt-center num-product" type="number"
                                            name="num-product" value="1">

                                        <div id="productup" class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-plus"></i>
                                        </div>
                                    </div>
                                    <?php if (isset($account['fullname'])) {?>
                                    <input name="addtcart" type="submit" value="Add to cart" class=" flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04
                                            ">
                                    </input>
                                    <?php } else {?>
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>You are not logged in!</strong> Please login to add products to
                                        your
                                        cart.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php }?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <!-- Tab01 -->
                <div class="tab01">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a>
                        </li>

                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab">Additional
                                information</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-t-43">
                        <!-- - -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">
                                    <?php echo $se['detail']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- - -->
                        <div class="tab-pane fade" id="information" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">
                                                Material
                                            </span>

                                            <span class="stext-102 cl6 size-206">
                                                <?php echo $se['materialname']; ?>
                                            </span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">
                                                Style
                                            </span>

                                            <span class="stext-102 cl6 size-206">
                                                <?php echo $se['namestyle']; ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php }?>


    <!-- Related Products -->
    <section class="sec-relate-product bg0 p-t-45 p-b-105">
        <div class="container">
            <div class="p-b-45">
                <h3 class="ltext-106 cl5 txt-center">
                    Related Products
                </h3>
            </div>
            <!-- Slide2 -->
            <div class="wrap-slick2">
                <div class="slick2">
                    <?php
                                foreach ($select_pd_st_ex as $se ) {
                                ?>
                    <div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
                        <!-- Block2 -->
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                <img src="../../darkpan-1.0.0/<?php echo $se['image'] ?>" alt="IMG-PRODUCT">
                                <a class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1
                                                p-lr-15 trans-04 "
                                    href="product-detail.php?idpdmodal=<?php echo $se['idproduct']; ?>">
                                    Quick View
                                </a>

                            </div>
                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l ">
                                    <a href="product-detail.php?idproduct=<?php echo $se['idproduct']; ?>&idstyle=<?php echo $se['idstyle']; ?>"
                                        class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                        <?php echo $se['namepd'] ?>
                                    </a>
                                    <span class=" stext-105 cl3">
                                        <?php echo $se['price'] ?> VND
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </section>


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
                        Any questions? Let us know in store at 8th floor, 379 Hudson St, New York, NY 10018 or call us
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

    <div style=" display: none; position: fixed; z-index: 1900;padding-top: 100px;
                left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgba(175, 172, 172, 0.541);
                align-items: center;justify-content: center;" class="modal11  p-t-60 p-b-20">
        <?php                        
                    if(isset($_GET['idpdmodal'])){
                            $edit_id = $_GET['idpdmodal'];
                            $edit_query = mysqli_query($conn, "SELECT idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
                            FROM product 
                            INNER JOIN material
                            ON product.idmaterial = material.idmaterial
                            INNER JOIN style 
                            ON product.idstyle = style.idstyle
                            WHERE product.idproduct = '$edit_id'");
                            if(mysqli_num_rows($edit_query) > 0){
                            while($fetch_edit = mysqli_fetch_assoc($edit_query)){
            ?>
        <div class="overlay-modal1 js-hide-modal1"></div>

        <div class="container">
            <div class="bg0 p-t-60 p-b-30 p-lr-15-lg how-pos3-parent">
                <button onclick="history.back();" class="how-pos3 hov3 trans-04 js-hide-modal1">
                    <img src="images/icons/icon-close.png" alt="CLOSE">
                </button>
                <div class="row">
                    <div class="col-md-6 col-lg-7 p-b-30">
                        <div class="p-l-25 p-r-30 p-lr-0-lg">
                            <div class="wrap-slick3 flex-sb flex-w">
                                <div class="wrap-slick3-dots"></div>
                                <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>
                                <div class="slick3 gallery-lb">
                                    <div class="item-slick3"
                                        data-thumb="../../darkpan-1.0.0/<?php echo $fetch_edit['image']; ?>">
                                        <div class="wrap-pic-w pos-relative">
                                            <img src="../../darkpan-1.0.0/<?php echo $fetch_edit['image']; ?>"
                                                alt="IMG-PRODUCT">

                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                href="../../darkpan-1.0.0/<?php echo $fetch_edit['image']; ?>">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-5 p-b-30">
                        <div class="p-r-50 p-t-5 p-lr-0-lg">
                            <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                                <?php echo $fetch_edit['namepd']; ?>
                            </h4>

                            <span class="mtext-106 cl2">
                                <?php echo $fetch_edit['price']; ?> VND
                            </span>

                            <p class="stext-102 cl3 p-t-23">
                                <?php echo $fetch_edit['detail']; ?>
                            </p>


                            <div class="p-t-33">
                                <div class="flex-w flex-r-m p-b-10">
                                    <form method="post" class="size-204 flex-w flex-m respon6-next">
                                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                                            Product Detail
                                        </h4>
                                        <div class="md-form">
                                            <span class="mtext-106 cl2">
                                                Material
                                            </span>
                                            <input disabled value="<?php echo $fetch_edit['materialname']; ?>"
                                                type="text" id="form1" class="form-control">
                                        </div>
                                        <br></br>
                                        <div class="md-form">
                                            <span class="mtext-106 cl2">
                                                Style
                                            </span>
                                            <input disabled value="<?php echo $fetch_edit['namestyle']; ?>" type="text"
                                                id="form1" class="form-control">
                                        </div>
                                        <div class="md-form">
                                            <span class="mtext-106 cl2">
                                                Quantity in stock: <?php echo $fetch_edit['pdquantity']; ?>
                                            </span>
                                            <input style="display:none;" name="pdquantity"
                                                value="<?php echo $fetch_edit['pdquantity']; ?>" type="text" id="stock"
                                                class="form-control">
                                        </div>
                                        <div style="display:none;" class="md-form">
                                            <span class="mtext-106 cl2">
                                                ID:
                                            </span>
                                            <input name="idproduct" value="<?php echo $fetch_edit['idproduct']; ?>"
                                                type="text" class="form-control">
                                        </div>
                                        <div style="display:none;" class="md-form">
                                            <span class="mtext-106 cl2">
                                                name:
                                            </span>
                                            <input name="namepd" value="<?php echo $fetch_edit['namepd']; ?>"
                                                type="text" class="form-control">
                                        </div>
                                        <div style="display:none;" class="md-form">
                                            <span class="mtext-106 cl2">
                                                Price:
                                            </span>
                                            <input name="price" value="<?php echo $fetch_edit['price']; ?>" type="text"
                                                class="form-control">
                                        </div>
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div id="productdown"
                                                class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input id="numpd" class="mtext-104 cl3 txt-center num-product" type="number"
                                                name="num-product" value="1">

                                            <div id="productup"
                                                class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                        <?php if (isset($account['fullname'])) {?>
                                        <input name="addtcart" type="submit" value="Add to cart" class=" flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04
                                            ">
                                        </input>
                                        <?php } else {?>
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <strong>You are not logged in!</strong> Please login to add products to
                                            your
                                            cart.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <?php }?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
                            };
                            };
                            echo "
                            <script>
                            document.querySelector('.modal11').style.display = 'block';
                            document.querySelector('.modal11').style.backgroundColor = 'rgba(175, 172, 172, 0.541)';
                            </script>";
                        };
                    ?>
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
    <script src="vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/slick/slick.min.js"></script>
    <script src="js/slick-custom.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/parallax100/parallax100.js"></script>
    <script>
    $('.parallax100').parallax100();
    </script>
    <!--===============================================================================================-->
    <script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
    <script>
    $('.gallery-lb').each(function() { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true
            },
            mainClass: 'mfp-fade'
        });
    });
    </script>
    <!--===============================================================================================-->
    <script src="vendor/isotope/isotope.pkgd.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/sweetalert/sweetalert.min.js"></script>
    <script>
    $('.js-addwish-b2, .js-addwish-detail').on('click', function(e) {
        e.preventDefault();
    });

    $('.js-addwish-b2').each(function() {
        var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
        $(this).on('click', function() {
            swal(nameProduct, "is added to wishlist !", "success");

            $(this).addClass('js-addedwish-b2');
            $(this).off('click');
        });
    });

    $('.js-addwish-detail').each(function() {
        var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

        $(this).on('click', function() {
            swal(nameProduct, "is added to wishlist !", "success");

            $(this).addClass('js-addedwish-detail');
            $(this).off('click');
        });
    });

    /*---------------------------------------------*/

    $('.js-addcart-detail').each(function() {
        var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
        $(this).on('click', function() {
            swal(nameProduct, "is added to cart !", "success");
        });
    });
    </script>
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

</html>