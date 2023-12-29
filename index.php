<?php
include_once 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorHTML;

session_start();
$totalItems = 0;
$error = '';

if (isset($_POST['clear_data'])) {
    unset($_SESSION['barcode_data']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Barcode Generator</title>
        <link rel="icon" type="image/x-icon" href="urllogo.jpg">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="area">
        <div class="container">
            <h1 class="mt-5 mb-4">Barcode Generator</h1>
            <hr>
            <form method="post">
                <div class="mb-3">
                    <label for="barcode_data" class="form-label">Enter Barcode Data (one per line):</label>
                    <textarea class="form-control" name="barcode_data" rows="10" cols="30"><?php echo isset($_SESSION['barcode_data']) ? htmlspecialchars($_SESSION['barcode_data']) : ''; ?></textarea>
                </div>
                <div class="button-container">
                    <button type="submit" class="btn " name="submit">Generate Barcodes</button>
                    <button type="submit" class="btn " name="clear_data">Clear Data</button>
                </div>
            </form>

            <?php

            try {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!isset($_POST['barcode_data'])) {
                        throw new Exception('Barcode data is missing.');
                    }

                    $_SESSION['barcode_data'] = $_POST['barcode_data'];

                    if (empty(trim($_SESSION['barcode_data']))) {
                        throw new Exception('No barcode data found. Please enter barcode data and try again.');
                    }
                }

                if (isset($_SESSION['barcode_data'])) {
                    $barcodeData = $_SESSION['barcode_data'];
                    $barcodeArray = explode("\n", $barcodeData);
                    if (count($barcodeArray) > 500) {
                        throw new Exception('You can only generate a maximum of 500 barcodes at a time.');
                    }

                    $generator = new BarcodeGeneratorHTML();
                    $totalItems = count($barcodeArray);
                    $itemsPerPage = 30;
                    $totalPages = ceil($totalItems / $itemsPerPage); 
                    $currentPage = isset($_GET['page']) ? max(1, min($totalPages, (int)$_GET['page'])) : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;
            ?>
                    <div class="barcode-container">
                        <?php
                        for ($i = $offset; $i < min($offset + $itemsPerPage, $totalItems); $i++) {
                            $data = $barcodeArray[$i];
                            echo '<div class="barcode-box">';
                            echo '<p><strong>Barcode Data:</strong> ' . htmlspecialchars($data) . '</p>';
                            echo '<p><strong>Number:</strong> ' . ($i + 1) . '</p>';
                            $barcodeHtml = $generator->getBarcode($data, $generator::TYPE_CODE_128);
                            $barcodeHtml = str_replace('height:30px;', 'height:80px;', $barcodeHtml);
                            echo $barcodeHtml;
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            $itemsPerPage = 30;
            $totalPages = ceil($totalItems / $itemsPerPage); 

            if (!empty($error)) {
                ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php
            } else {
            ?>
                <nav class="pagination">
                    <ul class="pagination">
                        <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                            <li class="page-item <?php echo $page == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php
            }

            ?>

            <div class="signature">
                <h6>-Mr.KH</h6>
            </div>
        </div>
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
</body>
</html>
<!-- 

Developed by Kushal Pipaliya 
Github :- https://github.com/Kushal129   

-->