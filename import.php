<?php
if (isset($_POST["import"])) {

    $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($_FILES["file"]["type"], $allowedFileType)) {

        $targetPath = 'uploads/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadSheet = $Reader->load($targetPath);
        $excelSheet = $spreadSheet->getActiveSheet();
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        for ($i = 0; $i <= $sheetCount; $i++) {
            $customer_username = "";
            if (isset($spreadSheetAry[$i][0])) {
                $customer_username = $mysqli->real_escape_string($spreadSheetAry[$i][0]);
            }
            $item_name = "";
            if (isset($spreadSheetAry[$i][1])) {
                $item_name = $mysqli->real_escape_string($spreadSheetAry[$i][1]);
            }
            $quantity_status = "";
            if (isset($spreadSheetAry[$i][1])) {
                $quantity_status = $mysqli->real_escape_string($spreadSheetAry[$i][2]);
            }
            $category = "";
            if (isset($spreadSheetAry[$i][1])) {
                $category = $mysqli->real_escape_string($spreadSheetAry[$i][3]);
            }
            $comments = "";
            if (isset($spreadSheetAry[$i][1])) {
                $comments = $mysqli->real_escape_string($spreadSheetAry[$i][4]);
            }

            if (
                !empty($customer_username) || !empty($item_name)
                || !empty($quantity_status) || !empty($category)
                || !empty($comments)
            ) {
                $query = "INSERT INTO orders(customer_username, item_name, quantity_status, category, comments)
           VALUES ('$customer_username', '$item_name', '$quantity_status', '$category', '$comments')";

                if ($mysqli->query($query) === TRUE) {
                    $type = "success";
                    $message = "Excel Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing Excel Data";
                }
            }
        }
    } else {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
    }
}
