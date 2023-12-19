<?php
include_once 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorHTML;

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generator - DJ Joker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        form {
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }

        textarea {
            resize: vertical;
            width: 100%;
        }

        .barcode-container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-box {
            width: 100%;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-box img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }

        h1, h6 {
            margin: 0;
            text-align: center;
        }

        .signature {
            align-self: flex-end;
            margin-top: auto;
        }

        button {
            width: 100%;
            background-color: black !important;
            color: whitesmoke !important;
            border: none !important;
        }

        button:hover {
            background-color: #434444 !important;
            color: whitesmoke !important;
            border: none !important;
        }

        hr {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .page-link {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5 mb-4">Barcode Generator</h1>
        <hr>
        <form method="post">
            <div class="mb-3">
                <label for="barcode_data" class="form-label">Enter Barcode Data (one per line):</label>
                <textarea class="form-control" name="barcode_data" rows="10" cols="30"><?php echo isset($_SESSION['barcode_data']) ? htmlspecialchars($_SESSION['barcode_data']) : ''; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Generate Barcodes</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['barcode_data'] = $_POST['barcode_data'];
        }

        if (isset($_SESSION['barcode_data'])) {
            $barcodeData = $_SESSION['barcode_data'];
            $barcodeArray = explode("\n", $barcodeData);

            if (count($barcodeArray) > 300) {
                die("You can only generate a maximum of 300 barcodes at a time.");
            }

            $generator = new BarcodeGeneratorHTML();

            $itemsPerPage = 30;
            $totalItems = count($barcodeArray);
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
                    echo $generator->getBarcode($data, $generator::TYPE_CODE_128);
                    echo '</div>';
                }
                ?>
            </div>
            <nav class="pagination">
                <ul class="pagination">
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
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
            <h6>- DJ Joker</h6>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
