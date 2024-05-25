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
    if (isset($_POST['submit'])) {
        if (isset($_FILES['image'])) {
            $imageDir = 'Images/';

            $categoryName = $_POST['name'];
            $existingCategory = $db->sql('id', 'category', "name = '$categoryName'");

            if ($existingCategory) {
                $_SESSION['msg'] = "Category with the same name already exists. Add a different category name.";
                $redirectUrl = 'category-add.php?type=add';
                redirectToWithMeta($redirectUrl);
                exit;
            }

            $uploadedImagePath = uploadImage($_FILES['image'], $imageDir, ['jpg', 'jpeg', 'png'], 'category_add.php?type=add');

            $data = array(
                'name' => $_POST['name'],
                'Image' => $uploadedImagePath,
                'ordering' => $_POST['ordering'],
                'status' => $_POST['status'],
            );

            $tbl = 'category';
            $insertResult = $db->insertData($tbl, $data);
            $insertResult = true;

            if ($insertResult) {
                $_SESSION['msg'] = "Category added successfully.";
                $_SESSION['success'] = true;
                redirectToWithMeta('category-list.php');
                exit;
            } else {
                $_SESSION['msg'] = "Error uploading the image.";
                $_SESSION['success'] = false;
            }
        } else {
            $_SESSION['msg'] = "No image file uploaded.";
            $_SESSION['success'] = false;
        }
    }

    // include('cat_form.php');
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
    </style>
    <script>
        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                output.style.display = 'block'; // Show the image preview
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('selected_image');
                output.src = reader.result;
                output.style.display = 'block'; // Show the image preview
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function confirmDelete(categoryID) {
            var confirmDelete = confirm('Are you sure you want to delete this category?');
            if (confirmDelete) {
                // Redirect to the deletion URL
                window.location.href = 'category_list.php?del=true&id=' + categoryID + '&confirm=true';
                // window.location.href='common_category.php';
            }
        }
    </script>
</head>
<body>
    <div class=" p-5">
        <h3>Add New Category</h3>
        <hr>
        <form method="post" enctype="multipart/form-data">
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

                <label for="exampleFormControlInput1">Product Category Name</label>
                <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Category Ordering</label>
                <input type="text" name="ordering" class="form-control" id="exampleFormControlInput1" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Category Image</label>
                <input type="file" name="image" class="form-control" id="image" accept=".jpg, .jpeg, .png" onchange="preview_image(event); " accept="image/*">
                <div>
                    <img id="selected_image" style="max-width: 200px; display: none;" />
                </div>
                <img id="output_image" />
            </div>
            <?php

            ?>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Product Category Status</label>
                <select class="form-control" name="status" id="exampleFormControlSelect1" required>
                    <option value="" <?php echo empty($_POST['status']) ? 'selected' : ''; ?>>Select</option>
                    <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <input type="hidden" name="form_submitted" value="1">
                    <input type="submit" name="submit" value="submit" class="btn btn-success">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='category-list.php';">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
