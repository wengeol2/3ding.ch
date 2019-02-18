<!doctype html>
<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/colors.php');

?>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Olivier Wenger">
    <title>3Ding.ch - Warenkorb</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/regular.css" integrity="sha384-4Cp0kYV2i1JFDfp6MQAdlrauJM+WTabydjMk5iJ7A9D+TXIh5zQMd5KXydBCAUN4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/fontawesome.css" integrity="sha384-iD1qS/uJjE9q9kecNUe9R4FRvcinAvTcPClTz7NI8RI5gUsJ+eaeJeblG1Ex0ieh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/3ding.css">
    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">
  </head>
  <body class="d-flex flex-column h-100">
    <?php
        // === TOP NAVIGATION ===
        $activePage = "cart";
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/navigation.php');
    ?>
    <script>
        function _(el) {
            return document.getElementById(el);
        }

        function updateColor(itemId, selectElement) {
            //console.log ("update " + itemId + ": set color to " + selectElement.value)
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //_("cartItem").innerHTML = "<pre>" + this.responseText + "</pre>"
                }
            }
            var formdata = new FormData();
            formdata.append("id", itemId)
            formdata.append("color", selectElement.value)
            ajax.open("PUT", "/cart/cartItem.php");
            ajax.send(formdata);
        }

        function updateAmount(itemId, selectElement) {
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let result = JSON.parse(this.responseText);
                    _("costEstimate_" + itemId).innerHTML = result.itemCost;
                    _("totalCost").innerHTML = result.totalCost + "";
                    console.log("Set estimate to " + result.itemCost)
                    console.log("set total cost to " + result.totalCost)
                }
            }
            var formdata = new FormData();
            formdata.append("id", itemId)
            formdata.append("amount", selectElement.value)
            ajax.open("PUT", "/cart/cartItem.php")
            ajax.send(formdata);
        }

        function removeItem(itemId, selectElement) {
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    _("cartRow_" + itemId).remove()
                    let result = JSON.parse(this.responseText);
                    _("totalCost").innerHTML = result.totalCost;
                    _("cartCounter").innerHTML = result.cartSize;
                    console.log("set total cost to" + result.totalCost)
                    console.log("set cart counter to " + result.cartSize)
                }
            }
            var formdata = new FormData();
            formdata.append("id", itemId)
            ajax.open("DELETE", "/cart/cartItem.php")
            ajax.send(formdata);
        }

        function updateCart() {
            // === update item in the cart
            var ajaxGetItem = new XMLHttpRequest();
            ajaxGetItem.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("cartItem").innerHTML = "<pre>" + this.responseText + "</pre>"
                }
            }
            var formdata = new FormData();
            formdata.append("id", 0)
            formdata.append("amount", 6)
            formdata.append("color", "blue")
            ajaxGetItem.open("PUT", "/cart/cartItem.php");
            ajaxGetItem.send(formdata);
        }
        $(document).ready(function () {
            $(".numberInput").inputSpinner();
        })

    </script>
    <!-- Begin page content -->
    <main role="main" class="flex-shrink-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mt-5">Warenkorb</h1>
                    <p class="text-muted"><i class="far fa-map-signs text-warning"></i> Geschätzte Angabe  <i class="far fa-check-circle text-success"></i> Geprüfte Angabe</p>
                    <!--pre><?php print_r($myCart) ?></pre-->
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Modell</th>
                                <th scope="col" class="text-center">Zeit</th>
                                <th scope="col" class="text-center">Filament</th>
                                <th scope="col" class="text-center" colspan=2>Stückpreis</th>
                                <th scope="col" class="text-center">Farbe</th>
                                <th scope="col" class="text-center">Stück</th>
                                <th scope="col" colspan=2 class="text-center">Total</th>
                                <th scope="col" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalCost = 0;
                            $statusIcon2 = "fa-check-circle text-success";
                            foreach($myCart->getCartItems() as $key=>$cartItem) {
                                // check if confirmed values are available
                                if ($cartItem->calculated) {
                                    $cTime = $cartItem->calculatedTime;
                                    $cTHours = floor($cTime / 3600);
                                    $cTMinutes = round($cTime / 60) - ($cTHours * 60);
                                    $Time = $cTHours . "h " . str_pad($cTMinutes, 2, '0', STR_PAD_LEFT) . "min";
                                    $Fil = number_format($cartItem->calculatedFilament / 1000,2,".","'") . "m";
                                    $Cost = number_format($cartItem->getItemCost() ,2,".","'");
                                    $statusIcon = "fa-check-circle text-success";
                                } else {
                                    $eTime = $cartItem->estimatedTime;
                                    $eTHours = floor($eTime / 3600);
                                    $eTMinutes = round($eTime / 60) - ($eTHours * 60);
                                    $Time = $eTHours . "h " . str_pad($eTMinutes, 2, '0', STR_PAD_LEFT) . "min";
                                    $Fil = number_format($cartItem->estimatedFilament / 1000,2,".","'") . "m";
                                    $Cost = number_format($cartItem->getItemCost() ,2,".","'");
                                    $statusIcon = "fa-map-signs text-warning";
                                    $statusIcon2 = $statusIcon;
                                }

                                $totalCost = number_format($myCart->calculateTotal() ,2,".","'");
                                ?>
                            <tr id="cartRow_<?= $key ?>">
                                <td><?= $cartItem->model ?></td>
                                <td class="text-right"><?= $Time ?> <i class="far <?= $statusIcon ?>"></i></td>
                                <td class="text-right"><?= $Fil ?> <i class="far <?= $statusIcon ?>"></i></td>
                                <td class="text-right">CHF</td>
                                <td class="text-right"><?= $Cost ?> <i class="far <?= $statusIcon ?>"></i></td>
                                <td>
                                    <select class="custom-select" id="colorSelector_<?= $key ?>" onchange="updateColor(<?= $key ?>, this)">
                                    <?php foreach($availableColors as $color) { ?>
                                        <option value="<?=$color->id?>" <?php if($color->id == $cartItem->color) { echo "selected"; }?>><div style="width:20px; height:20px; background-color:<?= $color->color ?>;"><?= $color->name ?></option>
                                    <?php }; ?>
                                    </select></td>
                                <td style="width:150px;"><input class="numberInput" id="amount_<?= $key ?>" type="number" value="<?= $cartItem->amount ?>" min="1" max="100" step="1" onchange="updateAmount(<?= $key?>, this)"/></td>
                                <td class="text-right table-pricing">CHF</td>
                                <td class="text-right table-pricing"><span id="costEstimate_<?=$key?>"><?= number_format($cartItem->amount * $cartItem->estimatedCost,2,".","'") ?></span> <i class="far <?= $statusIcon ?>"></i></td>
                                <td class="text-right table-pricing"><button class="btn btn-outline-danger btn-sm" onclick="removeItem(<?= $key ?>, this)"><i class="far fa-trash-alt " /></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan=7></td>
                                <th class="text-right">CHF</th>
                                <th class="text-right"><span id="totalCost"><?= number_format($totalCost ,2,".","'") ?></span> <i class="far <?= $statusIcon2 ?>"></i></th>
                                <th class="text-right"></th>
                            </tr>
                            <tr>
                                <td colspan=7></td>
                                <td colspan="3"><button class="btn btn-success">Bestellen</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <span id="cartItem"></span>
    </main>
    <?php
        // === FOOTER ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/footer.php');
    ?>
    <?php
        // === LOGIN MODAL ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/loginModal.php');
    ?>
</body>
</html>
