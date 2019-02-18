<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');

    // CREATE item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newObj = new CartItem;
    $receivedItem = json_decode($_POST['3DItem']);

    $newObj->model = $receivedItem->model;
    $newObj->estimatedTime = $receivedItem->estimatedTime;
    $newObj->estimatedFilament = $receivedItem->estimatedFilament;
    $newObj->estimatedCost = $receivedItem->estimatedCost;

    $myCart->addCartItem($newObj);
    echo($myCart->length()); // return number of items in cart
}

// READ item
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['id'])) {
        $itemKey = $_GET['id'];
    } else {
        foreach($_GET as $key => $value){
            $itemKey = $key;
        }
    }
    echo(json_encode($myCart->getCartItem($itemKey)));
}

// UPDATE item
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $put_vars = array();
    $_PUT = file_get_contents("php://input");
    $parameters = explode("------", $_PUT);
    foreach ($parameters as $line) {
        $identifier="name=";
        $namepos = strpos($line, $identifier) + strlen($identifier)+1;
        if ($namepos-(strlen($identifier)+1) != 0) {
            $namelength = strpos($line, "\n", $namepos) - $namepos;
            $parameterName = substr($line, $namepos, $namelength-2);
            $parameterValue = trim(substr($line, $namepos+$namelength ));
            if (($parameterValue)!=="") {
                $put_vars[$parameterName] =  $parameterValue;
            }
        }
    }
    $itemKey = $put_vars['id'];

    // loop through passed values and set matching cart properties
    foreach ($myCart->getCartItem($itemKey) as $key => $value) {
        if (isset($put_vars[$key])) {
            $cartItem = $myCart->getCartItem($itemKey);
            $cartItem->$key = $put_vars[$key];
            // updated parameter available
            $myCart->updateCartItem($cartItem,$itemKey);
        }
    }
    // return itemcost and total cost

    echo ('{ "itemCost" : "' . number_format($myCart->getCartItem($itemKey)->getItemTotal() ,2,".","") . '" , "totalCost" : "' . $myCart->calculateTotal() .'" }');
}

// DELETE item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $delete_vars = array();
    $_DELETE = file_get_contents("php://input");
    $parameters = explode("------", $_DELETE);
    foreach ($parameters as $line) {
        $identifier="name=";
        $namepos = strpos($line, $identifier) + strlen($identifier)+1;
        if ($namepos-(strlen($identifier)+1) != 0) {
            $namelength = strpos($line, "\n", $namepos) - $namepos;
            $parameterName = substr($line, $namepos, $namelength-2);
            $parameterValue = trim(substr($line, $namepos+$namelength ));
            if (($parameterValue)!=="") {
                $delete_vars[$parameterName] =  $parameterValue;
            }
        }
    }
    $itemKey = $delete_vars['id'];
    $myCart->deleteCartItem($itemKey);
    echo ('{ "cartSize" : ' . $myCart->length() . ', "totalCost" : "' . $myCart->calculateTotal() .'" }');
}

?>
