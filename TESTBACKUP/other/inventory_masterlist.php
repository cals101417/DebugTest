<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        include 'includes/disable_access.php';
        ?>
        <!-- Main Container -->
        <main id="main-container">
            <?php
            if (isset($_GET['edit'])){ // ---------------------------------- EDIT inventory DETAILS CONTENT ----------------------------------------
                $item_id = $_GET['edit'];
            //This query is for fetching inventory data
                $inventory_details = $conn->query("SELECT
                                                            tbl_inventory.inventory_id,
                                                            tbl_inventory.item,
                                                            tbl_inventory.description,
                                                            tbl_inventory.min_qty,
                                                            tbl_inventory.img_src,
                                                            tbl_inventory.item_currency
                                                            FROM
                                                            tbl_inventory
                                                            WHERE
                                                            tbl_inventory.inventory_id = $item_id");
                $inventory_fetch = $inventory_details->fetch();
                $inventory_item = $inventory_fetch['item'];
                $inventory_description = $inventory_fetch['description'];
                $inventory_id = $inventory_fetch['inventory_id'];
                $inventory_min_qty = $inventory_fetch['min_qty'];
                $inventory_img_src = $inventory_fetch['img_src'];
                $inventory_currency = $inventory_fetch['item_currency'];
                // $inventory_fullname = ucwords(strtolower($inventory_first.' '.$inventory_last));
            ?>

                <!-- Hero -->
                <div class="bg-image" style="background-image: url('assets/media/photos/construction4.jpeg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Edit Item Details</h1>
                        </div>
                    </div>
                </div>
            </div>

                <div class="content">
                    <nav class="breadcrumb push mb-0 pl-0">
                        <a class="breadcrumb-item" href="inventory_masterlist.php">Inventory List</a>
                        <span class="breadcrumb-item active">Edit</span>
                    </nav>
                    <!-- Update Product -->
                    <h2 class="content-heading mb-0">
                        <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                        Update Item Details
                    </h2>
                    <div class="content content-full text-center">
                        <div class="mb-15">
                          
                            <!-- <h1 class="h3 text-muted font-w700 mb-10"><?=$inventory_description?></h1> -->
                        </div>
                    </div>
                            <!------------------------------ UPDATE FORM------------------------------>
                    <form id="update_form">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="block">
                                    <div class="block-content block-content-full">
                                    <img class="img-avatar img-avatar96 img-avatar-thumb" src="assets/media/photos/inventory/<?=$inventory_img_src?>" alt="">
                                        <input type="hidden" name="update_inventory" value="<?=$inventory_id?>">
                                        <div class="row justify-content-center ">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="be-contact-name">Item</label>
                                                    <input type="text" class="form-control" id="item" name="item" value="<?=$inventory_item?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="be-contact-name">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?=$inventory_description?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="be-contact-name">Min Qty</label>
                                                    <input type="text" class="form-control" id="min_qty" name="min_qty" value="<?=$inventory_min_qty?>" required>
                                                </div>
                                                <div class="form-group">
                                                <label for="currency">Currency</label>
                                                    <select class="js-select2 form-control "  id="edit_currency" name="currency" required  style="width: 100%;" data-placeholder="Select Currency">
                                                        <option value=""></option>
                                                        <option value="AED">United Arab Emirates dirham</option>
                                                        <option value="AFN">Afghan afghani</option>
                                                        <option value="ALL">Albanian lek</option>
                                                        <option value="AMD">Armenian dram</option>
                                                        <option value="AOA">Angolan kwanza</option>
                                                        <option value="ARS">Argentine peso</option>
                                                        <option value="AUD">Australian dollar</option>
                                                        <option value="AWG">Aruban florin</option>
                                                        <option value="AZN">Azerbaijani manat</option>
                                                        <option value="BAM">Bosnia and Herzegovina convertible mark</option>
                                                        <option value="BBD">Barbadian dollar</option>
                                                        <option value="BDT">Bangladeshi taka</option>
                                                        <option value="BGN">Bulgarian lev</option>
                                                        <option value="BHD">Bahraini dinar</option>
                                                        <option value="BIF">Burundian franc</option>
                                                        <option value="BMD">Bermudian dollar</option>
                                                        <option value="BND">Brunei dollar</option>
                                                        <option value="BOB">Bolivian boliviano</option>
                                                        <option value="BRL">Brazilian real</option>
                                                        <option value="BSD">Bahamian dollar</option>
                                                        <option value="BTN">Bhutanese ngultrum</option>
                                                        <option value="BWP">Botswana pula</option>
                                                        <option value="BYR">Belarusian ruble</option>
                                                        <option value="BZD">Belize dollar</option>
                                                        <option value="CAD">Canadian dollar</option>
                                                        <option value="CDF">Congolese franc</option>
                                                        <option value="CHF">Swiss franc</option>
                                                        <option value="CLP">Chilean peso</option>
                                                        <option value="CNY">Chinese yuan</option>
                                                        <option value="COP">Colombian peso</option>
                                                        <option value="CRC">Costa Rican colón</option>
                                                        <option value="CUP">Cuban convertible peso</option>
                                                        <option value="CVE">Cape Verdean escudo</option>
                                                        <option value="CZK">Czech koruna</option>
                                                        <option value="DJF">Djiboutian franc</option>
                                                        <option value="DKK">Danish krone</option>
                                                        <option value="DOP">Dominican peso</option>
                                                        <option value="DZD">Algerian dinar</option>
                                                        <option value="EGP">Egyptian pound</option>
                                                        <option value="ERN">Eritrean nakfa</option>
                                                        <option value="ETB">Ethiopian birr</option>
                                                        <option value="EUR">Euro</option>
                                                        <option value="FJD">Fijian dollar</option>
                                                        <option value="FKP">Falkland Islands pound</option>
                                                        <option value="GBP">British pound</option>
                                                        <option value="GEL">Georgian lari</option>
                                                        <option value="GHS">Ghana cedi</option>
                                                        <option value="GMD">Gambian dalasi</option>
                                                        <option value="GNF">Guinean franc</option>
                                                        <option value="GTQ">Guatemalan quetzal</option>
                                                        <option value="GYD">Guyanese dollar</option>
                                                        <option value="HKD">Hong Kong dollar</option>
                                                        <option value="HNL">Honduran lempira</option>
                                                        <option value="HRK">Croatian kuna</option>
                                                        <option value="HTG">Haitian gourde</option>
                                                        <option value="HUF">Hungarian forint</option>
                                                        <option value="IDR">Indonesian rupiah</option>
                                                        <option value="ILS">Israeli new shekel</option>
                                                        <option value="IMP">Manx pound</option>
                                                        <option value="INR">Indian rupee</option>
                                                        <option value="IQD">Iraqi dinar</option>
                                                        <option value="IRR">Iranian rial</option>
                                                        <option value="ISK">Icelandic króna</option>
                                                        <option value="JEP">Jersey pound</option>
                                                        <option value="JMD">Jamaican dollar</option>
                                                        <option value="JOD">Jordanian dinar</option>
                                                        <option value="JPY">Japanese yen</option>
                                                        <option value="KES">Kenyan shilling</option>
                                                        <option value="KGS">Kyrgyzstani som</option>
                                                        <option value="KHR">Cambodian riel</option>
                                                        <option value="KMF">Comorian franc</option>
                                                        <option value="KPW">North Korean won</option>
                                                        <option value="KRW">South Korean won</option>
                                                        <option value="KWD">Kuwaiti dinar</option>
                                                        <option value="KYD">Cayman Islands dollar</option>
                                                        <option value="KZT">Kazakhstani tenge</option>
                                                        <option value="LAK">Lao kip</option>
                                                        <option value="LBP">Lebanese pound</option>
                                                        <option value="LKR">Sri Lankan rupee</option>
                                                        <option value="LRD">Liberian dollar</option>
                                                        <option value="LSL">Lesotho loti</option>
                                                        <option value="LTL">Lithuanian litas</option>
                                                        <option value="LVL">Latvian lats</option>
                                                        <option value="LYD">Libyan dinar</option>
                                                        <option value="MAD">Moroccan dirham</option>
                                                        <option value="MDL">Moldovan leu</option>
                                                        <option value="MGA">Malagasy ariary</option>
                                                        <option value="MKD">Macedonian denar</option>
                                                        <option value="MMK">Burmese kyat</option>
                                                        <option value="MNT">Mongolian tögrög</option>
                                                        <option value="MOP">Macanese pataca</option>
                                                        <option value="MRO">Mauritanian ouguiya</option>
                                                        <option value="MUR">Mauritian rupee</option>
                                                        <option value="MVR">Maldivian rufiyaa</option>
                                                        <option value="MWK">Malawian kwacha</option>
                                                        <option value="MXN">Mexican peso</option>
                                                        <option value="MYR">Malaysian ringgit</option>
                                                        <option value="MZN">Mozambican metical</option>
                                                        <option value="NAD">Namibian dollar</option>
                                                        <option value="NGN">Nigerian naira</option>
                                                        <option value="NIO">Nicaraguan córdoba</option>
                                                        <option value="NOK">Norwegian krone</option>
                                                        <option value="NPR">Nepalese rupee</option>
                                                        <option value="NZD">New Zealand dollar</option>
                                                        <option value="OMR">Omani rial</option>
                                                        <option value="PAB">Panamanian balboa</option>
                                                        <option value="PEN">Peruvian nuevo sol</option>
                                                        <option value="PGK">Papua New Guinean kina</option>
                                                        <option value="PHP">Philippine peso</option>
                                                        <option value="PKR">Pakistani rupee</option>
                                                        <option value="PLN">Polish złoty</option>
                                                        <option value="PRB">Transnistrian ruble</option>
                                                        <option value="PYG">Paraguayan guaraní</option>
                                                        <option value="QAR">Qatari riyal</option>
                                                        <option value="RON">Romanian leu</option>
                                                        <option value="RSD">Serbian dinar</option>
                                                        <option value="RUB">Russian ruble</option>
                                                        <option value="RWF">Rwandan franc</option>
                                                        <option value="SAR">Saudi riyal</option>
                                                        <option value="SBD">Solomon Islands dollar</option>
                                                        <option value="SCR">Seychellois rupee</option>
                                                        <option value="SDG">Singapore dollar</option>
                                                        <option value="SEK">Swedish krona</option>
                                                        <option value="SGD">Singapore dollar</option>
                                                        <option value="SHP">Saint Helena pound</option>
                                                        <option value="SLL">Sierra Leonean leone</option>
                                                        <option value="SOS">Somali shilling</option>
                                                        <option value="SRD">Surinamese dollar</option>
                                                        <option value="SSP">South Sudanese pound</option>
                                                        <option value="STD">São Tomé and Príncipe dobra</option>
                                                        <option value="SVC">Salvadoran colón</option>
                                                        <option value="SYP">Syrian pound</option>
                                                        <option value="SZL">Swazi lilangeni</option>
                                                        <option value="THB">Thai baht</option>
                                                        <option value="TJS">Tajikistani somoni</option>
                                                        <option value="TMT">Turkmenistan manat</option>
                                                        <option value="TND">Tunisian dinar</option>
                                                        <option value="TOP">Tongan paʻanga</option>
                                                        <option value="TRY">Turkish lira</option>
                                                        <option value="TTD">Trinidad and Tobago dollar</option>
                                                        <option value="TWD">New Taiwan dollar</option>
                                                        <option value="TZS">Tanzanian shilling</option>
                                                        <option value="UAH">Ukrainian hryvnia</option>
                                                        <option value="UGX">Ugandan shilling</option>
                                                        <option value="USD">United States dollar</option>
                                                        <option value="UYU">Uruguayan peso</option>
                                                        <option value="UZS">Uzbekistani som</option>
                                                        <option value="VEF">Venezuelan bolívar</option>
                                                        <option value="VND">Vietnamese đồng</option>
                                                        <option value="VUV">Vanuatu vatu</option>
                                                        <option value="WST">Samoan tālā</option>
                                                        <option value="XAF">Central African CFA franc</option>
                                                        <option value="XCD">East Caribbean dollar</option>
                                                        <option value="XOF">West African CFA franc</option>
                                                        <option value="XPF">CFP franc</option>
                                                        <option value="YER">Yemeni rial</option>
                                                        <option value="ZAR">South African rand</option>
                                                        <option value="ZMW">Zambian kwacha</option>
                                                        <option value="ZWL">Zimbabwean dollar</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group ">
                                                    <label class="col-12" for="item_img">Image</label>
                                                    <input type="file" id="inventory_img" name="inventory_img">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button   type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                    <i class="fa fa-save mr-5"></i> Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">

                            </div>
                        </div>
                    </form>
                    <!-- END Update Product-->
                </div>
                <script>
                    document.getElementById('edit_currency').value= '<?=$inventory_currency?>';
                </script>
            <?php
            }else{ // ---------------------------------- INVENTORY LIST CONTENT ----------------------------------------
            ?>
             <div class="bg-image" style="background-image: url('assets/media/photos/construction4.jpeg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">INVENTORY</h1>
                           
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page Content -->
            <div class="content">
                <?php
//                FETCH TOTAL STOCKS
            $fetch_total_qry = $conn->query("SELECT count(`inventory_id`) as total_stocks FROM `tbl_inventory` WHERE is_removed = 0");
            $total_stocks = $fetch_total_qry->fetch();
            $total_stock = $total_stocks['total_stocks'];

//                FETCH AVAILABLE ITEMS
            $total_available_qry = $conn->query("SELECT count(`inventory_id`) as total_available FROM `tbl_inventory` WHERE is_removed = 0 AND min_qty != 0");
            $fetch_total_available = $total_available_qry->fetch();
            $total_stock_available = $fetch_total_available['total_available'];

//                 FETCH OUT of stock ITEMS
            $total_unavailable_qry = $conn->query("SELECT count(`inventory_id`) as total_unavailable FROM `tbl_inventory` WHERE is_removed = 0 AND min_qty = 0");
            $fetch_total_unavailable = $total_unavailable_qry->fetch();
            $total_out_of_stock = $fetch_total_unavailable['total_unavailable'];

            //user access role
            $add_item_disable = '';
            $add_item_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 17 AND `status` = 0");
            if ($add_item_access->rowCount() > 0){
                $add_item_access = $add_item_access->fetch();
                $add_item_status = $add_item_access['status'];
                if ($add_item_status == 1){
                    $add_item_disable = 'disabled';
                }
            }else{
                $add_item_disable = 'disabled';
            }

                ?>
                <!-- Overview -->
                <h2 class="content-heading">Overview</h2>
                <div class="row gutters-tiny justify-content-center">
                    <!-- All Items -->
                       <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="fa fa-circle-o fa-2x text-elegance-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-elegance" data-toggle="countTo" data-speed="1000" data-to="<?=$total_stock?>">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">ALL ITEMS</div>
                            </div>
                        </a>
                    </div>  
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="fa fa-star fa-2x text-warning-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-warning" data-toggle="countTo" data-speed="1000" data-to="<?=$total_stock_available?>">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">  Available Items</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="fa fa-warning fa-2x text-danger-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-danger-light" data-toggle="countTo" data-speed="1000" data-to="<?=$total_out_of_stock?>">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">  OUT OF STOCK</div>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- END Overview -->

                <!-- Products -->
                <div class="content-heading">
                    Items (<?=$total_stock?>)

                     <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" <?=$add_item_disable ?> data-target="#add_new_item_modal">Add New Item</button>
                </div>
                <div class="block block-rounded">
                    <div class="block-content block-content-full table-responsive">
                        <!-- Products Table -->
                        <table id="tbl_items" class="table table-borderless table-striped table-vcenter ">
                            <thead class="text-center thead-light">
                            <tr>
                                <th>#</th>
                                <th style="width: 100px;">Ref. ID</th>
                                <th class="text-left">Item name</th>
                                <th>Image</th>
                                <th class="d-none d-sm-table-cell">Status</th>
                                <th class="d-none d-sm-table-cell">Min Quantity</th>
                                <th class="d-none d-sm-table-cell">Price</th>
                                <th class="d-none d-md-table-cell">Date Updated</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $fetch_items_qry = $conn->query("SELECT `inventory_id`, 
                                                                             `item`, 
                                                                             `min_qty`,
                                                                             `img_src`, 
                                                                             `date_created`, 
                                                                             `user_id`,
                                                                             `item_currency`,
                                                                             `item_price`
                                                                      FROM `tbl_inventory` WHERE is_removed = 0");
                            $items = $fetch_items_qry->fetchAll();
                            $item_count=1;
                           foreach ($items as $item){
                                $item_id = $item['inventory_id'];
                                $item_name = $item['item'];
                                $min_qty = $item['min_qty'];
                                $img_src = $item['img_src'];
                                $date_created = $item['date_created'];
                                $stock_status = '';
                                $current_stocks = 0;
                                
                                // GET ALL INBOUND AND OUTBOUND ACCORDING TO INVENTORY ID THEN COMPUTE THE CURRENT STOCKS

                               $query_stocks =  $conn->query("SELECT `inventory_id`, `type`, `qty` FROM `tbl_inventory_bound` WHERE inventory_id ='".$item_id."'");
                               $stocks = $query_stocks->fetchAll();
                               $inbound= 0;
                               $outbound= 0;
                                foreach ($stocks as $stock){
                                    if($stock['type'] == "inbound"){
                                        $inbound = $stock['qty'] + $inbound;
                                    } else {
                                        $outbound = $stock['qty'] + $outbound;
                                    }
                                }
                                $current_stocks = $inbound - $outbound;
                                if ($current_stocks <= 0){
                                    $stock_status = '<span class="badge badge-danger">Out of Stock</span>';
                                }else{
                                    $stock_status = '<span class="badge badge-success">Available</span>';
                                }
                           ?>
                                <tr>
                                    <td><?=$item_count++?></td>
                                    <td class="text-center"><?=$item_id?></td>
                                    <td class="font-w700"><?=$item_name?></td>
                                    <td>
                                        <img class="img pd-l-30" shape="circle" src="assets/media/photos/inventory/<?=$img_src?>" style="height: 60px; !important">
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center"><?=$stock_status?></td>
                                    <td class="d-none d-md-table-cell text-center"><?=$min_qty?></td>
                                    <td class="d-none d-md-table-cell text-center"><?=$item['item_currency'].$item['item_price']?></td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <?=date('M d, Y',strtotime($date_created))?>
                                    </td>
                                    <?php
                                        // USER ROLE ACCESS
                                    $edit_item_disable = '';
                                    $edit_item_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $user_id AND access_type_id = 19 AND `status` = 0");
                                    if ($edit_item_access->rowCount() > 0){
                                        $edit_item_access = $edit_item_access->fetch();
                                        $edit_tem_status = $edit_item_access['status'];
                                        if ($edit_tem_status == 1){
                                            $edit_item_disable = 'hidden';
                                        }
                                    }else{
                                        $edit_item_disable = 'hidden';
                                    }
                                           // USER ROLE ACCESS
                                    $delete_item_disable = '';
                                    $delete_item_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 18 AND `status` = 0");
                                    if ($delete_item_access->rowCount() > 0){
                                        $delete_item_access = $delete_item_access->fetch();
                                        $delete_status = $delete_item_access['status'];
                                        if ($delete_status == 1){
                                            $delete_item_disable = 'disabled';
                                        }
                                    }else{
                                        $delete_item_disable = 'disabled';
                                    }
                                    ?>
                                    <td class="text-right">
                                        <div class="btn-group">
                                      
                                        </button>
                                        <a href="inventory_masterlist.php?edit=<?=$item_id?>" type="button" <?=$edit_item_disable ?> class="btn btn-sm btn-success js-tooltip-enabled" data-toggle="tooltip"
                                           onclick="set_currency('<?=$item['item_currency'] ?>')"
                                        title="Edit inventory Record" data-original-title="Edit" >
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                      
                                        <button type="button" class="btn btn-danger btn-sm js-tooltip-enabled" onclick="remove_item(<?=$item_id?>,<?=$min_qty?>)" <?=$delete_item_disable ?> data-toggle="tooltip" title="Remove item from the list" data-original-title="Remove item from the list">
                                                <span class="si si-trash"></span>
                                        </button>
                                        </div>
                                    </td>
                                </tr>
                           <?php
                           }
                           ?>
                            </tbody>
                        </table>
                        <!-- END Products Table -->

                    </div>
                </div>
                <!-- END Products -->
            </div>
            <!-- END Page Content -->
            <?php
            }
            ?>

        </main>
        <!-- END Main Container -->
    </div>
     <!-- Add New Item Modal -->
    <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">
     <div class="modal fade" id="add_new_item_modal"  role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New Item Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_new_item_form">
                            <input type="hidden" name="add_new_item" value="1">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="item_name">Item name</label>
                                    <input type="text" class="form-control form-control-lg" id="item_name" name="item_name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="item_name">Description</label>
                                    <textarea class="form-control form-control-lg" id="desc" name="desc" placeholder="" required></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="min_qty">Quantity</label>
                                <div class="col-12">
                                    <input type="number" class="form-control form-control-lg" id="min_qty" name="min_qty" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="item_unit">Unit</label>
                                <div class="col-12">
                                    <select class="form-control col-12" id="item_unit" name = "item_unit">
                                        <option value = "Pcs">Pcs.</option>
                                        <option value = "Box">Box</option>
                                        <option value = "Meter">Meter</option>
                                        <option value = "Kgs.">Kgs.</option>
                                        <option value = "Grams">Grams.</option>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label class="col-12" for="item_price">Price</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg"  id="item_price" name="item_price">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="currency">Currency</label>
                                <div class="col-12">
<!--                                    <input type="text" class="form-control form-control-lg"  id="currency" name="currency">-->
                                    <select class=" js-select2 form-control form-control-lg"  id="currency" name="currency"  style="width: 100%;" data-placeholder="Select Currency">
                                        <option value=""></option>
                                        <option value="AED">United Arab Emirates dirham</option>
                                        <option value="AFN">Afghan afghani</option>
                                        <option value="ALL">Albanian lek</option>
                                        <option value="AMD">Armenian dram</option>
                                        <option value="AOA">Angolan kwanza</option>
                                        <option value="ARS">Argentine peso</option>
                                        <option value="AUD">Australian dollar</option>
                                        <option value="AWG">Aruban florin</option>
                                        <option value="AZN">Azerbaijani manat</option>
                                        <option value="BAM">Bosnia and Herzegovina convertible mark</option>
                                        <option value="BBD">Barbadian dollar</option>
                                        <option value="BDT">Bangladeshi taka</option>
                                        <option value="BGN">Bulgarian lev</option>
                                        <option value="BHD">Bahraini dinar</option>
                                        <option value="BIF">Burundian franc</option>
                                        <option value="BMD">Bermudian dollar</option>
                                        <option value="BND">Brunei dollar</option>
                                        <option value="BOB">Bolivian boliviano</option>
                                        <option value="BRL">Brazilian real</option>
                                        <option value="BSD">Bahamian dollar</option>
                                        <option value="BTN">Bhutanese ngultrum</option>
                                        <option value="BWP">Botswana pula</option>
                                        <option value="BYR">Belarusian ruble</option>
                                        <option value="BZD">Belize dollar</option>
                                        <option value="CAD">Canadian dollar</option>
                                        <option value="CDF">Congolese franc</option>
                                        <option value="CHF">Swiss franc</option>
                                        <option value="CLP">Chilean peso</option>
                                        <option value="CNY">Chinese yuan</option>
                                        <option value="COP">Colombian peso</option>
                                        <option value="CRC">Costa Rican colón</option>
                                        <option value="CUP">Cuban convertible peso</option>
                                        <option value="CVE">Cape Verdean escudo</option>
                                        <option value="CZK">Czech koruna</option>
                                        <option value="DJF">Djiboutian franc</option>
                                        <option value="DKK">Danish krone</option>
                                        <option value="DOP">Dominican peso</option>
                                        <option value="DZD">Algerian dinar</option>
                                        <option value="EGP">Egyptian pound</option>
                                        <option value="ERN">Eritrean nakfa</option>
                                        <option value="ETB">Ethiopian birr</option>
                                        <option value="EUR">Euro</option>
                                        <option value="FJD">Fijian dollar</option>
                                        <option value="FKP">Falkland Islands pound</option>
                                        <option value="GBP">British pound</option>
                                        <option value="GEL">Georgian lari</option>
                                        <option value="GHS">Ghana cedi</option>
                                        <option value="GMD">Gambian dalasi</option>
                                        <option value="GNF">Guinean franc</option>
                                        <option value="GTQ">Guatemalan quetzal</option>
                                        <option value="GYD">Guyanese dollar</option>
                                        <option value="HKD">Hong Kong dollar</option>
                                        <option value="HNL">Honduran lempira</option>
                                        <option value="HRK">Croatian kuna</option>
                                        <option value="HTG">Haitian gourde</option>
                                        <option value="HUF">Hungarian forint</option>
                                        <option value="IDR">Indonesian rupiah</option>
                                        <option value="ILS">Israeli new shekel</option>
                                        <option value="IMP">Manx pound</option>
                                        <option value="INR">Indian rupee</option>
                                        <option value="IQD">Iraqi dinar</option>
                                        <option value="IRR">Iranian rial</option>
                                        <option value="ISK">Icelandic króna</option>
                                        <option value="JEP">Jersey pound</option>
                                        <option value="JMD">Jamaican dollar</option>
                                        <option value="JOD">Jordanian dinar</option>
                                        <option value="JPY">Japanese yen</option>
                                        <option value="KES">Kenyan shilling</option>
                                        <option value="KGS">Kyrgyzstani som</option>
                                        <option value="KHR">Cambodian riel</option>
                                        <option value="KMF">Comorian franc</option>
                                        <option value="KPW">North Korean won</option>
                                        <option value="KRW">South Korean won</option>
                                        <option value="KWD">Kuwaiti dinar</option>
                                        <option value="KYD">Cayman Islands dollar</option>
                                        <option value="KZT">Kazakhstani tenge</option>
                                        <option value="LAK">Lao kip</option>
                                        <option value="LBP">Lebanese pound</option>
                                        <option value="LKR">Sri Lankan rupee</option>
                                        <option value="LRD">Liberian dollar</option>
                                        <option value="LSL">Lesotho loti</option>
                                        <option value="LTL">Lithuanian litas</option>
                                        <option value="LVL">Latvian lats</option>
                                        <option value="LYD">Libyan dinar</option>
                                        <option value="MAD">Moroccan dirham</option>
                                        <option value="MDL">Moldovan leu</option>
                                        <option value="MGA">Malagasy ariary</option>
                                        <option value="MKD">Macedonian denar</option>
                                        <option value="MMK">Burmese kyat</option>
                                        <option value="MNT">Mongolian tögrög</option>
                                        <option value="MOP">Macanese pataca</option>
                                        <option value="MRO">Mauritanian ouguiya</option>
                                        <option value="MUR">Mauritian rupee</option>
                                        <option value="MVR">Maldivian rufiyaa</option>
                                        <option value="MWK">Malawian kwacha</option>
                                        <option value="MXN">Mexican peso</option>
                                        <option value="MYR">Malaysian ringgit</option>
                                        <option value="MZN">Mozambican metical</option>
                                        <option value="NAD">Namibian dollar</option>
                                        <option value="NGN">Nigerian naira</option>
                                        <option value="NIO">Nicaraguan córdoba</option>
                                        <option value="NOK">Norwegian krone</option>
                                        <option value="NPR">Nepalese rupee</option>
                                        <option value="NZD">New Zealand dollar</option>
                                        <option value="OMR">Omani rial</option>
                                        <option value="PAB">Panamanian balboa</option>
                                        <option value="PEN">Peruvian nuevo sol</option>
                                        <option value="PGK">Papua New Guinean kina</option>
                                        <option value="PHP">Philippine peso</option>
                                        <option value="PKR">Pakistani rupee</option>
                                        <option value="PLN">Polish złoty</option>
                                        <option value="PRB">Transnistrian ruble</option>
                                        <option value="PYG">Paraguayan guaraní</option>
                                        <option value="QAR">Qatari riyal</option>
                                        <option value="RON">Romanian leu</option>
                                        <option value="RSD">Serbian dinar</option>
                                        <option value="RUB">Russian ruble</option>
                                        <option value="RWF">Rwandan franc</option>
                                        <option value="SAR">Saudi riyal</option>
                                        <option value="SBD">Solomon Islands dollar</option>
                                        <option value="SCR">Seychellois rupee</option>
                                        <option value="SDG">Singapore dollar</option>
                                        <option value="SEK">Swedish krona</option>
                                        <option value="SGD">Singapore dollar</option>
                                        <option value="SHP">Saint Helena pound</option>
                                        <option value="SLL">Sierra Leonean leone</option>
                                        <option value="SOS">Somali shilling</option>
                                        <option value="SRD">Surinamese dollar</option>
                                        <option value="SSP">South Sudanese pound</option>
                                        <option value="STD">São Tomé and Príncipe dobra</option>
                                        <option value="SVC">Salvadoran colón</option>
                                        <option value="SYP">Syrian pound</option>
                                        <option value="SZL">Swazi lilangeni</option>
                                        <option value="THB">Thai baht</option>
                                        <option value="TJS">Tajikistani somoni</option>
                                        <option value="TMT">Turkmenistan manat</option>
                                        <option value="TND">Tunisian dinar</option>
                                        <option value="TOP">Tongan paʻanga</option>
                                        <option value="TRY">Turkish lira</option>
                                        <option value="TTD">Trinidad and Tobago dollar</option>
                                        <option value="TWD">New Taiwan dollar</option>
                                        <option value="TZS">Tanzanian shilling</option>
                                        <option value="UAH">Ukrainian hryvnia</option>
                                        <option value="UGX">Ugandan shilling</option>
                                        <option value="USD">United States dollar</option>
                                        <option value="UYU">Uruguayan peso</option>
                                        <option value="UZS">Uzbekistani som</option>
                                        <option value="VEF">Venezuelan bolívar</option>
                                        <option value="VND">Vietnamese đồng</option>
                                        <option value="VUV">Vanuatu vatu</option>
                                        <option value="WST">Samoan tālā</option>
                                        <option value="XAF">Central African CFA franc</option>
                                        <option value="XCD">East Caribbean dollar</option>
                                        <option value="XOF">West African CFA franc</option>
                                        <option value="XPF">CFP franc</option>
                                        <option value="YER">Yemeni rial</option>
                                        <option value="ZAR">South African rand</option>
                                        <option value="ZMW">Zambian kwacha</option>
                                        <option value="ZWL">Zimbabwean dollar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="item_img">Image</label>
                                <div class="col-12">
                                    <input type="file" class="form-control form-control-lg"  id="item_img" name="item_img">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa-plus mr-5"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Add New Item Modal -->

    <!-- View inventory Details Modal -->
    <div class="modal fade" id="view_inventory_modal" tabindex="-1" role="dialog" aria-labelledby="add_inventory_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_inventory_name">inventory Details</span></h3>
                        <div class="block-options">
                            <?php
                            //                    ADD ACCESS TRAINING ENABLE DISABLE BUTTON
                            $print_access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $user_id AND access_type_id = 4");
                            if ($print_access->rowCount() > 0) {
                                $print_access_fetch = $print_access->fetch();
                                if ($print_access_fetch['status'] == 0){
                                ?>
                                <button type="button" class="btn-block-option" onclick="printDiv('print_div')">
                                    <i class="si si-printer"></i> Print
                                </button>
                                <?php
                                }
                            }
                            ?>
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row items-push">
                            <div class="col-xl-5 mt-0 mb-0" id="profile_info"></div>
                            <div class="col-xl-7 px-0 mb-0" id="basic_info"></div>
                        </div>
                        <!-- END User Info -->
                        <div class="content">
                            <!-- Cart -->
                            <h2 class="content-heading pt-0">Trainings</h2>
                            <div class="block block-rounded">
                                <!-- Trainings Table -->
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">#</th>
                                            <th style="width: 20%;">Title</th>
                                            <th class="text-center">Conducted by</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center" style="width: 20%;">Date Created</th>
                                            <th class="text-center" style="width: 15%;">Status</th>
                                            <th class="text-center" style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="trainings_info">
                                    </tbody>
                                </table>
                                <!-- END Trainings Table -->
                            </div>
                            <!-- END Cart -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END View inventory Details Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>


       <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <script>jQuery(function(){ Codebase.helpers('select2'); });</script>

    <script>
        $(document).ready(function () {
            // Datatable for item table
            $("th").attr("style","text-transform: capitalize");
            $("#update_form").submit(function (event) {

            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'ajax/inventory_ajax.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    console.log("Error adding inventory function");
                }
            });                      
        })
        // FUNCTION FOR ADDING NEW ITEMS
        $("#add_new_item_form").submit(function (event) {
            event.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'ajax/inventory_ajax.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    alert(response);
                    location.reload();
                },
                error: function () {
                    console.log("Error adding employee function");
                }
            });

        })
            $('#tbl_items').DataTable();
        });             
            // FUNCTION FOR SUBMITTING UPDATE       
        function set_currency(currency){

            $('#edit_currency select').val(currency);
        }
        function view_details(id){
            $('#view_inventory_modal').modal('show');
            inventory_profile_info(id);
            inventory_basic_info(id);
            inventory_trainings_info(id);
        }

        // VIEW inventory DETAILS AJAX FUNCTIONS
        function inventory_profile_info(id){
            $.ajax({
                type: "POST",
                url: "ajax/inventorys_ajax.php",
                data: {
                    inventory_id: id,
                    inventory_profile_info: 1,
                },
                success: function(data){
                    $('#profile_info').html(data);
                    // location.reload()
                }
            });
        }

        function inventory_basic_info(id){
            $.ajax({
                type: "POST",
                url: "ajax/inventorys_ajax.php",
                data: {
                    inventory_id: id,
                    inventory_basic_info: 1,
                },
                success: function(data){
                    $('#basic_info').html(data);
                    // location.reload()
                }
            });
        }

        function inventory_trainings_info(id){
            $.ajax({
                type: "POST",
                url: "ajax/inventorys_ajax.php",
                data: {
                    inventory_id: id,
                    inventory_trainings_info: 1,
                },
                success: function(data){
                    $('#trainings_info').html(data);
                    // location.reload()
                }
            });
        }
        // END VIEW inventory DETAILS AJAX FUNCTIONS

        function delete_inventory(id){
            if (confirm("Are you sure you want to remove this inventory?")){
                $.ajax({
                    type: "POST",
                    url: "ajax/inventory_ajax.php",
                    data: {
                        inventory_id: id,
                        delete_inventory: 1,
                    },
                    success: function(data){
                        alert(data);
                        location.href = 'inventory_masterlist.php';
                    }
                });
            }
        }
        function remove_item(id,min_qty){

            if (confirm("Are you sure you want to remove this item?")){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        inventory_id: id,
                        remove_item:1
                    },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        console.log("Error adding inventory function");
                    }
                });
            }
        }
        function printDiv(div){
            var printContents = document.getElementById(div).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }

    </script>
<?php
include 'includes/footer.php';