<?php

function uploadImage($file, $targetDir, $allowedExtensions, $redirectUrl, $oldImagePath = null) {
    $imageName = $file['name'];
    $timestamp = time();
    $imageName = $timestamp . '_' . $imageName;

    $imagePath = $targetDir . $imageName;
    echo $imagePath;
    // die();
    $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['msg'] = 'Please insert JPG, JPEG, and PNG format valid image format';
        redirectToWithMeta($redirectUrl);
        exit;
    }

    $imageSize = getimagesize($file['tmp_name']);
    if ($imageSize === false) {
        echo "<script>alert('Error getting image size.')</script>";
        exit();
    }

    list($width, $height) = $imageSize;
    if ($width > 500 || $height > 500) {
        $_SESSION['msg'] = 'Height and Width must be 500x500 or less...';
        redirectToWithMeta($redirectUrl);
        exit;
    }

    if (move_uploaded_file($file['tmp_name'], $imagePath)) {
        // If it's an update (edit), remove the old image
        if ($oldImagePath !== null && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        return $imagePath;
    } else {
        echo "<script>alert('Error uploading the image.')</script>";
        exit;
    }
}
function imageValidation($tempNames, $imageNames, $_allowed)
{
    // Array to store error messages
    $errors = [];

    // Flag to check overall image validation
    $allValid = true;

    // Iterate through each image for size validation
    foreach ($tempNames as $index => $tempName) {
        list($_width, $_height) = getimagesize($tempName);
        if ($_width > 500 || $_height > 500) {
            $allValid = false;
            $errors['size'][] = "Image at index $index has invalid height or width (must be 500x500 or less).";
        }
    }

    // Check if it's a valid image type
    foreach ($imageNames as $imageName) {
        $_ext = pathinfo($imageName, PATHINFO_EXTENSION);
        if (!in_array(strtolower($_ext), $_allowed)) {
            $allValid = false;
            $errors['extension'][] = "Image '$imageName' has an invalid extension. Only JPG, JPEG, and PNG are allowed.";
        }
    }

    // If any image fails validation, return error messages
    if (!$allValid) {
        return ['errors' => $errors];
    }

    // Return true if all images are valid
    return true;
}
function pagination($page, $total_records, $current_page, $limit, $queryParams = []) {
    if ($total_records <= 0 || $limit <= 0) {
        // Avoid division by zero or negative limit
        return '';
    }

    $total_pages = ceil($total_records / $limit);
    $output = '<ul class="pagination justify-content-center">';

    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $output .= '<li class="page-item"><a class="page-link" href="' . buildPaginationLink($page, $prev_page, $queryParams) . '">&laquo; Previous</a></li>';
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $output .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $output .= '<li class="page-item"><a class="page-link" href="' . buildPaginationLink($page, $i, $queryParams) . '">' . $i . '</a></li>';
        }
    }

    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $output .= '<li class="page-item"><a class="page-link" href="' . buildPaginationLink($page, $next_page, $queryParams) . '">Next &raquo;</a></li>';
    }

    $output .= '</ul>';
    return $output;
}
function buildPaginationLink($page, $pageNumber, $queryParams) {
    $queryParams['page'] = $pageNumber;
    $queryString = http_build_query($queryParams);
    return $page . '?' . $queryString;
}
function checkSession() {
    // session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
}
function displaySessionAlert() {
    if (isset($_SESSION['msg'])) {
        $alertClass = ($_SESSION['success'] == 1) ? 'alert-success' : 'alert-danger';

        echo "<div class='alert $alertClass text-center'>{$_SESSION['msg']}</div>";

        // Clear the session variables
        unset($_SESSION['msg']);
        unset($_SESSION['success']);
    }
}
function displaySessionMessage($message, $success = true){
    $alertClass = $success ? 'alert-success' : 'alert-danger';
?>
    <div class="alert <?php echo $alertClass; ?>" style="position: fixed; top: 15px; left: 0; right: 0; width: 100%; height: 53px; font-size: medium; z-index: 1000; text-align: center;" role="alert" id="myElem">
        <?php echo $message; ?>
    </div>
<?php
}
function getUserInfo() {
    // Get user information from the session
    $userID = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    return ['userID' => $userID, 'username' => $username];
}
function redirectToLoginIfNotLoggedIn() {
    // Start session if not already started
    // session_start();

    // Check if the user is not logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to the login page with an error message
        header("Location: login.php?error=Please log in to access this page");
        exit();
    }
}
function setErrorMessage($message) {
    $_SESSION['error'] = $message;
}
function setSuccessMessage($message) {
    $_SESSION['success'] = $message;
}

function getErrorMessage($message = null) {
    if (!isset($_SESSION['error'])) {
        return $message;
    }

    $message = $_SESSION['error'];
    unset($_SESSION['error']);
    return $message;
}
function getSuccessMessage($message) {
    $message = $_SESSION['success'];
    unset($_SESSION['success']);
    return $message;
}
function redirectTo($url) {
    header('Location: ' . $url);
    exit;
}
function redirectToWithMeta($url) {
    echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
    exit;
}

function setAuthUser($user) {
    $_SESSION['user'] = $user;
}

function getAuthUser() {
    return $_SESSION['user'] ?: [];
}

function isLogin() {
    return getAuthUser() ? 1 : 0;
}

function checkLogin() {
    if (!isLogin()) {
        setErrorMessage('You must need to login');
        redirectTo('login.php');
        exit;
    }
}

function generateUniqueFileName($originalName, $existingNames) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $counter = 1;

    while (in_array($baseName . $counter . '.' . $extension, $existingNames)) {
        $counter++;
    }

    return $baseName . $counter . '.' . $extension;
}
function initializeProductVariables() {
    return [
        'editproductId' => null,
        'editproductName' => null,
        'editproduct_code' => null,
        'editproductprice' => null,
        'editproductsale_price' => null,
        'editproductquantity' => null,
        'editproductOrdering' => null,
        'editproductStatus' => null,
    ];
}

?>
