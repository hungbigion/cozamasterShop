<?php 
include ('db-connect.php');
class Database {


    public static function select_onematerial ($id) {
      global $conn;
      $sql = "SELECT * FROM material WHERE idmaterial='$id'";
      $result = mysqli_query($conn,$sql);
      return $result;
    }
      public static function select_acc () {
      global $conn;
      $sql = "SELECT `IDacc`, `username`, `password`, `fullname`, `email`, `phonenum`, `address`, `per` FROM `account` WHERE per != 1";
      $result = mysqli_query($conn,$sql);
      return $result;
    }
      public static function select_contact () {
      global $conn;
      $sql = "SELECT `idcontact`, `email`, `date`, `message` FROM `contact` 
ORDER BY date DESC";
      $result = mysqli_query($conn,$sql);
      return $result;
    }

    public static function select_material() {
        global $conn;
        $sql = "SELECT * FROM material ";
        $run = mysqli_query($conn, $sql);
        return $run;
    }
    
    public function select_material_ex ($id) {
      global $conn;
      $sql = "SELECT * FROM material WHERE idmaterial != '$id'";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
      public function select_total ($id) {
      global $conn;
      $sql = "SELECT * FROM cart WHERE IDacc = '$id'";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
      public function select_bill ($id) {
      global $conn;
      $sql = "SELECT * FROM `bill` WHERE IDacc = '$id'";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
      public function select_all_bill () {
      global $conn;
      $sql = "SELECT * FROM `bill` ORDER BY dateout DESC";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
      public function select_totalm () {
      global $conn;
      $sql = "SELECT SUM(bill.billtotal) FROM bill WHERE MONTH(dateout) =  MONTH(CURDATE()) AND YEAR(dateout) =  YEAR(CURDATE()) ";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
      public function select_totald () {
      global $conn;
      $sql = "SELECT SUM(bill.billtotal) FROM bill WHERE MONTH(dateout) =  MONTH(CURDATE()) AND YEAR(dateout) = YEAR(CURDATE()) AND DATE(CURDATE()) = DATE(dateout)";
      $run = mysqli_query($conn,$sql);
      return $run;
    }

     public function select_style_ex ($id) {
      global $conn;
      $sql = "SELECT * FROM style WHERE idstyle != '$id'";
      $run = mysqli_query($conn,$sql);
      return $run;
    }
    public static function  select_pd() {
        global $conn;
        $sql = "SELECT idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
                FROM product 
                INNER JOIN material
                ON product.idmaterial = material.idmaterial
                INNER JOIN style 
                ON product.idstyle = style.idstyle
                WHERE sttus != 3 AND product.pdquantity > 0 
                ORDER BY sttus ASC";
        $run = mysqli_query($conn, $sql);
        return $run;
    }
        public static function  select_pd_all() {
        global $conn;
        $sql = "SELECT idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
                FROM product 
                INNER JOIN material
                ON product.idmaterial = material.idmaterial
                INNER JOIN style 
                ON product.idstyle = style.idstyle
                ORDER BY sttus ASC";
        $run = mysqli_query($conn, $sql);
        return $run;
    }
        public static function  quantity_of_cart($id) {
        global $conn;
        $sql = "SELECT COUNT(*) AS slcart FROM cartdetail WHERE cartdetail.IDacc = '$id'";
        $run = mysqli_query($conn, $sql);
        return $run;
    }
        public static function  select_cart($id) {
        global $conn;
        $sql = "SELECT idcartdetail,cartdetail.idcart,cartdetail.IDacc,cartdetail.idproduct,
                cartdetail.namepd,cartdetail.price,cartdetail.quantitycart, product.pdquantity,product.image, cartdetail.totalpd,cart.total
                FROM cartdetail
                INNER JOIN product
                ON cartdetail.idproduct = product.idproduct
                INNER JOIN cart 
                ON cartdetail.idcart = cart.idcart
                WHERE cartdetail.IDacc = '$id'";
        $run = mysqli_query($conn, $sql);
        return $run;
    }

    public static function  select_pd_ex($id) {
      global $conn;
      $sql = "SELECT idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
              FROM product 
              INNER JOIN material
              ON product.idmaterial = material.idmaterial
              INNER JOIN style 
              ON product.idstyle = style.idstyle
              WHERE product.idproduct = '$id' AND product.pdquantity > 0 ";
      $run = mysqli_query($conn, $sql);
      return $run;
    }
        public static function  select_pd_st_ex($id) {
      global $conn;
      $sql = "SELECT idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
              FROM product 
              INNER JOIN material
              ON product.idmaterial = material.idmaterial
              INNER JOIN style 
              ON product.idstyle = style.idstyle
              WHERE product.idstyle = '$id' AND sttus != 3 AND product.pdquantity > 0  ";
      $run = mysqli_query($conn, $sql);
      return $run;
    }

    public static function  select_pd_st1() {
      global $conn;
      $sql = "SELECT  idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
              FROM product 
              INNER JOIN material
              ON product.idmaterial = material.idmaterial
              INNER JOIN style 
              ON product.idstyle = style.idstyle
              WHERE product.idstyle = 1  AND sttus != 3 AND product.pdquantity > 0 ";
      $run = mysqli_query($conn, $sql);
      return $run;
    }
    public static function  select_pd_st2() {
      global $conn;
      $sql = "SELECT  idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
              FROM product 
              INNER JOIN material
              ON product.idmaterial = material.idmaterial
              INNER JOIN style 
              ON product.idstyle = style.idstyle
              WHERE product.idstyle = 2  AND sttus != 3 AND product.pdquantity > 0 ";
      $run = mysqli_query($conn, $sql);
      return $run;
    }
    public static function  select_pd_st3() {
      global $conn;
      $sql = "SELECT  idproduct,namepd,product.idmaterial,material.materialname,product.idstyle,style.namestyle,price,pdquantity,detail,image,sttus 
              FROM product 
              INNER JOIN material
              ON product.idmaterial = material.idmaterial
              INNER JOIN style 
              ON product.idstyle = style.idstyle
              WHERE product.idstyle = 4  AND sttus != 3 AND product.pdquantity > 0 ";
      $run = mysqli_query($conn, $sql);
      return $run;
    }

    public static function select_style() {
        global $conn;
        $sql = "SELECT * FROM style ";
        $run = mysqli_query($conn, $sql);
        return $run;
    }
  
    
}
?>