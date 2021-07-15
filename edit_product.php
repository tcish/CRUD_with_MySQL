<?php
  require_once "./connect/connection.php";
  require_once "./inc/header.php";

  if (!isset($_GET["id"]) OR $_GET["id"] == NULL OR !isset($_GET["cat_id"]) OR $_GET["cat_id"] == NULL OR !isset($_GET["brand_id"]) OR $_GET["brand_id"] == NULL) {
    header("Location: index.php");
  }else {
    $id = $_GET["id"];
    $cat_id = $_GET["cat_id"];
    $brand_id = $_GET["brand_id"];

    $fetch_query = "SELECT * FROM products WHERE id = $id";
    $fetch_sql = $connection->prepare($fetch_query);
    $fetch_sql->execute();
    $show = $fetch_sql->fetch((PDO::FETCH_ASSOC));
  }

  $error__1 = NULL;
  $error__2 = NULL;
  $error__3 = NULL;
  $error__4 = NULL;
  $error__5 = NULL;
  $error__6 = NULL;
  $error__7 = NULL;
  $error__8 = NULL;

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $pCategories = trim(strip_tags($_POST["productCategories"]));
    $pBrand = trim(strip_tags($_POST["productBrand"]));
    $pName = trim(strip_tags($_POST["productName"]));
    $pPrice = trim(strip_tags($_POST["productPrice"]));
    $pShort_desc =trim(strip_tags( $_POST["short_desc"]));
    $pLong_desc = trim(strip_tags($_POST["desc"]));
    $pImage_name = $_FILES["productImage"]["name"];
    $pImage_size = $_FILES["productImage"]["size"];
    $pImage_temp_location = $_FILES["productImage"]["tmp_name"];

    $allow_format = array("jpg", "jpeg", "png");
    $divide_name = explode(".", $pImage_name);
    $lowercase_img_ext_name = strtolower(end($divide_name));
    $unique_name_generate = rand(1000000, 100000000).".". $lowercase_img_ext_name;
    $image_directory = "img/";

    if (empty($pCategories)) {
      $error__1 = "<h6 style='color: red;'>Please Select Product Categories!</h6>";
    }
    if (empty($pBrand)) {
      $error__2 = "<h6 style='color: red;'>Please Select Product Brand!</h6>";
    }
    if (empty($pName)) {
      $error__3 = "<h6 style='color: red;'>Please Enter Product Name!</h6>";
    }
    if (empty($pPrice)) {
      $error__4 = "<h6 style='color: red;'>Please Enter Product Price!</h6>";
    }
    if (empty($pShort_desc)) {
      $error__6 = "<h6 style='color: red;'>Please Enter Short Description!</h6>";
    }
    if (empty($pLong_desc)) {
      $error__7 = "<h6 style='color: red;'>Please Enter Product Description!</h6>";
    }

    //check product already exist
    $query = "SELECT * FROM products WHERE name = '$pName'";
    $sql = $connection->prepare($query);
    $sql->execute();
    $fetch = $sql->fetch(PDO::FETCH_ASSOC);

    if (!empty($pImage_name)) {
      if ($pImage_size > 4000096) {
        $error__5 = "<h6 style='color:red;'>Your Image should less than 4MB !</h6>";
      }elseif (!in_array($lowercase_img_ext_name, $allow_format)) {
        $error__5 = "<h6 style='color:red;'>Only JPG, JPEG, PNG, files are allowed!</h6>";
      }else {
        if (isset($_GET["id"]) AND $_GET["id"] != NULL AND isset($_GET["cat_id"]) AND $_GET["cat_id"] != NULL AND isset($_GET["brand_id"]) AND $_GET["brand_id"] != NULL) {
          $pic_del_query = "SELECT image FROM products WHERE id = '$id'";
          $pic_del_sql = $connection->prepare($pic_del_query);
          $pic_del_sql->execute();
          $output = $pic_del_sql->fetch(PDO::FETCH_ASSOC);
          $del_pic = $output["image"];
          unlink("img/$del_pic");

          $product_update = "UPDATE products SET
                              categories_id = :categories_id,
                              brand_id = :brand_id,
                              name = :name,
                              price = :price,
                              image = :image,
                              short_desc = :short_desc,
                              description = :description
                              WHERE id = :id";
          $pro_up = $connection->prepare($product_update);
          $pro_up->bindParam(":categories_id", $pCategories);
          $pro_up->bindParam(":brand_id", $pBrand);
          $pro_up->bindParam(":name", $pName);
          $pro_up->bindParam(":price", $pPrice);
          $pro_up->bindParam(":image", $unique_name_generate);
          $pro_up->bindParam(":short_desc", $pShort_desc);
          $pro_up->bindParam(":description", $pLong_desc);
          $pro_up->bindParam(":id", $id);
          $pro_up->execute();

          move_uploaded_file($pImage_temp_location, $image_directory.$unique_name_generate);

          header("Location: index.php");
        }else {
          $error__8 = "<h6 style='color:red;'>There is something wrong while updating, please try again!</h6>";
        }
      }
    }else {
      if (isset($_GET["id"]) && $_GET["id"] != NULL && isset($_GET["cat_id"]) && $_GET["cat_id"] != NULL && isset($_GET["brand_id"]) && $_GET["brand_id"] != NULL) {
        $product_update = "UPDATE products SET
                            categories_id = :categories_id,
                            brand_id = :brand_id,
                            name = :name,
                            price = :price,
                            short_desc = :short_desc,
                            description = :description
                            WHERE id = :id";
        $pro_up = $connection->prepare($product_update);
        $pro_up->bindParam(":categories_id", $pCategories);
        $pro_up->bindParam(":brand_id", $pBrand);
        $pro_up->bindParam(":name", $pName);
        $pro_up->bindParam(":price", $pPrice);
        $pro_up->bindParam(":short_desc", $pShort_desc);
        $pro_up->bindParam(":description", $pLong_desc);
        $pro_up->bindParam(":id", $id);
        $pro_up->execute();

        header("Location: index.php");
      }else {
        $error__8 = "<h6 style='color:red;'>There is something wrong while updating, please try again!</h6>";
      }
    }
  }
