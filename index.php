<?php
  require_once "./connect/connection.php";
  require_once "./inc/header.php";

  $fetch_query = "SELECT products.*, categories.categories, brands.brand FROM products, categories, brands WHERE products.categories_id = categories.id AND products.brand_id = brands.id ORDER BY products.id DESC";
  $fetch_sql = $connection->prepare($fetch_query);
  $fetch_sql->execute();

  if (isset($_GET["delete_id"]) && $_GET["delete_id"] != NULL) {
    $id = $_GET["delete_id"];
    $pic_del_query = "SELECT image FROM products WHERE id = '$id'";
    $pic_del_sql = $connection->prepare($pic_del_query);
    $pic_del_sql->execute();
    $output = $pic_del_sql->fetch(PDO::FETCH_ASSOC);
    $del_pic = $output["image"];
    unlink("img/$del_pic");

    $query = "DELETE FROM products WHERE id = '$id'";
    $sql = $connection->prepare($query);
    $sql->execute();

    header("Location: index.php");
  }
?>
<div class="jumbotron m-0 vh-100">
  <div class="container">
    <div class="row">
      <div class="col-12">
      <h1 class="float-left font-weight-bold text-white">Products</h1>
      <a href="./addProduct.php" class="btn btn-info float-right">Add Product</a>
        <table class="table table-dark mt-5 text-center table-responsive-sm" id="show">
          <tr class="text-info">
            <th>Serial</th>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Categories</th>
            <th>Price</th>
            <th>Image</th>
            <th>Short Description</th>
            <th>Description</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>

          <?php
            if ($fetch_sql->rowCount() > 0) {
              $i = 1;
              while ($show = $fetch_sql->fetch((PDO::FETCH_ASSOC))) { ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo $show["name"]; ?></td>
                  <td><?php echo $show["brand"] ?></td>
                  <td><?php echo $show["categories"]; ?></td>
                  <td><?php echo $show["price"]; ?>/-</td>
                  <td><img src="./img/<?php echo $show["image"] ?>" width="150px"></td>
                  <td><?php echo $show["short_desc"]; ?></td>
                  <td style="word-wrap: break-word;"><?php echo substr($show["description"], 0, 30); ?>...</td>
                  <td><a class="btn btn-warning" href='edit_product.php?id=<?php echo $show["id"]; ?>&cat_id=<?php echo $show["categories_id"]; ?>&brand_id=<?php echo $show["brand_id"]; ?>'>Edit</a></td>
                  <td><a class="btn btn-danger" href="?delete_id=<?php echo $show["id"]; ?>">Delete</a></td>
                </tr>
        <?php }
            }else {
              echo "<td colspan='10'><h4>No Product Added Yet!</h4></td>";
            }
          ?>

        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once "./inc/footer.php" ?>