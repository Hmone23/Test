<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/db_connection.php';
require_once __DIR__ . '/../controller/import_csv.php';

    //エラー文言
    $err_message = "";

    //文言
    $message ="";

    //エラーフラッグ
    $err_flg = false;

    //ファイルがある場合
    if (isset($_FILES["file"]["name"])) {

        $fileName = $_FILES["file"]["name"];
        $fileTempName = $_FILES["file"]["tmp_name"];
        $fileSize = $_FILES["file"]["size"];

        $import = new Import_CSV();
        $back_message = $import->getCSV($fileSize,$fileTempName,$err_flg);

        if($back_message !=''){
            $err_message = $back_message;
            $err_flg = true;
        }


        //データがある場合
        if(!$err_flg){

            $import = $import->importCSV();
            if($import){
                $message = "インポートは完了しました。";
            }else{
                $err_message  = "失敗しました。";
            }
        }
    }
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../lib/bootstrap.min.css" crossorigin="anonymous">
        <script src = "../lib/jquery.min.js" type = "text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../css/common.css">
    </head>

    <body>

        <div class="container">
        <?php if(!empty($err_message)){ ?>
             <div class="alert alert-danger" role="alert">
              <?= $err_message;?>
            </div>
       <?php }
        if(!empty($message)){ ?>
             <div class="alert alert-success" role="alert">
              <?= $message;?>
            </div>
       <?php } ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="text-on-pannel text-primary"><strong class="text-uppercase"> 県によって日本の人口CSVインポート </strong></h4>
                </div>
                <div class="panel-body">
                <div class="row">
                    <form class="form-horizontal" action="import.php" method="post" name="frmimport" id="frmimport" enctype="multipart/form-data">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="filebutton">ファイル選択</label>
                                <div class="col-md-4">
                                    <input type="file" name="file" id="file" class="input-large">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="filebutton"></label>
                                <div class="col-md-4">
                                    <button name="import_btn" id="import_btn" class="btn btn-primary button-loading" data-loading-text="Loading...">インポート</button>
                                </div>
                            </div>

                            <div class="form-group col-md-4" style="float:right;">
                                <a href="../index.html" class="btn btn btn-primary">ホーム画面</a>
                               
                                <a href="search.php" class="btn btn btn-primary">検索画面</a>
                            </div>

                        </fieldset>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script type= "text/javascript">

    $(document).ready(function() {
        $("#import_btn").on("click", function () {

            if (!/\.CSV$/i.test($("#file").val())){
                alert("CSVファイルのみアップロードしてください！");
                return false;
            }

        });

    });

</script>