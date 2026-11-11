<?php
$filename = pathinfo(basename($_GET['fid'] ?? 'upload.dat'), PATHINFO_FILENAME) . '.txt';
$path = 'upload';
$fullpath = $path.'/'.$filename;
$file_content = '';
if(file_exists($fullpath)){
    $file_content = file_get_contents($path.'/'.$filename);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $filename ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="content">
         <div class="btn">
            <div class="download-btn">
                <a href="download.php?fid=<?= $filename ?>"><button type="button">Download</button></a>
            </div>
            <div class="print-btn">
                <button type="button" onclick="printNow()">Print</button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <?= $file_content ?>
            </div>
        </div>
       
    </div>
</body>
<script>
    function printNow(){
        window.print();
    }
</script>
</html>