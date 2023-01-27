<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include ('db-connect.php');
    include ('database.php');
    session_start();
    $account = (isset($_SESSION['account'])) ? $_SESSION['account']: [];
    $getdata = new Database ();
    $select_pd = $getdata ->select_pd();
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
    <title>About</title>
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
                            <li>
                                <a href="product.php">Shop</a>
                            </li>
                            <li class="active-menu">
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
                            <!-- =====================================-->
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


    <!-- Title page -->
    <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
        <h2 class="ltext-105 cl0 txt-center">
            About
        </h2>
    </section>


    <!-- Content page -->
    <section class="bg0 p-t-75 p-b-120">
        <div class="container">
            <div class="row p-b-148">
                <div class="col-md-7 col-lg-8">
                    <div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
                        <h3 class="mtext-111 cl2 p-b-16">
                            Our Story
                        </h3>

                        <p class="stext-113 cl6 p-b-26">Hello!

                            Introduce
                            Introduce
                            I am NGUYEN HUU HUNG born on September 27, 2001.

                            Introducing brothers and sisters. I was born in Hanoi and raised in Binh Phuoc. After
                            finishing secondary school, I got
                            lost in the city. Ho Chi Minh City, start a new life with the mindset of finding your own
                            path.

                            After a while, they often go to places where there are children with disabilities and visual
                            impairments. I finally came
                            up with a way to do something to help partially create the light. Giving it back to people,
                            and I chose to seek out ways
                            to increase my eyesight. I came to the refractory industry determined to pursue this path,
                            helping people increase their
                            perspective. And this is also a way to help yourself have more light, more joy.</p>

                        <p class="stext-113 cl6 p-b-26">PROBLEM SOLVING!

                            After graduation, I often follow the group to participate in charity visits. To help
                            increase the light for the
                            students. At this time, I see that there are many children with vision loss but do not know
                            how to improve.</p>

                        <p class="stext-113 cl6 p-b-26">From here I see that I still need to do more for this program.
                            Many months of sharing from teachers, the social
                            community, with parents and students. I am determined to bring out the product that is
                            Maximum Vision Correcting
                            Glasses. To bring a happy life to the whole society, pay attention to those who wear
                            glasses.</p>
                    </div>
                </div>

                <div class="col-11 col-md-5 col-lg-4 m-lr-auto">
                    <div class="how-bor1 ">
                        <div class="hov-img0">
                            <img src="images/ourstory.jpg" alt="IMG">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="order-md-2 col-md-7 col-lg-8 p-b-30">
                    <div class="p-t-7 p-l-85 p-l-15-lg p-l-0-md">
                        <h3 class="mtext-111 cl2 p-b-16">
                            Our Mission
                        </h3>

                        <p class="stext-113 cl6 p-b-26">MISSION
                            cozamaster - Vietnam's Leading Blue Light Anti-Eye Protector
                            With the operating philosophy from the name, "Cozamaster - Cover your eyes", Kavi glasses
                            always ensure that each glass
                            produced is of the best quality and brings absolute satisfaction to customers.

                            Kavi lenses are manufactured using new Korean technology, High index lenses, which make them
                            light in weight and have
                            higher light transmission than ordinary lenses. As a result, the wearer of glasses does not
                            have reduced vision, giving
                            the feeling of "wearing but not wearing".

                            Developing and maintaining the position of always being a pioneer and leading brand in
                            Vietnam.

                            Providing high quality products, always satisfying, meeting the diverse needs, desires and
                            tastes of domestic and
                            foreign customers.

                            – Constantly creating, breaking through, practicing good, creating value, worthy of being a
                            great brand.

                            Always take the satisfaction of customers and consumers as the focus for all activities.
                            Working closely with partners
                            in the spirit of trust, respect and equality for the mutual success and victory of both
                            sides.

                            – Always pay attention to the matter of survival, business development, regularly cultivate
                            company values, brand
                            values, customer values, legitimate interests of customers; always conscious of
                            environmental protection.

                            – Protect and develop brand reputation, build and constantly cultivate the unique cultural
                            identity of the business, its
                            brand identity, stimulate creative labor to maintain a meaningful difference , ensuring high
                            competitiveness, always
                            adapting to the business environment, orientation for sustainable and long-term development.
                        </p>
                    </div>
                </div>

                <div class="order-md-1 col-11 col-md-5 col-lg-4 m-lr-auto p-b-30">
                    <div class="how-bor2">
                        <div class="hov-img0">
                            <img src="images/ourmission.jpg" alt="IMG">
                        </div>
                    </div>
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

</html>