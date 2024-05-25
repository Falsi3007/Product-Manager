<?php
require_once('config/index.php');
include('header.php');
redirectToLoginIfNotLoggedIn();

$errorMessage = getErrorMessage();
if (!empty($_GET['error'])) {
    // Display the error message
    echo '<div style="color: red; font-weight: bold;">' . htmlspecialchars($_GET['error']) . '</div>';
}

if (isset($_GET['del']) && isset($_GET['id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
    $categoryID = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($categoryID !== false) {
        $tbl = 'category';
        $condition = "id = $categoryID";

        // Fetch the image path from the database
        $imagePathQuery = "SELECT * FROM $tbl WHERE $condition";
        $imagePathResult = $db->query($imagePathQuery);

        if ($imagePathResult->num_rows > 0) {
            $category = $imagePathResult->fetch_assoc();
            $imageName = $category['image'];
            $file_path = $imageName; // Assuming the image is in the same directory

            // Check if the file exists before attempting to delete
            if (file_exists($file_path)) {
                unlink($file_path);
            } else {
                echo "File does not exist";
            }
        }

        // Now, proceed with the database deletion
        $deleteResult = $db->deleteData($tbl, $condition);

        if ($deleteResult) {
            // Database deletion successful
            $_SESSION['msg'] = "Category deleted successfully...";
            $_SESSION['success'] = 1;
            redirectToWithMeta('category-list.php');
        } else {
            // Error in database deletion
            // showAlert("Error deleting data", 'danger');
        }
    } else {
        // Invalid category ID
        echo "<script>ErrorAlert()</script>";
    }
}
if (isset($_SESSION['msg'])) {
    $alertClass = ($_SESSION['success'] == 1) ? 'alert-success' : 'alert-danger';
    echo "<div class='alert $alertClass text-center'>{$_SESSION['msg']}
    
    </div>";

    // Clear the session variable
    unset($_SESSION['msg']);
    unset($_SESSION['success']);
}
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3; // Set your desired limit per page
$where = [];
$order = 'ASC';
$orderBy = 'DESC';

if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

if (isset($_GET['orderby'])) {
    $orderBy = $_GET['orderby'];
}
if (isset($_GET['name']) && $_GET['name'] !== '' && isset($_GET['status']) && $_GET['status'] !== '') {
    $search_name = $_GET['name'];
    $statusFilter = $_GET['status'];

    // Search by both name and status
    $where[] = "name LIKE '%$search_name%' AND status = '$statusFilter'";
} else {
    // Check if only name is set
    if (isset($_GET['name']) && $_GET['name'] !== '') {
        $search_name = $_GET['name'];

        // Assume it is a name search
        $where[] = "name LIKE '%$search_name%'";
    }

    // Check if only status is set
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $statusFilter = $_GET['status'];

        // Assume it is a status search
        $where[] = "status = '$statusFilter'";
    }
}
$validColumns = ['name', 'ordering', 'status'];
$orderBy = (isset($_GET['orderby']) && in_array($_GET['orderby'], $validColumns)) ? $_GET['orderby'] : 'name';
$order = (isset($_GET['order']) && $_GET['order'] === 'DESC') ? 'DESC' : 'ASC';
$whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

if (!empty($where)) {
    $whereClause .= " ORDER BY $orderBy $order";
    $offset = ($currentPage - 1) * $limit;
    $whereClause .= " LIMIT $limit OFFSET $offset";
} else {
    $offset = ($currentPage - 1) * $limit;
    $whereClause .= " ORDER BY $orderBy $order LIMIT $limit OFFSET $offset";
}
$countResult = $db->select('COUNT(*) as total', 'category', $where ? ' WHERE ' . implode(' AND ', $where) : '');
// print_r($countResult);
// die();
$totalRecords = !empty($countResult) && isset($countResult[0]['total']) ? $countResult[0]['total'] : 0;
$result = $db->select('*', 'category', $whereClause);
// echo $whereClause;

$queryParams = array();

// Add sorting parameters to the query parameters
if (isset($_GET['orderby'])) {
    $queryParams['orderby'] = $_GET['orderby'];
    $queryParams['order'] = $_GET['order'];
}
if (isset($_GET['status'])) {
    $queryParams['status'] = $_GET['status'];
}
if (isset($_GET['name'])) {
    $queryParams['name'] = $_GET['name'];
}
// print_r($_GET);
// die();
$queryString = http_build_query($queryParams);  //convert an associative array into a URL-encoded string
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php 
include_once('link.php');
?>

</head>
<body>
<div class="row filter_row">

    <div class="col pb-5">

        <div class="card mt-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <h4 class="mb-0">Add category</h4>
                    </div>
                    <div class="col-4 text-right">
                        <a href="category-add.php?type=add" class="btn btn-info">Add New Category</a>
                    </div>
                </div>
            </div>
            <div class="row filter_row justify-content-start mt-3">
                <form method="GET" action="category-list.php" class="form-inline">
                    <div class="form-group mx-2">
                        <label for="name" class="sr-only">Name:</label>
                        <input type="text" class="form-control" name="name" placeholder="Search by name" value="<?php echo isset($_GET['name']) ? $_GET['name'] : ''; ?>">
                    </div>
                    <div class="form-group mx-2">
                        <label for="status" class="sr-only">Status:</label>
                        <select class="form-control" name="status">
                            <option value="" <?php echo empty($_GET['status']) ? 'selected' : ''; ?>>All</option>
                            <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <input type="hidden" name="orderby" value="<?php echo $orderBy; ?>">
                    <input type="hidden" name="sort" value="<?php echo $orderBy; ?>">
                    <input type="hidden" name="order" value="<?php echo $order; ?>">
                    <button type="submit" class="btn btn-primary mx-2">Apply Filters</button>
                    <?php if (!empty($_GET['name']) || !empty($_GET['status'])) : ?>
                        <a href="list_category.php" class="btn btn-secondary mx-2">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Categories</h5>
            </div>

            <div class="card-body">
                <table class="table m-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=name&order=<?= ($orderBy == 'name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    Name
                                    &nbsp; <?= ($orderBy == 'name') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=ordering&order=<?= ($orderBy == 'ordering' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    Ordering
                                    &nbsp; <?= ($orderBy == 'ordering') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>

                            <th scope="col">image</th>
                            <th scope="col">
                                <a class="arrow" href="?<?= $queryString ?>&orderby=status&order=<?= ($orderBy == 'status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                    Status
                                    &nbsp; <?= ($orderBy == 'status') ? ($order == 'ASC' ? '<span>&#9650;</span>' : '<span>&#9660;</span>') : ''; ?>
                                </a>
                            </th>
                            <th scope="col">Created at</th>
                            <th scope="col">Updated at</th>
                            <th scope="col">Action</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($totalRecords > 0) {
                            if (!empty($result)) {
                                foreach ($result as $row) {
                                   ?>
                                    <tr>
                                        <th scope="row"><?php echo $row['id']; ?></th>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['ordering']; ?></td>
                                        <td>
                                            <?php
                                            $imagePath =  $row['image'];
                                            if (file_exists($imagePath)) {
                                                echo "<img src='{$imagePath}' class='box-img' height='100' alt=''>";
                                            } else {
                                                echo "Image not found: {$imagePath}";
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td><?php echo $row['created_at']; ?></td>
                                        <td><?php echo $row['updated_at']; ?></td>
                                        <td style="padding: 10;">
                                            <!-- Edit and delete buttons -->
                                            <a href="category-edit.php?type=edit&id=<?php echo htmlentities($row['id']); ?>" class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $row["id"]; ?>)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                    </tbody>
                </table>
        <?php
                            } else {
                                echo "<div class='alert alert-warning'>No records found for the specified criteria.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>No Category found in the database.</div>";
                        }
        ?>
            </div>
        </div>
    </div>
</div>
<div class="pagination justify-content-center">
    <div class="pagination justify-content-center">
        <?php echo pagination('category-list.php', $totalRecords, $currentPage, $limit, $queryParams); ?>
    </div>
</div> 
<script>
    function confirmDelete(categoryID) {
        var confirmDelete = confirm('Are you sure you want to delete this category?');
        if (confirmDelete) {
            window.location.href = 'category-list.php?del=true&id=' + categoryID + '&confirm=true';
        }
    }
</script>

</body>
</html>