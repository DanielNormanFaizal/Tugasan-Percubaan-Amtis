<?php
function calculateElectricityRate($voltage, $current, $rate, $time, $timeUnit) {
    // Calculate Power in Watts
    $power = $voltage * $current;

    // Calculate Energy in kWh
    if ($timeUnit === 'hour') {
        $energy = ($power * $time) / 1000; // Convert Wh to kWh
    } elseif ($timeUnit === 'day') {
        $energy = ($power * $time * 24) / 1000; // Convert Wh to kWh
    } else {
        $energy = 0;
    }

    // Calculate Total Charge
    $totalCharge = $energy * ($rate / 100);

    return array(
        'power' => $power,
        'energy' => $energy,
        'totalCharge' => $totalCharge
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voltage = isset($_POST['voltage']) ? floatval($_POST['voltage']) : 0;
    $current = isset($_POST['current']) ? floatval($_POST['current']) : 0;
    $rate = isset($_POST['rate']) ? floatval($_POST['rate']) : 0;
    $time = isset($_POST['time']) ? floatval($_POST['time']) : 0;
    $timeUnit = isset($_POST['timeUnit']) ? $_POST['timeUnit'] : '';

     // Validation
    if (!is_numeric($voltage) || !is_numeric($current) || !is_numeric($rate) || !is_numeric($time) || ($timeUnit !== 'hour' && $timeUnit !== 'day')) {
        $errors[] = 'Invalid input. Please enter valid numeric values.';
    } else {
        // If validation passes, perform the calculation
        $result = calculateElectricityRate($voltage, $current, $rate, $time, $timeUnit);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Electricity Calculator</title>
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .result-container {
            margin-top: 20px;
        }
		
		th {
        background-color: #001f3f; 
        color: #fff;
        }
		
		
		
    </style>
</head>
<body>

<div class="container">

    <h1 class="mt-5 mb-4">Electricity Calculator</h1>

    <form method="post">
        <div class="form-group">
            <label for="voltage">Voltage (V):</label>
            <input type="number" step="0.01" class="form-control" id="voltage" name="voltage" required>
        </div>
        <div class="form-group">
            <label for="current">Current (A):</label>
            <input type="number" step="0.01" class="form-control" id="current" name="current" required>
        </div>
        <div class="form-group">
            <label for="rate">Current Rate per kWh (%):</label>
            <input type="number" step="0.01" class="form-control" id="rate" name="rate" required>
        </div>
        <div class="form-group">
            <label for="time">Number of Hours/Days:</label>
            <input type="number" step="1" class="form-control" id="time" name="time" required>
        </div>
        <div class="form-group">
            <label for="timeUnit">Time Unit:</label>
            <select class="form-control" id="timeUnit" name="timeUnit" required>
                <option value="hour">Hour(s)</option>
                <option value="day">Day(s)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="background-color: #001f3f; border-color: #001f3f;">Calculate</button>
    </form>

   <?php if (isset($result)): ?>
    <div class="result-container mt-4">
        <h2>Calculation Result:</h2>
        <p>Power: <?php echo $result['power']; ?> Watts</p>
        <p>Energy: <?php echo $result['energy']; ?> kWh</p>
        <p>Total Charge: RM <?php echo number_format($result['totalCharge'], 2); ?></p>
    </div>

    <!-- Display Hourly Consumption Table -->
    <div class="result-container mt-4">
        <h2>Hourly Consumption Table:</h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Hour</th>
                <th scope="col">Energy (kWh)</th>
                <th scope="col">Cost (RM)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $power = $result['power']; 

            for ($hour = 1; $hour <= 24; $hour++): ?>
                <tr>
                    <td><?php echo $hour; ?></td>
                    <td><?php echo $power * $hour / 1000; ?></td>
                    <td>RM <?php echo number_format(($result['totalCharge'] / $result['energy']) * $power * $hour / 1000, 2); ?></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- Display Daily Consumption Table -->
    <div class="result-container mt-4">
        <h2>Daily Consumption Table:</h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Day</th>
                <th scope="col">Energy (kWh)</th>
                <th scope="col">Cost (RM)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $powerDaily = $result['power'] * 24; 

            for ($day = 1; $day <= 30; $day++): ?>
                <tr>
                    <td><?php echo $day; ?></td>
                    <td><?php echo $powerDaily * $day / 1000; ?></td>
                    <td>RM <?php echo number_format(($result['totalCharge'] / $result['energy']) * $powerDaily * $day / 1000, 2); ?></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>
