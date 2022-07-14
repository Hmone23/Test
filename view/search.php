<?php
    require_once __DIR__ . '/../db/db_connection.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <script src = "../lib/jquery.min.js" type = "text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../css/common.css">
        <link rel="stylesheet" href="../lib/bootstrap.min.css" crossorigin="anonymous">
    </head>
    <body>
      <div>
        <div class="search-heading">
          <h3>人口検索一覧</h3>
        </div>
        <br>
        <div class="search-form">
          <div class="search-group">
            <label class="search-label">県</label>
            <select name="prefecture" id="prefecture" class="search-slt">
              <option value='' selected>選択</option>
              <?php
                $sql ="SELECT DISTINCT `PREFECTURE` FROM `prefecture_population` ";

                $pdo = new MyPDO();
                $pdo->prepare($sql);
                $res = $pdo->query($sql);

                while ($list = $res->fetch()) {
                    $prefecture = $list['PREFECTURE'];
                    echo "<option value='".$prefecture."'>".$prefecture."</option>";
                }
              ?>
            </select>
          </div>

          <div class="search-group">
            <label class="search-label">年</label>
            <select name="year" id="year" class="search-slt">
              <option value='' selected>選択</option>
              <?php
                $sql ="SELECT DISTINCT `YEAR` FROM `prefecture_population` ";

                $pdo = new MyPDO();
                $pdo->prepare($sql);
                $res = $pdo->query($sql);

                while ($list = $res->fetch()) {
                    $year = $list['YEAR'];
                    echo "<option value='".$year."'>".$year."</option>";
                }
              ?>
            </select>
          </div>

            <button name="search_btn" id="search_btn" class="search-btn btn btn btn-primary">検索</button>
            <a href="../index.html" class="search-btn btn btn btn-primary">ホーム画面</a>
            <a href="import.php" class="search-btn btn btn btn-primary">インポート画面</a>

        </div>
        <div id="txtHint" class="searched-data">
            <span id="forblank">データはありません。県または年度を選択して検索してください。</span>
            <table id="data_show" style='width:1140px !important;display: none;margin: 0 auto;'>
                <thead style='display:block;'>
                    <th style='width:150px;'>No</th>
                    <th style='width:350px;'>県</th>
                    <th style='width:200px;'>年度</th>
                    <th style='width:250px;'>総人口</th>
                    <th style='width:200px;'>人口</th></tr>
                </thead>
                <tbody style='display:block;max-height:360px;overflow-y:auto;'>

                </tbody>
            </table>
        </div>
      </div>
    </body>
</html>

<script type= "text/javascript">

  $("#search_btn").on("click", function () {

    var prefecture = $("#prefecture option:selected").val();
    var year = $("#year option:selected").val();

    if(!prefecture && !year){
       alert("県、または　年度　を必ず選択してください！");
        $("#forblank").show();
        $("#data_show").css("display", "none");
       return false;
    }

        $.ajax({
          type: "POST",
          url: "searchPopulation.php",
          data: {"prefecture":prefecture,"year":year},
          success: function(result) {
              $("#forblank").hide();
              $("#data_show").css("display", "block");
              $('#data_show tbody').html('');
              let no =1;
              $.each(result, function (i, data) {
                  let content ="";
                   content += "<tr><td style='width:150px;'>"+no+"</td>";
                   content += "<td style='width:350px;'>"+data.PREFECTURE+"</td>";
                   content += "<td style='width:200px;'>"+data.YEAR+"</td>";
                   content += "<td style='width:250px;'>"+data.TOTAL+"</td>";
                   content += "<td style='width:200px;'>"+data.POPULATION+"</td></tr>";

                  $('#data_show tbody').append(content);
                  no++;
              });
          }
        });
  });

</script>