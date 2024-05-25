<?php
require_once('config/index.php');
checkSession();
// Get user information from the session
getUserInfo();
redirectToLoginIfNotLoggedIn();
?>
<?php
if (isset($_GET['type']) && $_GET['type'] == 'edit') {
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
        $productId = filter_var($productId, FILTER_VALIDATE_INT);
        $imageColumns = "*"; // Assuming you want to retrieve all columns
        $imageTable = "product_images";
        $imageWhereCondition = " WHERE product_id = $productId";
        $imageResult = $db->select($imageColumns, $imageTable, $imageWhereCondition);
        $existingImages = [];

        if (!empty($imageResult)) {
            foreach ($imageResult as $row) {
                $existingImages[] = $row['image'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $editedproductId = isset($_POST['editproductId']) ? $_POST['editproductId'] : null;
            $editedproductName = isset($_POST['editproductName']) ? $_POST['editproductName'] : null;
            $editproduct_code = isset($_POST['editproduct_code']) ? $_POST['editproduct_code'] : null;
            $editproductprice = isset($_POST['editproductprice']) ? $_POST['editproductprice'] : null;
            $editproductsale_price = isset($_POST['editproductsale_price']) ? $_POST['editproductsale_price'] : null;
            $editproductquantity = isset($_POST['editproductquantity']) ? $_POST['editproductquantity'] : null;
            $editproductOrdering = isset($_POST['editproductOrdering']) ? $_POST['editproductOrdering'] : null;
            $editedproductStatus = isset($_POST['editproductStatus']) ? $_POST['editproductStatus'] : null;
            $editedproductcategories = isset($_POST['editproductcategories']) ? $_POST['editproductcategories'] : array();

            $editedproductId = $productId;

            // Delete existing category associations
            $deleteCategoriesQuery = "DELETE FROM product_category WHERE product_id = '$editedproductId'";
            $db->query($deleteCategoriesQuery);

            // Insert new category associations
            foreach ($editedproductcategories as $categoryId) {
                $insertCategoryQuery = "INSERT INTO product_category(product_id, category_id) VALUES ('$editedproductId', '$categoryId')";
                $db->query($insertCategoryQuery);
            }

            if (!empty($_FILES['newImages']['name'][0])) {
                foreach ($_FILES['newImages']['tmp_name'] as $key => $tmp_name) {
                    $newImage = strtolower($_FILES['newImages']['name'][$key]);

                    $duplicateCount = 0;
                    $duplicateCheckQuery = "SELECT COUNT(*) FROM product_images WHERE product_id = '$editedproductId' AND image ='$newImage' AND is_main_image = 0";
                    $duplicateCheckResult = $db->query($duplicateCheckQuery);
                    $duplicateCount = $duplicateCheckResult->fetch_row()[0];

                    if ($duplicateCount > 0) {
                        // Duplicate image, handle accordingly
                        $_SESSION['msg'] = 'Duplicate image is not allowed';
                        redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                        exit;
                    }

                    $_allowed = array('jpg', 'jpeg', 'png', 'pdf');
                    $tempNames = array($_FILES['newImages']['tmp_name'][$key]);
                    $fileNames = array($_FILES['newImages']['name'][$key]);

                    $result = imageValidation($tempNames, $fileNames, $_allowed);

                    if (is_array($result) && isset($result['errors'])) {
                        // Display error messages
                        foreach ($result['errors'] as $errorType => $errorMessages) {
                            foreach ($errorMessages as $errorMessage) {
                                $_SESSION['msg'] = $errorMessage;
                                redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                                exit;
                            }
                        }
                    }

                    $targetDirectory = "Images/";
                    $targetPath = $targetDirectory . $newImage;

                    if (move_uploaded_file($_FILES['newImages']['tmp_name'][$key], $targetPath)) {
                        // Insert the image information into the product_images table with flag 0
                        $insertImageQuery = "INSERT INTO product_images(product_id, image, is_main_image) VALUES ('$editedproductId', '$newImage', 0)";
                        $db->query($insertImageQuery);
                    }
                    if (in_array($newImage, $existingImages)) {
                        // Duplicate image, handle accordingly
                        $_SESSION['msg'] = 'Duplicate image is not insert';
                        redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                        exit;
                    }
                }
            }

            $newMainImage = isset($_FILES['newMainImage']['name']) ? $_FILES['newMainImage']['name'] : null;
            $newMainImage = $_FILES['newMainImage']['name'];
            $existingMainImage = !empty($existingImages) ? $existingImages[0] : null;

            // Check if a new main image is uploaded
            if (!empty($newMainImage)) {
                $targetDirectory = "Images/";

                if (!file_exists($targetDirectory)) {
                    mkdir($targetDirectory, 0777, true);
                }

                if (in_array($newMainImage, $existingImages)) {
                    $_SESSION['msg'] = 'Duplicate image is not insert';
                    redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                    exit;
                }

                $targetPath = $targetDirectory . $newMainImage;
                $_allowed = array('jpg', 'jpeg', 'png');
                $tempNames = array($_FILES['newMainImage']['tmp_name']);
                $fileNames = array($_FILES['newMainImage']['name']);

                $result = imageValidation($tempNames, $fileNames, $_allowed);

                if (is_array($result) && isset($result['errors'])) {
                    // Display error messages
                    foreach ($result['errors'] as $errorType => $errorMessages) {
                        foreach ($errorMessages as $errorMessage) {
                            $_SESSION['msg'] = $errorMessage;
                            redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                            exit;
                        }
                    }
                }

                if (move_uploaded_file($_FILES['newMainImage']['tmp_name'], $targetPath)) {
                    // Update the main image without removing existing images
                    $oldMainImagePath =  $existingMainImage;

                    if (file_exists($oldMainImagePath)) {
                        unlink($oldMainImagePath);
                    }
                    var_dump($oldMainImagePath, file_exists($oldMainImagePath));
// die();
                    $updateMainImageQuery = "UPDATE product_images SET image = '$targetPath', is_main_image = 1 WHERE product_id = '$editedproductId' AND image = '$existingMainImage'";
                    $db->query($updateMainImageQuery);
                }
            }

            $tbl = 'product';
            $updateData = array(
                'name' => $editedproductName,
                'product_code' => $editproduct_code,
                'price' => $editproductprice,
                'sale_price' => $editproductsale_price,
                'quantity' => $editproductquantity,
                'ordering' => $editproductOrdering,
                'status' => $editedproductStatus
            );

            $whereCondition = array('id' => $editedproductId);
            $result = $db->updateData($tbl, $updateData, $whereCondition);

            $result = true;
            if ($result) {
                $_SESSION['msg'] = "Product Updated Successfully...";
                $_SESSION['success'] = 1;
                redirectToWithMeta('product-edit.php?type=edit&id=' . $productId);
                exit;
            } else {
                // echo "<script>ErrorAlert()</script>";
            }
        }

        if ($productId !== false) {
            $categories = $db->select("*", "category");
            $productId = $productId;
            $selectedCategoryIds = $db->select("category_id", "product_category", " WHERE product_id = $productId");
            $editedproductcategories = array_column($selectedCategoryIds, 'category_id');

            $tbl = 'product';
            $condition = "id = $productId";
            $query = "SELECT * FROM $tbl WHERE $condition";

            $result = $db->query($query);
            $images = array();

            if ($result->num_rows > 0) {
                $product = $db->select("*", $tbl, " WHERE $condition");
                if (!empty($product)) {
                    $images = array();

                    $product = $product[0];
                    $editproductId = $product['id'];
                    $editproductName = $product['name'];
                    $editproduct_code = $product['product_code'];
                    $editproductprice = $product['price'];
                    $editproductsale_price = $product['sale_price'];
                    $editproductquantity = $product['quantity'];
                    $editproductOrdering = $product['ordering'];
                    $editproductStatus = $product['status'];

                    $editedproductId = isset($_POST['editproductId']) ? $_POST['editproductId'] : null;
                    $editedproductcategories = isset($_POST['editproductcategories']) ? $_POST['editproductcategories'] : array();

                    // Remove existing associations in product_category table
                    $deleteCategoriesQuery = "DELETE FROM product_category WHERE product_id = '$editedproductId'";
                    $db->query($deleteCategoriesQuery);

                    // Insert selected categories into product_category table
                    foreach ($editedproductcategories as $categoryId) {
                        $insertCategoryQuery = "INSERT INTO product_category(product_id, category_id) VALUES ('$editedproductId', '$categoryId')";
                        $db->query($insertCategoryQuery);
                    }

                    // Fetch all images associated with the product from product_images table
                    $images = $db->select("*", "product_images", " WHERE product_id = '$productId'");

                    // If you want to structure the result array with specific keys
                    $images = array_map(function ($image) {
                        return [
                            'id' => $image['id'],
                            'name' => $image['image']
                        ];
                    }, $images);
                }
            }
        } else {
            // Invalid category ID.
        }
    } else {
        // No data found for the specified ID.
    }
}
 elseif (isset($_GET['productId']) && isset($_GET['deleteid'])) {
    $productId = $_GET['productId'];
    $imageId = $_GET['deleteid'];

    // Fetch image information from the database
    $getImageQuery = "SELECT id, image FROM product_images WHERE product_id = '$productId' AND id = '$imageId'";
    $imageResult = $db->select('id, image', 'product_images', " WHERE product_id = '$productId' AND id = '$imageId'");

    if ($imageResult) {
        if (!empty($imageResult)) {
            $imageData = $imageResult[0]; // Assuming you expect only one row
            $imagePath = "Images/" . $imageData['image'];
            // Delete the image record from the database
            $deleteQuery = "DELETE FROM product_images WHERE product_id = '$productId' AND id = '$imageId'";
            $result = $db->query($deleteQuery);

            if ($result) {
                // Delete the actual image file
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                // Redirect back to the product-edit page
                header("Location: product-edit.php?type=edit&id=$productId");
                $_SESSION['msg'] = "Image deleted successfully.";
                exit;
            } else {
                // Handle the error (you can customize this)
                echo 'Error deleting image record';
            }
        } else {
            // Handle the case where the image record is not found
            echo 'Image record not found';
        }
    } else {
        // Handle the case where there is an issue with the query
        echo 'Error executing query';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include_once('link.php');
    ?>
    <style>
        #output_image {
            max-width: 200px;
        }

        .pagination-link {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            color: #333;
        }

        .pagination-link.active {
            background-color: #007bff;
            color: #fff;
        }
    </style>
    <script>
        function imageDeleteAlert() {
            Swal.fire({
                title: 'Image Deleted!',
                text: 'The image has been successfully deleted.',
                icon: 'success',
                showConfirmButton: false,
                timer: 5000 // Automatically close after5 seconds
            });
        }

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('selected_image');
                output.src = reader.result;
                output.style.display = 'block'; // Show the image preview
            }

        }
    </script>
