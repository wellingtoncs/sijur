<?php
// Example use

include("../Html2Rtf/class_rtf.php");

$rtf = new rtf("../Html2Rtf/rtf_config.php");
$rtf->setPaperSize(5);
$rtf->setPaperOrientation(1);
$rtf->setDefaultFontFace(0);
$rtf->setDefaultFontSize(24);
$rtf->setAuthor("Fábio Torres");
$rtf->setOperator("fabiotorres@abraz.adv.br");
$rtf->setTitle("RTF Document");
$rtf->addColour("#000000");
$text = str_replace('\"','',$_POST['name_text']);
$rtf->addText($text);
$rtf->getDocument();
?>