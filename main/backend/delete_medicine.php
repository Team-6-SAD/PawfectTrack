<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: ../Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['ids']) && is_array($data['ids'])) {
        $ids = $data['ids'];
        $errors = [];
        $stockExists = false;

        foreach ($ids as $id) {
            $medicineId = $id['medicineId'];
            $medicineBrandId = $id['medicineBrandId'];

            // Check if there is stock in medicineinventory
            $sql = "SELECT SUM(COALESCE(StockQuantity, 0)) AS TotalStockQuantity FROM medicineinventory WHERE MedicineBrandID = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $medicineBrandId);
                $stmt->execute();
                $stmt->bind_result($totalStockQuantity);
                $stmt->fetch();
                $stmt->close();

                if ($totalStockQuantity > 0) {
                    $stockExists = true;
                    break;
                }
            } else {
                $errors[] = "Error preparing statement for stock check: " . $conn->error;
                break;
            }

            // Perform the deletion from the database
            $sql = "DELETE FROM medicinebrand WHERE MedicineID = ? AND MedicineBrandID = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ii", $medicineId, $medicineBrandId);
                if (!$stmt->execute()) {
                    $errors[] = "Error deleting from medicinebrand: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = "Error preparing statement for medicinebrand: " . $conn->error;
            }

            $sql = "DELETE FROM medicine WHERE MedicineID = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $medicineId);
                if (!$stmt->execute()) {
                    $errors[] = "Error deleting from medicine: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = "Error preparing statement for medicine: " . $conn->error;
            }
        }

        if ($stockExists) {
            echo json_encode(['success' => false, 'message' => 'Stock still exists, please remove stock before deleting']);
        } else if (empty($errors)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No valid IDs provided']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
