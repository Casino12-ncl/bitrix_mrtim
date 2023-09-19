<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form action="" method="get">
    <label for="DATE_FROM">Дата от: </label>
    <input type="date" name="DATE_FROM" id="DATE_FROM" value="<?= htmlspecialcharsbx($_GET['DATE_FROM']) ?>">
    <label for="DATE_TO">Дата до: </label>
    <input type="date" name="DATE_TO" id="DATE_TO" value="<?= htmlspecialcharsbx($_GET['DATE_TO']) ?>">
    <label for="PAYMENT_METHOD">Способ оплаты: </label>
    <select name="PAYMENT_METHOD" id="PAYMENT_METHOD">
        <option value="">Все</option>
        <?
        $res = \Bitrix\Sale\PaySystem\Manager::getList(array(
            'select' => array('ID', 'NAME', 'ACTIVE')
        ));
        while ($paySystem = $res->fetch()) {
            if ($paySystem['ACTIVE'] == 'Y') {
                $selected = ($_GET['PAYMENT_METHOD'] == $paySystem['ID']) ? 'selected' : '';
                echo '<option value="' . $paySystem['ID'] . '" ' . $selected . '>' . $paySystem['NAME'] . '</option>';
            }
        }
        ?>
    </select>
    <button type="submit">Показать</button>
    <button type="submit" name="export_to_xls" value="1">Экспорт в XLS</button>
</form>
<table>
    <tr>
        <th>название</th>
        <th>количество</th>
        <th>стоимость</th>
    </tr>
    <? foreach ($arResult["ITEMS"] as $item) : ?>
        <tr>
            <td><?= $item["NAME"] ?></td>
            <td><?= $item["QUANTITY"] ?></td>
            <td><?= $item["PRICE"] ?></td>
        </tr>
    <? endforeach; ?>
</table>