<?php
require_once('config/index.php');
checkSession();
// Get user information from the session
getUserInfo();
redirectToLoginIfNotLoggedIn();
?>
<?php
// $editCategoryName = '';
if (isset($_GET['type']) && $_GET['type'] == 'edit') {
  
if (isset($_GET['id'])) {
    $categoryID = $_GET['id'];
    $categoryID = filter_var($categoryID, FILTER_VALIDATE_INT);

    // Fetch the category based on the ID
    $tbl = 'category';
    $columns = '*';
    $condition = " WHERE id = $categoryID";
    // Call the select function
    $result = $db->select($columns, $tbl, $condition);

    if (!empty($result)) {
        // Loop through the result using foreach
        foreach ($result as $category) {
            $editCategoryId = $category['id'];
            $editCategoryName = $category['name'];
            $editCategoryOrdering = $category['ordering'];
            $editCategoryImage = $category['image'];
            $editCategoryStatus = $category['status'];
            $oldImageName = $category['image']; // Store the old image name
            // echo $oldImageName;
               // die();
            $imageName = $category['image'];
            $file_path = "Images/$imageName";
            $fullImagePath = $file_path;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $editedCategoryId = isset($_POST['editCategoryId']) ? $_POST['editCategoryId'] : null;
                $editedCategoryName = isset($_POST['editCategoryName']) ? $_POST['editCategoryName'] : null;
                $editedCategoryOrdering = isset($_POST['editCategoryOrdering']) ? $_POST['editCategoryOrdering'] : null;
                $editedCategoryStatus = isset($_POST['editCategoryStatus']) ? ($_POST['editCategoryStatus'] == 'active' ? 'active' : 'inactive') : 'inactive';

                // Check if the category name already exists (excluding the current category)
                $existingCategoryQuery = "SELECT id FROM category WHERE name = '{$editedCategoryName}' AND id != '{$editedCategoryId}'";
                $existingCategoryResult = $db->query($existingCategoryQuery);

                if ($existingCategoryResult->num_rows > 0) {
                    $_SESSION['msg'] = "Same name category already exists. Add a different category.";
                    redirectToWithMeta('category-edit.php?type=edit&id=' . $categoryID);
                    exit;
                }

                if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === 0) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];

                    // Use uploadImage function to handle image uploads and validations
                    $imagePath = uploadImage($_FILES['newImage'], 'Images/', $allowedExtensions, 'category-edit.php?type=edit&id=' . $categoryID, $fullImagePath, true);

                    // Update the database with the new image name
                    $updateData = array('image' => $imagePath);
                    $whereCondition = array('id' => $editedCategoryId);
                    $db->updateData($tbl, $updateData, $whereCondition);
                }

                // Update other fields in the database
                $updateData = array(
                    'name' => $editedCategoryName,
                    'ordering' => $editedCategoryOrdering,
                    'status' => $editedCategoryStatus
                );

                $whereCondition = array('id' => $editedCategoryId);
                $result = $db->updateData($tbl, $updateData, $whereCondition);

                if ($result) {
                    // Data updated successfully
                    $_SESSION['msg'] = "Category Updated Successfully...";
                    $_SESSION['success'] = 1;
                    redirectToWithMeta('category-edit.php?type=edit&id=' . $categoryID);
                    exit;
                } else {
                    // Handle error updating category
                    // showAlert("Error updating category.", 'danger');
                }
            }
        }
    } else {
        echo "No data found for the specified ID.";
    }
} else {
    echo "Invalid category ID.";
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
        .file-container {
            display: flex;
            padding-top: 10px;
            flex-direction: column;
        }

        .file-container input {
            margin-top: 10px;
        }

        #output_image {
            max-width: 200px;
        }
    </style>
    <script>
        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('categoryImage');
                if (output) {
                    output.src = reader.result;
                    output.style.maxWidth = '200px';
                    output.style.maxHeight = '200px';
                } else {
                    console.error("Element with ID 'categoryImage' not found.");
                }
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>

<body>
    <div class=" p-5">
        <h3>Edit Category</h3>
        <hr>
        <form method="post" enctype="multipart/form-data">
            <?php
            //  if (!empty($_SESSION['msg'])) {
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
                <input type="hidden" name="editCategoryId" value="<?php echo $editCategoryId; ?>">

                <input type="text" name="editCategoryName" class="form-control" id="exampleFormControlInput1" placeholder="" value="<?php echo htmlspecialchars($editCategoryName); ?>" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Category Ordering</label>
                <input type="text" name="editCategoryOrdering" class="form-control" id="exampleFormControlInput1" value="<?php echo htmlspecialchars($editCategoryOrdering); ?>" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Category Image</label><br>

                <!-- Display the current image -->
                <img src="<?php echo htmlspecialchars($editCategoryImage); ?>" alt="Current Image" style="max-width: 200px;">

                <!-- Add a container for styling -->
                <div class="file-container">
                    <input type="hidden" name="editCategoryImage" value="<?php echo htmlspecialchars($editCategoryImage); ?>">
                    <input type="file" class="form-control" name="newImage" accept="image/*" accept=".jpg, .jpeg, .png" onchange="preview_image(event);">
                    <div>
                        <img id="selected_image" style="max-width: 200px; display: none;">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="exampleFormControlSelect1">Product Category Status</label><br>
                <select name="editCategoryStatus" class="form-control">
                    <option value="active" <?php echo ($editCategoryStatus == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($editCategoryStatus == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <input type="hidden" name="form_submitted" value="1">
                    <input type="submit" name="update" value="update" class="btn btn-success">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='category-list.php';">Cancel</button>

                </div>
            </div>
        </form>
    </div>
</body>

</html>