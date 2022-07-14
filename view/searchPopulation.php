<?php
    header('Content-type:application/json;charset=utf-8');

    require_once __DIR__ . '/../db/db_connection.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $prefecture = '';
        $year = '';

        if(isset($_POST['prefecture'])){
            $prefecture = '%' . $_POST['prefecture'] . '%';
        }
        if(isset($_POST['year'])){
            $year =$_POST['year'] ;
        }

        $where = "";
        $stmt ="";
        $pdo = new MyPDO();

        if($prefecture !="" && $year !=""){
            $where .= "`PREFECTURE` LIKE :prefecture";
            $stmt = $pdo->prepare("SELECT * FROM `prefecture_population` WHERE `PREFECTURE` LIKE :prefecture AND `YEAR` = :year");
            $stmt->bindParam(':prefecture', $prefecture, PDO::PARAM_STR);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        }

        if($prefecture !="" && $year ==""){
            $stmt = $pdo->prepare("SELECT * FROM `prefecture_population` WHERE `PREFECTURE` LIKE :prefecture");
            $stmt->bindParam(':prefecture', $prefecture, PDO::PARAM_STR);
        }

        if($prefecture =="" && $year !=""){
            $stmt = $pdo->prepare("SELECT * FROM `prefecture_population` WHERE `YEAR` = :year");
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        }

        $stmt->execute();
        $arr = $stmt->fetchAll();
        if(!$arr) exit('No rows');

        echo json_encode($arr);
        exit;
    }

?>
