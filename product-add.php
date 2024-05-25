<?php
// session_start(); // Add this line to start the session

require_once('config/index.php');
checkSession();
// Get user information from the session
getUserInfo();
redirectToLoginIfNotLoggedIn();
?>
<?php

$type = isset($_GET['type']) ? $_GET['type'] : null;
if (isset($type) && ($type == 'add')) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Initialize $productId outside the loop
        // print_r($_POST);
        // die();
        $productId = null;
        $oldImageNames = array();
        $mainImageUploaded = false;
        $oldMainImage = null;

        // Handle image uploads and insert into product_images table
        if (isset($_FILES['newImages']) && count($_FILES['newImages']['name']) > 0) {
            foreach ($_FILES['newImages']['tmp_name'] as $key => $tmp_name) {
                $file = [
                    'name' => $_FILES['newImages']['name'][$key],
                    'tmp_name' => $_FILES['newImages']['tmp_name'][$key],
                    'size' => $_FILES['newImages']['size'][$key],
                ];

                $imagePath = uploadImage($file, 'Images/', ['jpg', 'jpeg', 'png'], 'product-add.php?type=add');

                if ($imagePath) {
                    $newFileName = basename($imagePath);
                    // die();

                    // Insert data into product table only if at least one image is uploaded
                    if (!isset($productId)) {
                        $productData = array(
                            "name" => $_POST['name'],
                            "product_code" => $_POST['product_code'],
                            "price" => $_POST['price'],
                            "sale_price" => $_POST['sale_price'],
                            "quantity" => $_POST['quantity'],
                            "ordering" => $_POST['ordering'],
                            "status" => $_POST['status'],
                        );

                        $productId = $db->insertData("product", $productData);
                        // echo $productId;
                        // print_r($productData);
                        // die();

                        if ($productId) {
                            if (!empty($_POST['categories'])) {
                                foreach ($_POST['categories'] as $categoryId) {
                                    $categoryData = array(
                                        "product_id" => $productId,
                                        "category_id" => $categoryId,
                                    );
                                    $db->insertData("product_category", $categoryData);
                                }
                            }
                        } else {
                            echo "<script>ErrorAlert()</script>";
                            die();
                        }
                    }

                    $isMainImage = ($key == 0) ? 1 : 0;
                    $imageData = array(
                        "product_id" => $productId,
                        "image" => $newFileName,
                        "is_main_image" => $isMainImage,
                    );

                    $imageId = $db->insertData("product_images", $imageData);
                    if (!$imageId) {
                        // Handle error in inserting image information
                        echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Uploading File',
                            text: 'Error inserting image information into product_images table.',
                            showConfirmButton: true
                        });
                        </script>";
                        die();
                    }

                    if ($isMainImage) {
                        $mainImageUploaded = true;
                        $oldMainImage = $newFileName;
                        // die();
                    }
                } else {
                    // Handle the error
                    // Redirect is handled in uploadImage function
                    exit;
                }
            }
            $productId = true;
            if ($productId) {

                $_SESSION['msg'] = "Product Add Successfully...";
                $_SESSION['success'] = 1;
                redirectToWithMeta('product-list.php');
                exit;
            }
        }
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
    <div class="p-5">
        <h3>Add New Product</h3>
        <hr>
        <form method="post" id="myForm" enctype="multipart/form-data" onsubmit="return validateForm()">
            <?php
            if (!empty($_SESSION['msg'])) {
                // Check if it's a success message (1) or an error message (0)
                $success = !empty($_SESSION['success']) ? true : false;

                // Display the session message using the function
                displaySessionMessage($_SESSION['msg'], $success);

                // Unset the session variables after displaying their values
                unset($_SESSION['msg']);
                unset($_SESSION['success']);
            }
            ?>
            <div class="form-group">
                <!-- <input type="hidden" name="product_id" value="<?php echo $editProductId; ?>"> -->
                <label for="exampleFormControlInput1">Product Name</label>
                <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Code</label>
                <input type="text" name="product_code" id="productCode" class="form-control" placeholder="" readonly required>
            </div>

            <div class="form-group">
                <label for="imgInp">Product Images</label>
                <input type="file" class="form-control" id="imgInp" name="newImages[]" accept="image/*" onchange="preview_images(event)" placeholder="" required multiple>
                <div id="imagePreviews"></div>
            </div>

            <?php if (!empty($_FILES['newImages']['name'])) : ?>
                <div>
                    <label>Selected Images:</label>
                    <ul>
                        <?php foreach ($_FILES['newImages']['name'] as $key => $fileName) : ?>
                            <li><?php echo $fileName; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php
            $connection = mysqli_connect("localhost", "root", "", "management");
            $query = "SELECT * FROM category";
            $query_run = mysqli_query($connection, $query);
            $result = $query_run;
            ?>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Product Category</label>
                <select class="form-control" name="categories[]" id="exampleFormControlSelect1" multiple required>
                    <option>Select</option>
                    <?php
                    if (mysqli_num_rows($query_run) > 0) {
                        while ($row = mysqli_fetch_assoc($query_run)) {
                    ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php
                        }
                    } else {
                        echo "<option value='0'>No Category</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Price</label>
                <input type="text" name="price" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Sale Price</label>
                <input type="text" name="sale_price" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Quantity</label>
                <input type="text" name="quantity" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Product Ordering</label>
                <input type="text" name="ordering" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>

            <div class="form-group">
                <label for="exampleFormControlSelect1">Product Status</label>
                <input type="hidden" name="status_hidden" value="<?php echo isset($_GET['status']) ? $_GET['status'] : ''; ?>">
                <select class="form-control" name="status" id="exampleFormControlSelect1" required>
                    <option value="">Select</option>
                    <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <input type="hidden" name="form_submitted" value="1">

                    <input type="submit" name="submit" value="submit" class="btn btn-success">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='product-list.php';">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        function customResetForm() {
            var formElement = document.getElementById('myForm');
            formElement.reset();
        }
    </script>
    <script>
        function preview_images(event) {
            var previewContainer = document.getElementById('imagePreviews');
            previewContainer.innerHTML = ''; // Clear previous previews

            for (var i = 0; i < event.target.files.length; i++) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px'; // Adjust as needed
                    img.style.marginRight = '5px';
                    img.style.marginBottom = '5px';
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(event.target.files[i]);
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            generateProductCode(); // Call the function when the page loads
        });

        function generateProductCode() {
            // Generate a random 6-digit product code
            var randomCode = Math.floor(100 + Math.random() * 9000);

            // Set the generated code to the "Product Code" input field
            document.getElementById('productCode').value = randomCode;
        }
    </script>
</body>

</html>