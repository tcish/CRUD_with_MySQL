<?php
  require_once "./connect/connection.php";
  require_once "./inc/header.php";

  $error__1 = NULL;
  $error__2 = NULL;
  $error__3 = NULL;
  $error__4 = NULL;
  $error__5 = NULL;
  $error__6 = NULL;
  $error__7 = NULL;

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
    if (empty($pImage_name)) {
      $error__5 = "<h6 style='color: red;'>Please Select an Image!</h6>";
    }
    if (empty($pShort_desc)) {
      $error__6 = "<h6 style='color: red;'>Please Enter Short Description!</h6>";
    }
    if (empty($pLong_desc)) {
      $error__7 = "<h6 style='color: red;'>Please Enter Product Description!</h6>";
    }

    // check if product already exist
    $query = "SELECT * FROM products WHERE name = '$pName'";
    $sql = $connection->prepare($query);
    $sql->execute();
    $fetch = $sql->fetch(PDO::FETCH_ASSOC);

    if (!empty($pCategories) && !empty($pBrand) && !empty($pName) && !empty($pPrice) && !empty($pImage_name) && !empty($pShort_desc) && !empty($pLong_desc)) {
      if ($pImage_size > 4000096) {
        $error__5 = "<h6 style='color:red;'>Your Image should less than 4MB !</h6>";
      }elseif (!in_array($lowercase_img_ext_name, $allow_format)) {
        $error__5 = "<h6 style='color:red;'>Only JPG, JPEG, PNG, files are allowed!</h6>";
      }else {
        if ($fetch["name"] != $pName) {
          $insert_query = "INSERT INTO products(categories_id, brand_id, name, price, image, short_desc, description) VALUES(:categories_id, :brand_id, :name, :price, :image, :short_desc, :description)";
          $insert_sql= $connection->prepare($insert_query);
          $insert_sql->bindParam(":categories_id", $pCategories);
          $insert_sql->bindParam(":brand_id", $pBrand);
          $insert_sql->bindParam(":name", $pName);
          $insert_sql->bindParam(":price", $pPrice);
          $insert_sql->bindParam(":image", $unique_name_generate);
          $insert_sql->bindParam(":short_desc", $pShort_desc);
          $insert_sql->bindParam(":description", $pLong_desc);
          $insert_sql->execute();
          
          move_uploaded_file($pImage_temp_location, $image_directory.$unique_name_generate);
  
          header("Location: index.php");
        }else {
          $error__3 = "<h6 style='color:red;'>Product Already Exist!</h6>";
        }
      }
    }
  }
?>
<div class="jumbotron m-0">
  <div class="container">
    <div class="row">
      <div class="col-12 col-sm-8 col-md-6 col-lg-5 offset-sm-2 offset-md-3 offset-lg-3">
        <div class="shadow-lg p-4 bg-dark text-white">
          <h1 class="text-center mb-3 pb-1 custom__border text-info">Product Entry Form</h1>
          <form autocomplete="off" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="productCategories" class="font-weight-bold">Select Categories:</label>
              <select name="productCategories" class="form-control">
                <option value="0">Select Categories</option>

                <?php
                  $cat_query = "SELECT * FROM categories ORDER BY categories ASC";
                  $cat_sql = $connection->prepare($cat_query);
                  $cat_sql->execute();
                  while ($cat_fetch = $cat_sql->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='".$cat_fetch['id']."'>".$cat_fetch['categories']."</option>";
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
                  $brand_query = "SELECT * FROM brands ORDER BY brand ASC";
                  $brand_sql = $connection->prepare($brand_query);
                  $brand_sql->execute();
                  while ($brand_fetch = $brand_sql->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='".$brand_fetch['id']."'>".$brand_fetch['brand']."</option>";
                  }
                ?>
                
              </select>
              <span><?php echo "$error__2"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Product Name:</label>
              <input type="text" class="form-control"  name="productName" placeholder="Enter product name">
              <span><?php echo "$error__3"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Product Price:</label>
              <input type="text" class="form-control"  name="productPrice" placeholder="Enter product price">
              <span><?php echo "$error__4"; ?></span>
            </div>
            <div class="form-group">
              <label class="font-weight-bold" for="">Image:</label>
              <input type="file" class="form-control"  name="productImage">
              <span><?php echo "$error__5"; ?></span>
            </div>
            <div class="form-group">
              <label>Short Description: </label>
              <textarea name="short_desc" cols="41" rows="3"></textarea>
              <span><?php echo "$error__6"; ?></span>
            </div>
            <div class="form-group">
              <label>Description: </label>
              <textarea name="desc" cols="41" rows="3"></textarea>
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