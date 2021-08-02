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

            /*
             * Allowed file types extension ( CSV,XLS, XLSX, TSV)
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::CSV);
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::XLSX);
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::XLS);
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::TSV);
            */

            // Indicate Type of Excel File Extension , CSV
            $data = Excel::toArray('', $path, null, \Maatwebsite\Excel\Excel::CSV)[0];



            if(!empty($data)) {

                $dataImported= "";
                // Looping Row by Row
                for($i=0;$i<count($data);$i++) {
                    $column0 = $data[$i][0]; // movieID
                    //$column1 = $data[$i][1];
                    $column2 = $data[$i][2]; // addedUserID
                    //$column3 = $data[$i][3];
                    $column4 = $data[$i][4]; // updateUserID
                    $column5 = $data[$i][5]; // siteID
                    //$column6 = $data[$i][6];
                    //$column7 = $data[$i][7];
                    //$column8 = $data[$i][8];
                    //$column9 = $data[$i][9];
                    //$column10 = $data[$i][10];
                    //$column11 = $data[$i][11];
                    $column29  = $data[$i][29]; // movieFilename

                    $dataImported .= $this->prepareMoviesDataArray($column0, $column2, $column4, $column5, $column29);
                    //$dataImported .= $this->getModelsFromMoviesID($column0);
                    //$dataImported .= $this->updateStatusLost($column0);
                    //$dataImported .= $this->UpdatesQOH($column0, $column1);
                    //$dataImported .= $this->InsertHistoryQOH($column0, $column1);
                    //$dataImported .= $this->prepareInsertsIfnotExist($column1);
                    //$dataImported .= $this->prepareUpdates($column0,$column1,$column2);
                    //$dataImported .= $this->prepareInserts($column0, $column1, $column2);
                    //$dataImported .= $this->getqtyItems($column0);
                    //$dataImported .= $this->prepareUpdatesHistory($column5,$column3);
                    //$dataImported .= prepareUpdatesQOH($item, $qty);
                    //$dataImported .= $this->prepareInsertHistoryQOH($column0, $column2);
                    //$dataImported .= $this->prepareUpdatesQOH($column0, $column2);
                    //$dataImported .= $this->UpdatesPriceD($column0, $column3);
                    //$dataImported .= $this->InsertVehicleItemCount($column10, $column8);
                    //$dataImported .= $this->InsertPackageScan($column0, $column3);
                    //$dataImported .= $this->InsertLCPull($column0, $column3);
                    //$dataImported .= $this->InsertNewPriceList($column3, $column4, $column5, $column6, $column7);
                    //$dataImported .= $this->prepareUpdatesBarcodes($column0, $column1);
                    //$dataImported .= $this->updatesPriceHTLKEYS($column3, $column4, $column6, $column7, $bank='trc');
                    //$dataImported .= $this->prepareDeletes($column4);
                    //$dataImported .= $this->prepareUpdatesBarcodes2($column1);
                    //$dataImported .= $this->updateSitesTier($data[$i][2], $data[$i][7]);
                    //die();

                }

                //return Excel::download(collect($duplicates), 'export_file.xlsx');

                echo $dataImported;

                die();
            }
        }

    }



    /**
     * Build a large Array , based on CSV Data information
     */
    public function prepareMoviesDataArray($column0, $column2, $column4, $column5, $column29) {
        return "[<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'movieID' => {$column0},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'addedUserID' => {$column2},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'updateUserID' => {$column4},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'siteID' => {$column5},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'movieStatus' => 'active',<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'master_status' => 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'publishedDate' => '2017-02-06 11:00:00', <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'isPublished' => 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'loc' => 'web1',<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'verified' => 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'releaseDate' => '2017-02-06 11:00:00',<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'defaultPrice' => 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'movieFilename' => {$column29},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'modelID' => Model::where('slug', '{$column29}')->first()->modelID<br>
                ],<br>";
    }



    /*
     * UPDATE Mysql , Makes individual list of UPDATE QUERYS For update Inventory
     */
    public function getModelsFromMoviesID($movieID) {
        $sampleSQL= "SELECT * FROM `movies_models`
                WHERE movieID IN (12297, 12298, 12299) ;";
        return "{$movieID},";
    }


    /**
     * UPDATE Mysql , Makes individual list of UPDATE QUERYS For update Inventory
     */
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
