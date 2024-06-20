<?php

//check matching data and insert in table 

include 'connection.php';
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$batchSize = 1000; 

$insert_sql = "INSERT INTO hotel_city_list (destination, country, country_code, tripjack_id, tbo_id, bdsd_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmtInsert = $conn->prepare($insert_sql);

$page = 0;
$offset = 0;

do {
    $offset = $page * $batchSize;

    $sql = "
        SELECT 
            tripjack.city_name, 
            tripjack.country AS tripjack_country,
            tripjack.country_code AS tripjack_country_code,
            tripjack.city_id AS tripjack_city_id,
            tbo.city_id AS tbo_city_id
        FROM 
            hotel_city_list_tripjack AS tripjack
        INNER JOIN 
            hotel_city_list_tbo AS tbo
        ON 
            tripjack.city_name = tbo.destination 
            AND tripjack.country_code = tbo.country_code
        WHERE 
            tripjack.type IN ('CITY', 'MULTI_CITY_VICINITY')
        LIMIT ?, ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $batchSize);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $city_name = $row["city_name"];
            $country = $row["tripjack_country"];
            $country_code = $row["tripjack_country_code"];
            $tripjack_id = $row["tripjack_city_id"];
            $tbo_id = $row["tbo_city_id"];
            $bdsd_id = '1000' . ($page * $batchSize + $result->current_field + 1);

            $stmtInsert->bind_param("ssssss", $city_name, $country, $country_code, $tripjack_id, $tbo_id, $bdsd_id);
            if ($stmtInsert->execute()) {
                echo "Record inserted successfully for city $city_name<br>";
            } else {
                echo "Error inserting record for city $city_name: " . $stmtInsert->error . "<br>";
            }
        }
    }
    $page++;
} while ($result->num_rows > 0);

$stmtInsert->close();
$stmt->close();

$conn->close();
?>



<!-- *********************************************  delete dublicate data  ***********************************  -->

<!-- DELETE hcl1
FROM hotel_city_list hcl1
JOIN (
    SELECT destination, country_code, MIN(id) AS min_id
    FROM hotel_city_list
    GROUP BY destination, country_code
) hcl2 ON hcl1.destination = hcl2.destination 
       AND hcl1.country_code = hcl2.country_code 
       AND hcl1.id > hcl2.min_id; -->


<!-- ********************************************* delete dublicate data  ***********************************  -->





<!-- ********************************************* check dublicate data ***********************************  -->

<!-- SELECT destination, country_code, COUNT(*) AS duplicate_count
FROM hotel_city_list
GROUP BY destination, country_code
HAVING COUNT(*) > 1; -->

 <!-- ********************************************* check dublicate data ***********************************  -->


<!-- *********************************************update bdsd_id and destination to uppercase ***********************************  -->

<!-- UPDATE hotel_city_list
SET 
    destination = UPPER(destination),
    bdsd_id = CONCAT('1000', id);
 -->

<!-- *********************************************update bdsd_id and destination to uppercase ***********************************  -->