?>
<div class="jumbotron m-0">
  <div class="container">
    <div class="row">
      <div class="col-12 col-sm-8 col-md-6 col-lg-5 offset-sm-2 offset-md-3 offset-lg-3">
        <div class="shadow-lg p-4 bg-dark text-white">
          <h1 class="text-center mb-3 pb-1 custom__border text-info">Product Edit Form</h1>
          <span><?php echo "$error__8"; ?></span>
          <form autocomplete="off" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="productCategories" class="font-weight-bold">Select Categories:</label>
              <select name="productCategories" class="form-control">
                <option value="0">Select Categories</option>

                <?php
                  $fetch_cat_query = "SELECT * FROM categories";
                  $fetch_cat_sql = $connection->prepare($fetch_cat_query);
                  $fetch_cat_sql->execute();
                  while ($show_cat = $fetch_cat_sql->fetch(PDO::FETCH_ASSOC)) {
                      if ($show_cat["id"] == $cat_id) {
                          echo "<option selected value='".$show_cat['id']."'>".$show_cat['categories']."</option>";
                      } else {
                          echo "<option value='".$show_cat['id']."'>".$show_cat['categories']."</option>";
                      }
                  }
                ?>

              </select>
              <span><?php echo "$error__1"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Select Brand:</label>
              <select name="productBrand" class="form-control">
                <option value="0">Select Brand</option>

                <?php
                    $fetch_brand_query = "SELECT * FROM brands";
                    $fetch_brand_sql = $connection->prepare($fetch_brand_query);
                    $fetch_brand_sql->execute();
                    while ($show_brand = $fetch_brand_sql->fetch(PDO::FETCH_ASSOC)) {
                        if ($brand_id == $show_brand["id"]) {
                          echo "<option selected value='".$show_brand['id']."'>".$show_brand['brand']."</option>";
                        } else {
                          echo "<option value='".$show_brand['id']."'>".$show_brand['brand']."</option>";
                        }
                    }
                ?>

              </select>
              <span><?php echo "$error__2"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Product Name:</label>
              <input type="text" class="form-control"  name="productName" placeholder="Enter product name" value="<?php echo $show["name"]; ?>">
              <span><?php echo "$error__3"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Product Price:</label>
              <input type="text" class="form-control"  name="productPrice" placeholder="Enter product price" value="<?php echo $show["price"]; ?>">
              <span><?php echo "$error__4"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Image:</label>
              <input type="file" class="form-control"  name="productImage">
              <img src="./img/<?php echo $show["image"]; ?>" width="150px" class="mt-3">
              <span><?php echo "$error__5"; ?></span>
            </div>
            <div class="form-group">
              <label>Short Description: </label>
              <textarea name="short_desc" cols="41" rows="3"><?php echo $show["short_desc"]; ?></textarea>
              <span><?php echo "$error__6"; ?></span>
            </div>
            <div class="form-group">
              <label>Description: </label>
              <textarea name="desc" cols="41" rows="3"><?php echo $show["description"]; ?></textarea>
              <span><?php echo "$error__7"; ?></span>
            </div>
            <button type="submit" name="submit" class="btn-block btn btn-success">Submit</button>
            <a href="./index.php" class="btn btn-dark btn-block">Go Back</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once "./inc/footer.php"; ?>