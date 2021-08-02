<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('import');
    }

    public function processImport(Request $request) {

        if($request->file('imported-file')) {
            $path = $request->file('imported-file')->getRealPath();

            //TODO Make Validation type of extension file
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::CSV)[0];

            /*
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::CSV);
            Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::XLSX);
            Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::XLS);
            Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::TSV);
            Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::CSV);
            */

            if(!empty($data)) {

                $dataImported= "";
                for($i=0;$i<count($data);$i++) {
                    $field0 = $data[$i][0]; // shortSiteName
                    $field1 = $data[$i][1]; // domain
                    $field2 = $data[$i][2]; // siteID
                    //$field3 = $data[$i][3];
                    //$field4 = $data[$i][4];
                    //$field5 = $data[$i][5];
                    //$field6 = $data[$i][6];
                    $field7 = $data[$i][7]; // tier
                    //$field8 = $data[$i][8];
                    //$field9 = $data[$i][9];
                    //$field10 = $data[$i][10];
                    //$field11 = $data[$i][11];


                    //$dataImported .= $this->updateStatusLost($field0);
                    //$dataImported .= $this->UpdatesQOH($field0, $field1);
                    //$dataImported .= $this->InsertHistoryQOH($field0, $field1);
                    //$dataImported .= $this->prepareInsertsIfnotExist($field1);
                    //$dataImported .= $this->prepareUpdates($field0,$field1,$field2);
                    //$dataImported .= $this->prepareInserts($field0, $field1, $field2);
                    //$dataImported .= $this->getqtyItems($field0);
                    //$dataImported .= $this->prepareUpdatesHistory($field5,$field3);
                    //$dataImported .= prepareUpdatesQOH($item, $qty);
                    //$dataImported .= $this->prepareInsertHistoryQOH($field0, $field2);
                    //$dataImported .= $this->prepareUpdatesQOH($field0, $field2);
                    //$dataImported .= $this->UpdatesPriceD($field0, $field3);
                    //$dataImported .= $this->InsertVehicleItemCount($field10, $field8);
                    //$dataImported .= $this->InsertPackageScan($field0, $field3);
                    //$dataImported .= $this->InsertLCPull($field0, $field3);
                    //$dataImported .= $this->InsertNewPriceList($field3, $field4, $field5, $field6, $field7);
                    //$dataImported .= $this->prepareUpdatesBarcodes($field0, $field1);
                    //$dataImported .= $this->updatesPriceHTLKEYS($field3, $field4, $field6, $field7, $bank='trc');
                    //$dataImported .= $this->prepareDeletes($field4);
                    //$dataImported .= $this->prepareUpdatesBarcodes2($field1);
                    $dataImported .= $this->updateSitesTier($data[$i][2], $data[$i][7]);
                    //die();

                }

                /*
                $duplicates = [];
                for ($i=0; $i < count($data); $i++) {
                    $item_id = $data[$i][1]; // item_id
                    $action = $data[$i][2]; // action
                    $notes = $data[$i][8]; // action
                    $date_at = $data[$i][10]; // date_at
                    for( $j=$i; $j < count($data); $j++) {
                        if ( ($i != $j) && ($item_id == $data[$j][1])
                             && ($action == $data[$j][2])
                             && ($notes == $data[$j][8])
                             && ($date_at == $data[$j][10]) ){
                            $duplicates[] = [
                                'data 1' => $data[$i],
                                'data 2' => $data[$j]
                            ];
                        }

                    }
                }*/

                //return Excel::download(collect($duplicates), 'disney.xlsx');

                echo $dataImported;

                die();
            }
        }

    }


    public function prepareUpdatesHistory($qoh, $item_name) {
        return "UPDATE akc_qb.items_working SET QOH = '{$qoh}'
                WHERE FullName = '{$item_name}';<br>";
    }

    /**
     * insert Mysql , Insert If not Exist this works when is not unique values set in the column field
     */
    public function prepareInsertsIfnotExist($item_name) {

        // Insert If not Exist
        return "INSERT INTO `inventory_htl`.`livecounts` (`location_id`, `location_name`, `company_id`, `item_name`, `livecount`, `average`, `min`, `max`, `critical_reorder_point`, `rpln`, `ytd_count`, `cycle_type`, `cycle_date`, `cycle_count`, `created_at`, `updated_at`)
         SELECT * FROM (SELECT '331615', '502', '777', '{$item_name}', '0', '1', '2', '3', '5', '6', '7', 'c', '2019-12-07 15:53:00', '8', '2019-12-07 15:53:01', '2019-12-07 15:53:02') AS tmp
         WHERE NOT EXISTS (
            Select `item_name` From `inventory_htl`.`livecounts` WHERE `item_name` = '{$item_name}' AND location_name = '502'
         ) LIMIT 1;";
    }


    public function prepareUpdates($livecount, $item_name, $truck) {
        return "UPDATE inventory_htl.livecounts SET livecount = '{$livecount}',
                 company_id = '1',
                 average = '1',
                 min = '1',
                 max = '1',
                 critical_reorder_point = '1',
                 cycle_count = '1',
                 rpln = '1',
                 ytd_count = '1',
                 cycle_count = '0'
                WHERE item_name = '{$item_name}' AND location_name = '{$truck}';";
    }
    public function prepareInserts($item_name, $barcode, $package_id){
        return "INSERT INTO `inventory_htl`.`outgoing` (`item_name`, `barcode`, `package_id`, `created_at`, `updated_at`)
        VALUES(
        '{$item_name}',
        '{$barcode}', {$package_id}, '2020-02-21 15:53:00', '2020-02-21 15:53:00');<br>";
    }

    public function prepareDeletes($barcode) {
        return "DELETE FROM `inventory_htl`.`barcodes` WHERE barcode = '${barcode}';<br>";
    }

    public function getqtyItems($barcode) {
        $item = substr($barcode,0,4);

        if (isset($this->itemlist[$item]) || array_key_exists($item, $this->itemlist)) {
            $this->itemlist[$item]= $this->itemlist[$item]+1;
        }else{
            $this->itemlist[$item]= 1;
        }
    }

    public function parsingItems() {
        $data ="";
        foreach ($this->itemlist as $key => $value) {

            $data.= $this->prepareInsertHistoryQOH($key, $value);
            $data.= $this->prepareUpdatesQOH($key, $value);
        }
        return $data;
    }

    public function prepareUpdatesPriceList($CustomerName, $item, $make, $Price2, $duplicate_price) {
        return "
        UPDATE `billing`.`itemcustom`
        SET `Price2` = '${Price2}',
        `duplicate_price` = '${duplicate_price}'
        WHERE `CustomerName` = '${CustomerName}'
        AND `FullName` = '${item}'
        AND `Make` = '${make}';<br>";
    }

    public function prepareUpdatesQOH($item, $qty) {
        return "
        UPDATE `akc_qb`.`items_working` as iwa
        INNER JOIN `akc_qb`.`items_working` AS iwb ON iwa.id = iwb.id
        SET iwa.`QOH` =  (iwb.`QOH` - {$qty})
        WHERE iwa.`FullName` = '{$item}';<br>";
    }

    /**
     * Insert based on Subqueries
     * Sum + Operation bewteen Columns result
     */
    public function prepareInsertHistoryQOH($item, $qty) {
        return "INSERT INTO `akc_qb`.`history` (`item_id`, `action`, `item`, `from`, `to`, `change`, `by`, `for`, `notes`,`entered`, `updated`)
        VALUES(
        (SELECT `id` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        'Adjustment',
        '{$item}',
        (SELECT `qoh` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        (SELECT (SELECT (`qoh` * 1) FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1) - {$qty}),
        '{$qty}',
        'cron',
        'AKC',
        'adjustment for broken cron 2_18_2020 to 3_3_2020 - validated by inventory',
        '2020-03-11 07:30:00',
        '2020-03-11 07:30:00'
        );<br>";
    }

    public function parsingHistoryItems() {
        $data ="";
        foreach ($this->itemlist as $key => $value) {
            $data.= $this->prepareInsertHistoryQOH($key, $value);
        }
        return $data;
    }

    public function updateStatusLost($barcode) {
        return "UPDATE inventory_htl.barcodes SET status = 'lost'
                WHERE barcode = '{$barcode}';<br>";
    }

    public function UpdatesQOH($item, $qty) {
        return "
        UPDATE `akc_qb`.`items_working` as iwa
        INNER JOIN `akc_qb`.`items_working` AS iwb ON iwa.id = iwb.id
        SET iwa.`QOH` =  (iwb.`QOH` + {$qty})
        WHERE iwa.`FullName` = '{$item}';<br>";
    }




    /**
     * Insert based on Subqueries
     * Sum + Operation bewteen Columns result
     */
    public function InsertHistoryQOH($item, $qty) {
        return "INSERT INTO `akc_qb`.`history` (`item_id`, `action`, `item`, `from`, `to`, `change`, `by`, `for`, `notes`,`entered`, `updated`)
        VALUES(
        (SELECT `id` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        'Adjustment',
        '{$item}',
        (SELECT `qoh` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        (SELECT (SELECT (`qoh` * 1) FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1) + {$qty}),
        '{$qty}',
        'AKC',
        'AKC',
        'adjustment for item returned from truck',
        '2020-05-05 05:30:00',
        '2020-05-05 05:30:00'
        );<br>";
    }

    /**
     * @param $item
     * @param $price
     * @return string
     */
    public function UpdatesPriceD($item, $price) {

        if( substr_count($price, '.') < 1 ){
            $price = $price.".00";
        }

        if ( (strlen($price) - strrpos($price, '.') - 1) == 1){
            $price = $price."0";
        }
        return "
        UPDATE `akc_qb`.`items_working`
        SET PriceD =  {$price}
        WHERE FullName = '{$item}';<br>";
    }

    public function updateSitesTier($siteID, $tier) {
        // UPDATE `_sites` SET `tier` = '0' WHERE `_sites`.`siteID` = 1;
        // 'badmilfs.com' => 1331,
        // return "UPDATE `_sites` SET tier =  {$tier} WHERE siteID = '{$siteID}';<br>";
        return "{$siteID} => {$tier},<br>";
    }

    /**
     * Insert Vehicle Item Count
     */
    public function InsertVehicleItemCount($year, $model) {
        $model = strtoupper($model);

        return "INSERT INTO `vehicle_search`.`item_count` (`year`, `make`, `model`, `vin_seq`, `item`, `full_item`, `count`, `mid`, `last_used`)
        VALUES(
        '{$year}',
        'MERCEDES-BENZ',
        '{$model}',
        '',
        '7777',
        '7777',
        '1000',
        '0',
        '2020-06-03 03:30:00'
        );<br>";
    }


    /**
     * Insert based on Subqueries
     * Sum + Operation bewteen Columns result
     * @param $item
     * @param $qty
     * @return string
     */
    public function InsertPackageScan($item, $qty) {
        //-- INSERT INTO `inventory_htl`.`package_scans` (`package_id`, `item`, `user_id`, `needed`, `sent`, `received`, `cost`, `backordered`, `result`, `submitted_by`, `created_at`, `updated_at`)
        // VALUES ('5504144', '1111', '6380', '4', '0', '0', '15', '0', 'confirmed', 'Emmanuel Clark', '2020-08-03 08:30:00', '2020-08-03 08:30:00');
        return "INSERT INTO `inventory_htl`.`package_scans` (`package_id`, `item`, `user_id`, `needed`, `sent`, `received`, `cost`, `backordered`, `result`, `submitted_by`, `created_at`, `updated_at`)
        VALUES (
        '5504144',
        '{$item}',
        '6380',
        '{$qty}',
        '0',
        '0',
        (SELECT `PurchaseCost` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        '0',
        'confirmed',
        'Emmanuel Clark',
        '2020-08-03 08:30:00',
        '2020-08-03 08:30:00'
        );<br>";
    }

    /**
     * Insert based on Subqueries
     * Sum + Operation bewteen Columns result
     * @param $item
     * @param $qty
     * @return string
     */
    public function InsertLCPull($item, $qty) {
        //INSERT INTO `inventory`.`lcpull` (`lcitem`, `lclocation`, `lctruck`, `needed`, `livecount`, `lcaverage`, `send`, `recieved`, `backordered`, `date_pulled`, `lcpackageid`)
        //VALUES ('1111', 'AAA', '142', '3', '0', '0', '0', '0', '0', '2020-08-03', '5504144');
        return "INSERT INTO `inventory`.`lcpull` (`lcitem`, `lclocation`, `lctruck`, `needed`, `livecount`, `lcaverage`, `send`, `company_id`, `recieved`, `backordered`, `date_pulled`, `lcpackageid`)
        VALUES (
        '{$item}',
        (SELECT `AKC_Location` FROM `akc_qb`.`items_working` WHERE `FullName` = '{$item}' LIMIT 1),
        '142',
        '{$qty}',
        '0',
        '0',
        '0',
        '5',
        '0',
        '0',
        '2020-08-03',
        '5504144'
        );<br>";
    }


    /**
     * Insert based on Subqueries
     * Sum + Operation bewteen Columns result
     * @param $FullName
     * @param $Price2
     * @param $duplicate_price
     * @param $Make
     * @param $Cat
     * @return string
     */
    public function InsertNewPriceList($FullName, $Price2, $duplicate_price, $Make, $Cat) {
        //INSERT INTO `billing`.`itemcustom` (`price_list_id`, `CustomerName`, `FullName`, `Price2`, `duplicate_price`, `Make`, `Cat`, `Type`, `Active`)
        // VALUES ('71', 'GKAA', '9999', '99', '99', 'FORD', 'Brass Key', '3', 'yes');
        return "INSERT INTO `billing`.`itemcustom` (`price_list_id`, `CustomerName`, `FullName`, `Price2`, `duplicate_price`, `Make`, `Cat`, `Type`, `Active`)
        VALUES (
        '98',
        'gkaa',
        '{$FullName}',
        '{$Price2}',
        '{$duplicate_price}',
        '{$Make}',
        '{$Cat}',
        '3',
        'yes'
        );<br>";
    }

    /**
     * @param $id
     * @param $barcode
     * @return string
     */
    public function prepareUpdatesBarcodes($id, $barcode) {

        $splitBarcode = explode("|", $barcode);
        $cleanBarcode = $splitBarcode[0];
        return "
                UPDATE `inventory_htl`.`barcodes`
                SET barcode = '${cleanBarcode}999'
                WHERE barcode = '${cleanBarcode}';<br>
                UPDATE `inventory_htl`.`barcodes`
                SET barcode = '${cleanBarcode}'
                WHERE id = '${id}';<br>";
    }

    /**
     * @param $id
     * @param $barcode
     * @return string
     */
    public function prepareUpdatesBarcodes2($barcode) {

        $splitBarcode = explode("|", $barcode);
        $cleanBarcode = $splitBarcode[0];

        return "${cleanBarcode}</br>";

//        return "
//                UPDATE `inventory_htl`.`barcodes`
//                SET barcode = '${cleanBarcode}'
//                WHERE barcode = '${barcode}';<br>";
    }

    /**
     * @param $item
     * @param $price
     * @param $make
     * @param $bank
     * @return string
     */
    public function updatesPriceHTLKEYS($item, $price, $make, $cat, $bank) {

        if( substr_count($price, '.') < 1 ){
            $price = $price.".00";
        }

        if ( (strlen($price) - strrpos($price, '.') - 1) == 1){
            $price = $price."0";
        }

        return "
        UPDATE `inventory`.`custom_price`
        SET `price` = '${price}'
        WHERE `item` = '${item}'
        AND `make` = '${make}'
        AND `cat` = '${cat}'
        AND `bank` = '${bank}';<br>";

    }



}
