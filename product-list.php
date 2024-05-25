<?php
require_once('config/index.php');
include('header.php');
redirectToLoginIfNotLoggedIn();
$errorMessage = getErrorMessage();
if (!empty($errorMessage)) {
    echo '<div style="color: red; font-weight: bold;">' . $errorMessage . '</div>';
    // Clear the error message after displaying
    unset($_SESSION['error_message']);
}
require_once('config/index.php');

if (isset($_GET['del']) && isset($_GET['ID'])) {
    // Get the product ID from the URL
    $productId = $_GET['ID'];

    // Sanitize and validate the product ID
    $productId = filter_var($productId, FILTER_VALIDATE_INT);

    if ($productId !== false) {
        $productTable = 'product';
        $imageTable = 'product_images';
        $productCondition = "id = $productId";

        if (isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
            $getImageQuery = "SELECT image FROM $imageTable WHERE product_id = $productId";
            $imageResult = $db->query($getImageQuery);
            $imagesToDelete = $imageResult->fetch_all(MYSQLI_ASSOC);

            // Delete associated images from the folder
            foreach ($imagesToDelete as $image) {
                $imageFilename = $image['image'];
                $imagePath = 'Images/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete records from the product_images table
            $deleteImagesQuery = "DELETE FROM $imageTable WHERE product_id = $productId";
            $db->query($deleteImagesQuery);

            // Call the deleteData method to delete the product
            $deleteResult = $db->deleteData($productTable, $productCondition);
            if ($deleteResult) {
                // Data deleted successfully
                $_SESSION['msg'] = "Product deleted successfully.";
                $_SESSION['success'] = 1;
                // Redirect to the product list page
                header('Location: product-list.php');
                exit;
            } else {
                // Error in data deletion
                echo "<script>alert('Error deleting product.'); window.location.replace('product-list.php');</script>";
            }
        }
    } else {
        // Invalid product ID
        // echo "<script>alert('Invalid product ID.'); window.location.replace('product-list.php');</script>";
    }
} else {
    // Invalid request
    // echo "<script>alert('Invalid request.'); window.location.replace('product-list.php');</script>";
}
displaySessionAlert();


$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Set your desired limit per page
$where = [];
$order = 'ASC';
$orderBy = 'DESC';

$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$searchProductCode = isset($_GET['search_product_code']) ? $_GET['search_product_code'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchCategory = isset($_GET['search_category']) ? $_GET['search_category'] : '';
$startPrice = isset($_GET['start_price']) ? $_GET['start_price'] : null;
$endPrice = isset($_GET['end_price']) ? $_GET['end_price'] : null;

// Set up filter conditions array

if (!empty($startPrice) && !empty($endPrice)) {
    $where[] = "price BETWEEN $startPrice AND $endPrice";
} elseif (!empty($startPrice)) {
    $where[] = "price >= $startPrice";
} elseif (!empty($endPrice)) {
    $where[] = "price <= $endPrice";
}
// var_dump($startPrice)."<br>";
// die();

if (!empty($searchName)) {
    $where[] = "product.name LIKE '%$searchName%'";
}


if (!empty($searchProductCode)) {
    $where[] = "product_code = '$searchProductCode'";
}

if (!empty($statusFilter)) {
    $where[] = "product.status = '$statusFilter'";
}

if (!empty($searchCategory)) {
    $where[] = "product.id IN (
        SELECT pc.product_id                                               
        FROM product_category pc 
        JOIN category c ON pc.category_id = c.id 
        WHERE c.name LIKE '%$searchCategory%'
    )";
}
$validColumns = ['name', 'ordering', 'status', 'sale_price', 'price', 'Quantity', 'Sale price'];
$orderBy = (isset($_GET['orderby']) && in_array($_GET['orderby'], $validColumns)) ? $_GET['orderby'] : 'name';
$order = (isset($_GET['order']) && $_GET['order'] === 'DESC') ? 'DESC' : 'ASC';
$whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
if (!empty($where)) {
    $whereClause .= " ORDER BY $orderBy $order";
} else {
    // If there are no filters, include the ordering conditions in the normal way
    $offset = ($currentPage - 1) * $limit;
    $whereClause .= " ORDER BY $orderBy $order LIMIT $limit OFFSET $offset";
}

$countResult = $db->select('COUNT(*) as total', 'product', $where ? ' WHERE ' . implode(' AND ', $where) : '');
// print_r($countResult);
// die();
$totalPages = !empty($countResult) && isset($countResult[0]['total']) ? $countResult[0]['total'] : 0;
$result = $db->select('*', 'product', $whereClause);


$queryParams = array();

// Add sorting parameters to the query parameters
if (isset($_GET['orderby'])) {
    $queryParams['orderby'] = $_GET['orderby'];
    $queryParams['order'] = $_GET['order'];
}
if (isset($_GET['status'])) {
    $queryParams['status'] = $_GET['status'];
}
if (isset($_GET['start_price'])) {
    $queryParams['start_price'] = $_GET['start_price'];
}
if (isset($_GET['end_price'])) {
    $queryParams['end_price'] = $_GET['end_price'];
}
if (isset($_GET['search_name'])) {
    $queryParams['search_name'] = $_GET['search_name'];
}
if (isset($_GET['search_product_code'])) {
    $queryParams['search_product_code'] = $_GET['search_product_code'];
}
if (isset($_GET['search_category'])) {
    $queryParams['search_category'] = $_GET['search_category'];
}

// print_r($_GET);
// die();
$queryString = http_build_query($queryParams);
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php 
include_once('link.php');
?>
    <script>
        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                // If the user confirms, redirect to the delete confirmation page
                window.location.href = 'product-list.php?del=true&ID=' + productId + '&confirm=true';
            }
        }
    </script>

