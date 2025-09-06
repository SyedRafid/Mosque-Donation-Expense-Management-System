<?php
include('includes/config.php');

$profileId = isset($_POST['profileId']) ? intval($_POST['profileId']) : 0;
$year = isset($_POST['year']) ? intval($_POST['year']) : 0;

if ($profileId > 0 && $year > 0) {
    // Fetch paid months and corresponding amounts for the given profile and year
    $sql = "SELECT MONTH(date) as paidMonth, amount 
            FROM payment 
            WHERE pr_id = :profileId 
            AND YEAR(date) = :year";
    $query = $dbh->prepare($sql);
    $query->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $query->bindParam(':year', $year, PDO::PARAM_INT);
    $query->execute();
    $paidData = $query->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the profile creation date (con_date)
    $sql = "SELECT con_date FROM profile WHERE pr_id = :profileId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $query->execute();
    $profile = $query->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
        // Extract the year and month from con_date
        $conYear = intval(date('Y', strtotime($profile['con_date'])));
        $conMonth = intval(date('m', strtotime($profile['con_date'])));

        // Determine starting month logic based on the selected year
        $startMonth = 1; // Default to January for years after the conYear

        if ($year == $conYear) {
            // If it's the conYear, start from conMonth
            $startMonth = $conMonth;
        }

        // Find the first unpaid month for the selected year
        $firstUnpaidMonth = null;
        $paidMonths = array_column($paidData, 'paidMonth'); // Extract paid months
        for ($month = $startMonth; $month <= 12; $month++) {
            if (!in_array($month, $paidMonths)) {
                $firstUnpaidMonth = $month;
                break;
            }
        }

        echo json_encode([
            'paidMonths' => $paidMonths,
            'paidAmounts' => array_column($paidData, 'amount', 'paidMonth'), // Return amounts with the corresponding months
            'firstUnpaidMonth' => $firstUnpaidMonth,
            'conYear' => $conYear,
            'conMonth' => $conMonth
        ]);
    } else {
        echo json_encode(['error' => 'Invalid profile.']);
    }
} else {
    echo json_encode(['error' => 'Invalid profile or year.']);
}
?>
