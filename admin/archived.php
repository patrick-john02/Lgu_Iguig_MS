<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header("Location: admin_login.php");
    exit();
}

include('../config/config.php');
try {
    $query = "
    SELECT 
        ad.document_id AS document_id,
        ad.title AS document_title,
        ad.archived_at AS archived_at,
        ad.subject AS document_subject,
        CONCAT(u.first_name, ' ', u.last_name) AS author_name
    FROM archiveddocuments ad
    LEFT JOIN users u ON ad.authored_by = u.user_id
    ORDER BY ad.archived_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $archivedDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching archived documents: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Documents</title>
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../prod/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Archived Documents</h2>
        <table class="table table-striped jambo_table bulk_action">
            <thead>
                <tr class="headings">
                    <th>
                        <input type="checkbox" id="check-all" class="flat">
                    </th>
                    <th class="column-title">Document ID</th>
                    <th class="column-title">Title</th>
                    <th class="column-title">Archived At</th>
                    <th class="column-title">Author</th>
                    <th class="column-title no-link last"><span class="nobr">Action</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($archivedDocuments)): ?>
                    <?php foreach ($archivedDocuments as $document): ?>
                        <tr class="even pointer">
                            <td class="a-center">
                                <input type="checkbox" class="flat" name="table_records" data-id="<?php echo htmlspecialchars($document['document_id']); ?>">
                            </td>
                            <td class=" "><?php echo htmlspecialchars($document['document_id']); ?></td>
                            <td class=" "><?php echo htmlspecialchars($document['document_title']); ?></td>
                            <td class=" "><?php echo htmlspecialchars($document['archived_at']); ?></td>
                            <td class=" "><?php echo htmlspecialchars($document['author_name']); ?></td>
                            <td class=" last">
                                <a href="document_info.php?document_id=<?php echo urlencode($document['document_id']); ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No archived documents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button id="restoreButton" class="btn btn-warning" >Restore</button>
    </div>

    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <script src="../vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Archive functionality
        document.getElementById('restoreButton').addEventListener('click', function() {
            var selectedIds = [];
            var checkboxes = document.querySelectorAll('.table tbody input[type="checkbox"]:checked');

            checkboxes.forEach(function(checkbox) {
                selectedIds.push(checkbox.getAttribute('data-id'));
            });

            // Check if any documents are selected
            if (selectedIds.length > 0) {
                console.log("Selected Documents to Restore: ", selectedIds);
                // You can implement AJAX here to restore the selected documents back to the document table
                // Make sure to update the `is_archived` field in the `document` table
            } else {
                // If no selections are made, show alert
                alert('No selected documents to restore.');
            }
        });

        // Toggle the visibility of the Restore button
        function toggleRestoreButton() {
            var checkboxes = document.querySelectorAll('.table tbody input[type="checkbox"]:checked');
            var restoreButton = document.getElementById('restoreButton');
            if (checkboxes.length > 0) {
                restoreButton.style.display = 'inline-block';
            } else {
                restoreButton.style.display = 'none';
            }
        }

        // Select/Deselect all checkboxes
        document.getElementById('check-all').addEventListener('change', function(event) {
            var checkboxes = document.querySelectorAll('.table tbody input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = event.target.checked;
            });
            toggleRestoreButton();
        });

        // Listen for individual checkbox change to toggle the Restore button
        document.querySelectorAll('.table tbody input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', toggleRestoreButton);
        });
    </script>
</body>
</html>
