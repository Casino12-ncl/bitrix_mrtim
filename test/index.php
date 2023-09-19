<?

/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("отчет");

$APPLICATION->IncludeComponent(
    "company:test",
    "",
    array(
        "COMPONENT_TEMPLATE" => ".default",
    ),
    array()
);

?>
</p><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>