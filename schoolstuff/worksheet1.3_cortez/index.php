<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers Queries</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #333; }
        .result-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<h1>Customers Query Results (AJAX)</h1>

<div class="result-box">
    <h2>1. Customers from Brazil</h2>
    <div id="result1">Loading...</div>
</div>

<div class="result-box">
    <h2>2. Customers starting with "B"</h2>
    <div id="result2">Loading...</div>
</div>

<div class="result-box">
    <h2>3. Customers from London, Berlin, Madrid, Caracas</h2>
    <div id="result3">Loading...</div>
</div>

<div class="result-box">
    <h2>4. All Cities in Venezuela</h2>
    <div id="result4">Loading...</div>
</div>

<div class="result-box">
    <h2>5. Number of customers per country</h2>
    <div id="result5">Loading...</div>
</div>

<script>
$(document).ready(function() {
    $("#result1").load("number1.php");
    $("#result2").load("number2.php");
    $("#result3").load("number3.php");
    $("#result4").load("number4.php");
    $("#result5").load("number5.php");
});
</script>

</body>
</html>
