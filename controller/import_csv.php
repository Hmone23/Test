<?php
use League\Csv\Reader;
use League\Csv\Statement;

class Import_CSV
{
    private $year = array();    //年度配列
    private $total = array();   //総人口配列
    private $_big_data = array();   //県と関係る人口配列
    private $list = array();  //データベースに入れる配列

    public function getCSV($fileSize,$fileTempName,$err_flg)
    {
        $err_message ='';

        if ($fileSize > 0) {

            $csv = Reader::createFromPath($fileTempName, 'r');
            $csv->addStreamFilter('convert.iconv.SJIS/UTF-8');
            $stmt = (new Statement())
                ->offset(2)
            ;
            $rows = $stmt->process($csv);

            foreach ($rows as $offset => $row) {
                print_r($row);

                if($offset == 2){  //CSVの3行は年度配列に入れる
                    $this->year= $row;
                }else if($offset == 3){  //CSVの4行は総人口配列に入れる
                    $this->total = $row;
                }else{
                    $this->_big_data[] = $row;  //県と関係る人口配列に入れる
                }

            }

            //年度がない場合はエラーメッセージ表示する
            if(count(array_filter($this->year)) == 0){
                $err_message = "CSVの３行に年度をいれてください！";
                $err_flg = true;
            }

            //総人口がない場合はエラーメッセージ表示する
            if(count(array_filter($this->total)) == 0){
                $err_message = "CSVの４行に総人口をいれてください！";
                $err_flg = true;
            }

            //県と人口がない場合はエラーメッセージ表示する
            if(count($this->_big_data) == 0){
                $err_message = "県と人口をいれてください！";
                $err_flg = true;
            }
        }else{
            $err_message = "CSVファイルは空になっています";
            $err_flg = true;
        }

        foreach($this->_big_data as $k2=>$val2){
            foreach($this->year as $k1=>$val1){
                if($k1 > 0 && !$err_flg){   //年度　ブランク　カラム　を　スキップ
                    $tmpArr = array();
                    $tmpArr["PREFECTURE"] = $val2[0];
                    $tmpArr["YEAR"] = $this->year[$k1];
                    $tmpArr["TOTAL"] = $this->total[$k1];
                    $tmpArr["PREFE_POP"] = $val2[$k1];
                    if( ( !is_numeric($tmpArr["YEAR"]) ) || ( !is_numeric($tmpArr["TOTAL"]) ) || ( !is_numeric($tmpArr["PREFE_POP"]) ) ){
                        $err_message = "データを正しく入力してください！";
                        $err_flg = true;
                    }
                    $this->list[] = $tmpArr;
                }
            }
        }
        return $err_message;

    }

    public function importCSV(){
        $pdo = new MyPDO();
        $sql = $pdo->prepare("INSERT INTO `prefecture_population`(`PREFECTURE`, `YEAR`, `TOTAL`, `POPULATION`) VALUES(:prefecture,:year,:total,:population)");
        foreach ($this->list as $key => $value) {
                $prefecture = substr($value["PREFECTURE"], 3);
                $year = $value["YEAR"];
                $total = $value["TOTAL"];
                $population = $value["PREFE_POP"];

                $sql->bindParam(':prefecture', $prefecture, PDO::PARAM_STR);
                $sql->bindParam(':year', $year, PDO::PARAM_INT);
                $sql->bindParam(':total', $total, PDO::PARAM_INT);
                $sql->bindParam(':population', $population, PDO::PARAM_INT);
                $sql->execute();
            }
        return $sql;
    }

}
?>