</head>

<body>

    <div class=" p-5">
        <h3>Edit Product</h3>
        <hr>

        <form method="post" enctype="multipart/form-data">
            <?php
            if (!empty($_SESSION['msg'])) {
                if (!empty($_SESSION['msg'])) {
                    // Check if it's a success message (1) or an error message (0)
                    $success = !empty($_SESSION['success']) ? true : false;

                    // Display the session message using the function
                    displaySessionMessage($_SESSION['msg'], $success);

                    // Unset the session variables after displaying their values
                    unset($_SESSION['msg']);
                    unset($_SESSION['success']);
                }
            }
            ?>
            <div class="form-group">
                <input type="hidden" name="editproductId" value="<?php echo $editedproductId; ?>">
                <label for="exampleFormControlInput1">Product Name</label>
                <input type="text" name="editproductName" class="form-control" value="<?php echo htmlspecialchars($editproductName); ?>" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Code</label>
                <input type="text" name="editproduct_code" class="form-control" id="exampleFormControlInput1" value="<?php echo isset($editproduct_code) ? $editproduct_code : ''; ?>" placeholder="" required>
            </div>

            <!-- Main Image -->
            <!-- Main Image -->
<div class="form-group">
    <label for="mainImage">Main Image:</label>
    <?php if (!empty($images)) : ?>
        <?php $mainImage = reset($images); ?>
        <!-- Display main image with delete functionality -->
        <div class="image-container">
            <img src="Images/<?php echo $mainImage['name']; ?>" alt="Main Product Image" style="width: 200px; height: 200px;">
        </div>
        <input type="hidden" name="existingMainImage" value="<?php echo $mainImage['name']; ?>">
    <?php else : ?>
        <p>No main image available.</p>
    <?php endif; ?>
