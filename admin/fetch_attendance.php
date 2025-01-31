<?php
include('../config/config.php');

$sql = "SELECT 
            u.first_name, 
            u.last_name, 
            u.position_id, 
            p.position_name, 
            DATE(a.time_in) AS date, 
            a.time_in AS check_in, 
            a.break_out, 
            a.break_in, 
            a.time_out AS check_out
        FROM tbl_attendance a
        JOIN users u ON a.user_id = u.user_id
        LEFT JOIN positions p ON u.position_id = p.id
        WHERE DATE(a.time_in) = CURDATE() -- Default to the current date
        ORDER BY a.time_in DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    [$status, $remarks] = calculateStatusAndRemarks(
        $row['check_in'],
        $row['break_out'],
        $row['break_in'],
        $row['check_out']
    );

    echo "<tr>
            <td>{$row['first_name']}</td>
            <td>{$row['last_name']}</td>
            <td>{$row['position_name']}</td>
            <td>{$row['date']}</td>
            <td>{$row['check_in']}</td>
            <td>{$row['break_out']}</td>
            <td>{$row['break_in']}</td>
            <td>{$row['check_out']}</td>
            <td>{$status}</td>
            <td>{$remarks}</td>
          </tr>";
}
?>