</head>

<body>
    <div class="row filter_row">
        <div class="col">
            <div class="card mt-2">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="mb-0">Filter Items</h5>
                        </div>
                        <div class="col-4 text-right">
                            <a href="product-add.php?type=add" class="btn btn-info">Add New Product</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" class="filterForm" id="filterForm" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_price" class="form-label">Start Price</label>
                                <input type="text" name="start_price" value="<?php echo isset($_GET['start_price']) ? $_GET['start_price'] : ''; ?>" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="end_price" class="form-label">End Price</label>
                                <input type="text" name="end_price" value="<?php echo isset($_GET['end_price']) ? $_GET['end_price'] : ''; ?>" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label for="search" class="form-label">Search by Name</label>
                                <div class="input-group">
                                    <input type="text" name="search_name" value="<?php echo $searchName; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="product_code" class="form-label">Search by Product Code</label>
                                <div class="input-group">
                                    <input type="text" name="search_product_code" value="<?php echo $searchProductCode; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-top: 15px;">
                                <select name="search_category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php
                                    // Fetch all category names from the database
                                    $categoryQuery = "SELECT name FROM category";
                                    $categoryResult = $db->query($categoryQuery);

                                    if (!empty($categoryResult)) {
                                        foreach ($categoryResult as $categoryData) {
                                            $selected = ($searchCategory === $categoryData['name']) ? 'selected' : '';
                                            echo "<option value='{$categoryData['name']}' $selected>{$categoryData['name']}</option>";
                                        }
                                    }

                                    ?>
                                </select>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="status" class="sr-only">Status:</label>
                                <select class="form-control" name="status">
                                    <option value="" <?php echo empty($_GET['status']) ? 'selected' : ''; ?>>All</option>
                                    <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-success px-4">Filter</button>
                                <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">Clear</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product image</th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=name&order=<?= ($orderBy == 'name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    name &nbsp; <?= ($orderBy == 'name') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">Product_code</th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=price&order=<?= ($orderBy == 'price' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    price &nbsp; <?= ($orderBy == 'price') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=sale_price&order=<?= ($orderBy == 'sale_price' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    sale_price &nbsp; <?= ($orderBy == 'sale_price') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=Quantity&order=<?= ($orderBy == 'Quantity' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    Quantity &nbsp; <?= ($orderBy == 'Quantity') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>

                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=ordering&order=<?= ($orderBy == 'ordering' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    ordering &nbsp; <?= ($orderBy == 'ordering') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=status&order=<?= ($orderBy == 'status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    status &nbsp; <?= ($orderBy == 'status') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">Category</th>
                            <th scope="col">Created at</th>
                            <th scope="col">Updated at</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($totalPages > 0) {
                            if (!empty($result)) {
                                foreach ($result as $row) {
                        ?>
                                    <tr>
                                        <th scope="row"><?php echo $row['id']; ?></th>
                                        <td>
                                            <?php
                                            // Display the main image with flag is 1
                                            $mainImageQuery = "SELECT image FROM product_images WHERE product_id = '{$row['id']}' AND is_main_image = 1";
                                            $mainImageResult = $db->query($mainImageQuery);

                                            if ($mainImageResult) {
                                                $mainImage = $mainImageResult->fetch_assoc();

                                                if ($mainImage) {
                                                    $imagePath = 'Images/' . $mainImage['image'];

                                                    if (file_exists($imagePath)) {
                                                        echo "<img src='{$imagePath}' class='box-img' height='100' alt='Main Image'>";
                                                    } else {
                                                        echo "Image file does not exist: {$imagePath}";
                                                    }
                                                } else {
                                                    echo "No main image found for product ID: {$row['id']}";
                                                }
                                            } else {
                                                echo "Error retrieving main image";
                                            }
                                            ?>
                                        </td>

                                        <?php

                                        $categoryQuery = "SELECT category_id FROM product_category WHERE product_id = '{$row['id']}'";
                                        $categoryResult = $db->query($categoryQuery);
                                        $categories = array();

                                        if ($categoryResult) {
                                            foreach ($categoryResult as $categoryData) {
                                                $categories[] = $categoryData['category_id'];
                                            }
                                        }

                                        // Now $categories is an array containing all category IDs associated with the product
                                        ?>

                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['product_code']; ?></td>
                                        <td><?php echo $row['price']; ?></td>
                                        <td><?php echo $row['sale_price']; ?></td>
                                        <td><?php echo $row['quantity']; ?></td>
                                        <td><?php echo $row['ordering']; ?></td>
                                        <td><?php echo $row['status']; ?></td>

                                        <?php
                                        $categoryQuery = "SELECT c.name 
                                 FROM category c 
                                 JOIN product_category pc ON c.id = pc.category_id 
                                 WHERE pc.product_id = '{$row['id']}'";

                                        $categoryResult = $db->query($categoryQuery);
                                        $categories = array();

                                        if ($categoryResult) {
                                            foreach ($categoryResult as $categoryData) {
                                                $categories[] = $categoryData['name'];
                                            }

                                            $categoryNames = !empty($categories) ? implode(', ', $categories) : 'N/A';
                                        } else {
                                            $categoryNames = 'Error retrieving categories';
                                        }
                                        ?>
                                        <!-- Display category names -->
                                        <td><?php echo $categoryNames; ?></td>
                                        <td><?php echo $row['created_at']; ?></td>
                                        <td><?php echo $row['updated_at']; ?></td>
                                        <td style="padding: 10px; text-align: center;">
                                            <!-- Edit and delete buttons -->
                                            <a href="product-edit.php?type=edit&id=<?php echo htmlentities($row['id']); ?>" class="btn btn-sm btn-primary" style="width: 80px; margin-right: 5px;">
                                                Edit
                                            </a>
                                            <button class="btn btn-sm btn-danger" style="width: 80px;" onclick="confirmDelete(<?php echo $row["id"]; ?>)">
                                                Delete
                                            </button>
                                        </td>

                                    </tr>
                        <?php
                                }
                            }
                        } else {
                            echo "<tr><td colspan='5'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <div class="pagination justify-content-center">
        <?php echo pagination('product-list.php', $totalPages, $currentPage, $limit, $queryParams); ?>
    </div>
</body>

</html>
<script>
    function clearFilters() {
        document.getElementById("filterForm").reset(); // Clear form fields

        // Remove filter parameters from the URL
        var urlParams = new URLSearchParams(window.location.search);
        urlParams.delete('start_price');
        urlParams.delete('end_price');
        urlParams.delete('search_name');
        urlParams.delete('search_product_code');
        urlParams.delete('status');
        urlParams.delete('search_category');

        urlParams.delete('page');

        // Redirect to the main page with cleared filters
        window.location.href = 'product-list.php?' + urlParams.toString();
    }
</script>