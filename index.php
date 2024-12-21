<link href="style.css" rel="stylesheet" />
<?php
include_once("highlighter.class.php");
$colorObj = new highlighter();
$fileContent = $colorObj->applycolor("highlighter.class.php");
echo $fileContent;
$colorObj->showfilename(false);
$fileContent = $colorObj->applycolor("index.php");
echo $fileContent;
?>
