<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";

class Test extends CBitrixComponent
{
    public function executeComponent()
    {
        $paymentMethodId = isset($_GET["PAYMENT_METHOD"]) ? $_GET["PAYMENT_METHOD"] : null;
        $orderFilter = array(
            "STATUS_ID" => "F"
        );

        if ($paymentMethodId) {
            $orderFilter["PAY_SYSTEM_ID"] = $paymentMethodId;
        }

        if ($_GET["DATE_FROM"]) {
            $dateFrom = new \Bitrix\Main\Type\DateTime($_GET["DATE_FROM"], 'Y-m-d');
            $orderFilter[">=DATE_INSERT"] = $dateFrom;
        }

        if ($_GET["DATE_TO"]) {
            $dateTo = new \Bitrix\Main\Type\DateTime($_GET["DATE_TO"] . ' 23:59:59', 'Y-m-d H:i:s');
            $orderFilter["<=DATE_INSERT"] = $dateTo;
        }

        $orders = \Bitrix\Sale\Order::getList(
            array(
                "filter" => $orderFilter,
                "select" => array("ID")
            )

        );
        $orderIds = array();
        while ($order = $orders->fetch()) {
            $orderIds[] = $order["ID"];
        }


        $productFilter = array(
            "ORDER_ID" => $orderIds
        );
        $products = \Bitrix\Sale\Basket::getList(
            array(
                "filter" => $productFilter,
                "select" => array("NAME", "QUANTITY", "PRICE")
            )
        );

        $result = array();
        while ($product = $products->fetch()) {
            $name = $product["NAME"];
            $quantity = $product["QUANTITY"];
            $price = $product["PRICE"] * $quantity;
            if (isset($result[$name])) {
                $result[$name]["QUANTITY"] += $quantity;
                $result[$name]["PRICE"] += $price;
            } else {
                $result[$name] = array(
                    "NAME" => $name,
                    "QUANTITY" => $quantity,
                    "PRICE" => $price
                );
            }
        }

        $this->arResult["ITEMS"] = $result;

        $this->includeComponentTemplate();
        if ($_GET["export_to_xls"] == 1) {
            $this->exportToXLS($result);
        }
    }

    function exportToXLS($items)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue("A1", "название");
        $sheet->setCellValue("B1", "количество");
        $sheet->setCellValue("C1", "стоимость");

        // Add data
        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue("A" . $row, $item["NAME"]);
            $sheet->setCellValue("B" . $row, $item["QUANTITY"]);
            $sheet->setCellValue("C" . $row, $item["PRICE"]);
            $row++;
        }

        // Save XLS file
        $writer = new Xlsx($spreadsheet);
        $xlsFile = $_SERVER["DOCUMENT_ROOT"] . "/report_" . date("Y-m-d_H-i-s") . ".xlsx";
        $writer->save($xlsFile);

        // Output XLS file for download
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=" . basename($xlsFile));
        header("Cache-Control: max-age=0");
        readfile($xlsFile);
        unlink($xlsFile);
        exit;
    }
}