</div>

<!-- Add upload field for a new main image -->
<div class="form-group">
    <label for="newMainImage">Upload New Main Image:</label>
    <input type="file" name="newMainImage" class="form-control" accept="image/*">
</div>

<div class="form-group">
    <label>Other Images:</label>
    <div class="other-images-container">
        <?php if (!empty($images)) : ?>
            <?php foreach ($images as $image) : ?>
                <?php if ($image['name'] !== $mainImage['name']) : ?>
                    <!-- Display other images with delete functionality -->
                    <div class="image-container">
                        <img src="Images/<?php echo $image['name']; ?>" alt="Product Image" style="width: 100px; height: 100px; margin-right: 10px;">
                        <button type="button" class="btn btn-danger"><a style="color: black;" href="product-edit.php?productId=<?php echo $productId; ?>&deleteid=<?php echo $image['id']; ?>">Delete</a></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No images available.</p>
        <?php endif; ?>
    </div>
</div>

            <!-- Add New Images -->
            <div class="form-group">
                <label for="editProductImage">Add New Images:</label>
                <input type="file" name="newImages[]" class="form-control" id="editProductImage" multiple>
                <?php if (isset($duplicateImageError) && $duplicateImageError) : ?>
                    <p style="color: red;">Cannot add duplicate images.</p>
                <?php endif; ?>
            </div>
            <?php
            $categoriesQuery = "SELECT * FROM category";
            // print_r($categoriesQuery);
            $categoriesResult = $db->query($categoriesQuery);
            // print_r($categoriesResult);
            $allCategories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
            // print_r($allCategories);
            ?>
            <div class="form-group">
                <label for="editproductcategories">Categories</label>
                <select class="form-control" multiple name="editproductcategories[]" id="editproductcategories">
                    <?php
                    // Assuming $productId is the ID of the current product
                    $selectedCategoriesQuery = "SELECT category_id FROM product_category WHERE product_id = $productId";
                    $selectedCategoriesResult = $db->query($selectedCategoriesQuery);
                    $selectedCategoryIds = $selectedCategoriesResult->fetch_all(MYSQLI_ASSOC);

                    // Extract category IDs into an array
                    $editedproductcategories = array_column($selectedCategoryIds, 'category_id');
                    print_r($editedproductcategories);

                    foreach ($categories as $category) {
                        $categoryId = $category['id'];
                        $categoryName = $category['name'];

                        // Check if the category is selected for the current product
                        $selected = in_array($categoryId, $editedproductcategories) ? 'selected' : '';

                        echo "<option value=\"$categoryId\" $selected>$categoryName</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Price</label>
                <input type="text" name="editproductprice" class="form-control" id="exampleFormControlInput1" placeholder="" value="<?php echo $editproductprice ?>" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Sale Price</label>
                <input type="text" name="editproductsale_price" class="form-control" id="exampleFormControlInput1" value="<?php echo $editproductsale_price; ?>" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Quantity</label>
                <input type="text" name="editproductquantity" class="form-control" id="exampleFormControlInput1" placeholder="" value="<?php echo $editproductquantity; ?>" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Ordering</label>
                <input type="text" name="editproductOrdering" class="form-control" id="exampleFormControlInput1" placeholder="" value="<?php echo $editproductOrdering; ?>" required>
            </div>

            <div class="form-group">
                <label for="exampleFormControlSelect1">Product Category Status</label><br>
                <select name="editproductStatus" class="form-control">
                    <!-- <input type="hidden" name="status" value="<?php echo isset($_GET['status']) ? $_GET['status'] : ''; ?>"> -->

                    <option value="active" <?php echo ($editproductStatus == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($editproductStatus == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <input type="hidden" name="form_submitted" value="1">
                    <input type="submit" name="update" value="update" class="btn btn-success">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='product-list.php';">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</body>