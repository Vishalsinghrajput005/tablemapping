<?php

//change destination and city name to lowercase and trim 

include 'connection.php';

$sql = "SELECT id, city_name FROM hotel_city_list_tripjack WHERE type = 'CITY' OR type = 'MULTI_CITY_VICINITY'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $city_name = trim($row["city_name"]);
        $city_name = strtolower($city_name);

        // Update the city_name column
        $update_sql = "UPDATE hotel_city_list_tripjack SET city_name = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $city_name, $id);

        if ($stmt->execute()) {
            echo "Record updated successfully for ID $id<br>";
        } else {
            echo "Error updating record for ID $id: " . $stmt->error . "<br>";
        }
    }
} else {
    echo "No records found";
}

$conn->close();
?>
