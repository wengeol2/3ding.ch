<!doctype html>
<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');
?>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Olivier Wenger">
    <title>3Ding.ch - 3D Druckkostenrechner</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/regular.css" integrity="sha384-4Cp0kYV2i1JFDfp6MQAdlrauJM+WTabydjMk5iJ7A9D+TXIh5zQMd5KXydBCAUN4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/fontawesome.css" integrity="sha384-iD1qS/uJjE9q9kecNUe9R4FRvcinAvTcPClTz7NI8RI5gUsJ+eaeJeblG1Ex0ieh" crossorigin="anonymous">
    <script src="stl_viewer.min.js"></script>
    <link href="/css/3ding.css" rel="stylesheet">
    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">
  </head>
  <body class="d-flex flex-column h-100">
    <?php // === TOP NAVIGATION ===
        $activePage = "calcualtor";
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/navigation.php');
    ?>
    <script>
        var STLFileName = "";
        var eTime =0; // time estimate
        var eFil = 0; // filament estimate
        var eCost =0; // cost estimate
        var stl_viewer;
        function _(el) {
            return document.getElementById(el);
        }

        function uploadFile() {
            console.log("uploading...");
            var file = _("file1").files[0];
            ajaxUploadCall(file);
        }

        function showNewFile() {
            $("#droprow").removeClass("d-none")
            $("#addFileRow").addClass("d-none")
        }

        function addItemToCart() {
            var formdata = new FormData()
            var obj3D = {
                model: STLFileName,
                estimatedTime: eTime,
                estimatedFilament: eFil,
                estimatedCost: eCost
            }
            formdata.append("3DItem", JSON.stringify(obj3D))
            var ajax = new XMLHttpRequest()
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    _('cartCounter').innerHTML = this.responseText
                    $.notify({
                        message: 'Item added to Cart',
                        icon: 'far fa-file-check'
                    },{
                        type: 'success',
                        offset: {
                            x: 20,
                            y: 70
                        }
                    });
                    _("addToCartBtn").disabled = true;
                }
            }
            ajax.open("POST", "/cart/cartItem.php");
            ajax.send(formdata);
        }

        function ajaxUploadCall(file) {
            STLFileName = file.name.replace(" ", "_");
            console.log("Load STL into viewer")
            // reset progress status
            $("#droprow").addClass("d-none")
            $("#addFileRow").removeClass("d-none")
            $("#lg_select").removeClass("list-group-item-success")
            $("#lg_upload").removeClass("list-group-item-success")
            $("#lg_parse").removeClass("list-group-item-success")
            $("#lg_select_ok").addClass("d-none")
            $("#lg_upload_ok").addClass("d-none")
            $("#lg_parse_ok").addClass("d-none")
            _("filament").innerHTML = ""
            _("printtime").innerHTML = ""
            _("dimensions").innerHTML = ""
            _("volume").innerHTML = ""
            _("estimate").innerHTML = ""

            if (typeof stl_viewer !== 'undefined') {
                stl_viewer.clean();
                stl_viewer.add_model({id:1, local_file:file })
            } else {
                stl_viewer = new StlViewer(document.getElementById("stl_cont"), {
                    models : [
                        {
                            id:1,
                            local_file:file
                        }
                    ],
                    bgcolor : '#CCCCCC',
                    canvas_width : '100%',
                    canvas_height : '100%',
                    auto_rotate : true
                } );
            }
            console.log("Setting STLFile to " + STLFileName)

            $("#fileNameLabel").innerHTML = file.name
            var formdata = new FormData()
            file.name = file.name.replace(' ', '_');
            formdata.append("file1", file)
            console.log(file.name)
            var ajax = new XMLHttpRequest()
            ajax.upload.addEventListener("progress", progressHandler, false)
            ajax.addEventListener("load", completeHandler, false)
            ajax.addEventListener("error", errorHandler, false)
            ajax.addEventListener("abort", abortHandler, false)
            ajax.open("POST", "/upload/")
            $("#lg_select_ok").removeClass("d-none")
            $("#lg_upload_spinner").removeClass("d-none")
            ajax.send(formdata)
            console.log(formdata)
            // reset the way the drop zone looks
            _("drop_zone").style = "border: #999 5px dashed; background-color: #EEE;"
        }
        function drag_drop(event) {
            event.preventDefault();
            ajaxUploadCall(event.dataTransfer.files[0]);
        }
        function dragover(event) {
            event.preventDefault();
            _("drop_zone").style = "border: #666 5px solid; background-color: #CCC;";
        }
        function dragleave(event) {
            event.preventDefault();
            _("drop_zone").style = "border: #999 5px dashed; background-color: #EEE;";
        }
        function progressHandler(event) {
            //_("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
           var percent = (event.loaded/event.total) * 100;
            _("progressBar").style= "width: " + Math.round(percent) + "%";
            _("progressBar").innerHTML = Math.round(percent) + "%"
            //_("status").innerHTML = Math.round(percent) + "% uploaded";
        }
        function completeHandler(event) {
            $("#lg_upload_spinner").addClass("d-none")
            $("#lg_upload_ok").removeClass("d-none")
            console.log(event.target.responseText)
            sliceSTLCall();
        }
        function sliceSTLCall() {
            var formdata = new FormData();
            formdata.append("stlFile", STLFileName)

            var ajaxSlice = new XMLHttpRequest();
            ajaxSlice.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    $("#lg_parse_spinner").addClass("d-none")
                    $("#lg_parse_ok").removeClass("d-none")

                    document.getElementById("sliceResult").innerHTML = "<pre>" + this.responseText + "</pre>"
                    var resultArray = this.responseText.split("\n")
                    console.log(resultArray)
                    var filament = 0
                    var printtime = 0
                    // loop through array and pick fields for calculation
                    resultArray.forEach(function(entry) {
                        if (entry.includes('Filament:')) {
                            filament = parseInt(entry.substring( entry.lastIndexOf(':') +1 ), 10)
                            eFil = filament
                            _("filament").innerHTML = parseFloat(Math.round(filament/10) / 100).toFixed(2) + " m"
                        }
                        if (entry.includes('Print time:')) {
                            printtime = parseInt(entry.substring( entry.lastIndexOf(':') +1 ), 10)
                            eTime = printtime
                            var printminutes = Math.floor(printtime/60)
                            var printhours = Math.floor(printminutes / 60) // only need the hours
                            var printminutes = printminutes - (60*printhours) //only need the leftover minutes
                            var minuteString = "00" + printminutes
                            _("printtime").innerHTML = printhours + " Std. " + minuteString.substring(minuteString.length -2) + " Min."
                        }
                    });
                    let costPerHour = 8
                    let meterToGram = 0.00300646864821054
                    let costPerKg = 50
                    let costSetup = 3
                    var costEstimate = ((printtime/3600)*costPerHour) + ((filament*meterToGram*costPerKg/1000)) + costSetup
                    eCost = costEstimate
                    _("estimate").innerHTML = "CHF " + parseFloat(Math.round(costEstimate * 100) / 100).toFixed(2)
                    _("addToCartBtn").disabled = false;
                    _("dimensions").innerHTML = Math.floor(stl_viewer.get_model_info(1).dims.x) + " mm (X) " + Math.floor(stl_viewer.get_model_info(1).dims.y) + " mm (Y) " + Math.floor(stl_viewer.get_model_info(1).dims.z) + " mm (Z) "
                    _("volume").innerHTML = Math.floor(stl_viewer.get_model_info(1).volume) + " mm3"

                }
            };
            ajaxSlice.open("POST", "/slice/");
            $("#lg_parse_spinner").removeClass("d-none")
            ajaxSlice.send(formdata);
        }
        function errorHandler(event) {
            _("status").innerHTML = "Upload Failed";
        }
        function abortHandler(event) {
            _("status").innerHTML = "Upload Aborted";
        }

        function errorHandler() {}
        function abortHandler() {}
    </script>
    <!-- Begin page content -->
    <main role="main" class="flex-shrink-0">
        <div class="container">
            <div class="row">
                <div class="col-1">
                  <span class="badge badge-primary">1</span><br/>
                  3D Modell wählen
                </div>
                <div class="col-1">
                  <span class="badge badge-primary">2</span><br/>
                  Modell in Warenkorb
                </div>
                <div class="col-1">
                  <span class="badge badge-primary">3</span><br/>
                  Offertanfrage
                </div>
                <div class="col-1">
                  <span class="badge badge-primary">4</span><br/>
                  Verbindliche Offerte
                </div>
                <div class="col-1">
                  <span class="badge badge-primary">5</span><br/>
                  Bestellen
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h1 class="mt-5">3D Modell für Druck hochladen</h1>
                    <p class="lead">Laden Sie Ihr 3D Modell als .STL hier hoch für eine unverbindliche Preisschätzung</p>
                </div>
            </div>
            <div class="row d-none" id="addFileRow">
                <div class="col-12">
                    <button class="btn-primary" onclick="showNewFile()">Anderes 3D Modell hochladen</button>
                </div>
            </div>
            <div class="row" id="droprow">
                <div class="col-12" style="margin-bottom:20px;">
                    <div id="drop_zone" ondrop="drag_drop(event)" ondragover="dragover(event)" ondragleave="dragleave(event)">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file1" id="file1" onchange="uploadFile()">
                                <label class="custom-file-label" id="fileNameLabel" for="file1">Datei wählen</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="progress" style="height:30px;">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Viewer</h5>
                            <div id="stl_cont" style="width:100%;height:390px;margin:0 auto;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card" >
                        <div class="card-body">
                            <h5 class="card-title">Details</h5>
                            <ul class="list-group">
                                <li class="list-group-item" id="lg_select">
                                    <i class="far fa-check d-none green" id="lg_select_ok"></i>
                                    3D Modell ausgewählt
                                </li>
                                <li class="list-group-item" id="lg_upload">

                                    <div class="spinner-grow d-none text-warning" role="status" id="lg_upload_spinner">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <i class="far fa-check d-none green" id="lg_upload_ok"></i>
                                    3D Modell upload
                                </li>
                                <li class="list-group-item" id="lg_parse">
                                    <div class="spinner-grow d-none text-warning" role="status" id="lg_parse_spinner">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <i class="far fa-check d-none green" id="lg_parse_ok"></i>
                                    3D Modell parsing
                                </li>
                                <li class="list-group-item" id="lg_filament">Filament: <span id="filament"></span></li>
                                <li class="list-group-item" id="lg_printtime">Druckzeit: <span id="printtime"></span></li>
                                <li class="list-group-item" id="lg_dimensions">3D-Modell - Dimensionen: <span id="dimensions"></span></li>
                                <li class="list-group-item" id="lg_volume">Druckvolumen: <span id="volume"></span></li>
                                <li class="list-group-item" id="lg_estimate">
                                    Geschätzte Druckkosten <span id="estimate"></span>
                                </li>
                                <li class="list-group-item" id="lg_addToCart">
                                    <button class="btn btn-primary btn-block" id="addToCartBtn" disabled onClick="addItemToCart();">
                                        in Warenkorb hinzufügen
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails"><i class="far fa-brackets-curly"></i> Show Details</button>
                    <div class="collapse" id="collapseDetails">
                        <div class="card card-body">
                            <p id="sliceResult"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php // === FOOTER ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/footer.php');
    ?>
    <?php // === LOGIN MODAL ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/loginModal.php');
    ?>
</body>
</html>
