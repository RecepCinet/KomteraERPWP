<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

//include '../../_conn.php';
//
//$sql = "Select * from aaa_erp_kt_bayiler";
//$stmt = $conn->query($sql);
//$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
//$response = "{\"data\":" . json_encode($data) . "}";
//if (isset($_GET['callback'])) {
//    echo $_GET['callback'] . '(' . $response . ')';
//} else {
//    echo $response;
//}
//
//
//


class SortHelper {
    public static function deSerializeSort($pq_sort) {
        $sorters = json_decode($pq_sort);
        $columns = array();
        $sortby = "";
        foreach ($sorters as $sorter) {
            $dataIndx = $sorter->dataIndx;
            $dir = $sorter->dir;
            if ($dir == "up") {
                $dir = "asc";
            } else {
                $dir = "desc";
            }
            if (ColumnHelper::isValidColumn($dataIndx)) {
                $columns[] = $dataIndx . " " . $dir;
            } else {
                throw new Exception("invalid column " . $dataIndx);
            }
        }
        if (sizeof($columns) > 0) {
            $sortby = " order by " . join(", ", $columns);
        }
        return $sortby;
    }
}

$sortQuery = "";
if (isset($_GET["pq_sort"])) {
    $pq_sort = $_GET["pq_sort"];
    $sortQuery = SortHelper::deSerializeSort($pq_sort);
}

class ColumnHelper
{
    public static function isValidColumn($dataIndx)
    {
        //update it to match your requirements for field names in db.
        return preg_match('/^[a-z,A-Z,_]*$/', $dataIndx);
    }
}

class FilterHelper
{
    public static function _contain($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " like CONCAT('%', ?, '%')";
        $param[] = $value;
    }
    public static function _notcontain($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " not like CONCAT('%', ?, '%')";
        $param[] = $value;
    }
    public static function _begin($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " like CONCAT( ?, '%')";
        $param[] = $value;
    }
    public static function _notbegin($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " not like CONCAT( ?, '%')";
        $param[] = $value;
    }
    public static function _end($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " like CONCAT('%', ?)";
        $param[] = $value;
    }
    public static function _notend($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " not like CONCAT('%', ?)";
        $param[] = $value;
    }
    public static function _equal($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " = ?";
        $param[] = $value;
    }
    public static function _notequal($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " != ?";
        $param[] = $value;
    }
    public static function _empty($dataIndx, &$fcrule){
        $fcrule[] = "ifnull(" . $dataIndx . ",'')=''";
    }
    public static function _notempty($dataIndx, &$fcrule){
        $fcrule[] = "ifnull(" . $dataIndx . ",'')!=''";
    }
    public static function _less($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " < ?";
        $param[] = $value;
    }
    public static function _lte($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " <= ?";
        $param[] = $value;
    }
    public static function _great($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " > ?";
        $param[] = $value;
    }
    public static function _gte($dataIndx, &$fcrule, &$param, $value){
        $fcrule[] = $dataIndx . " >= ?";
        $param[] = $value;
    }
    public static function _between($dataIndx, &$fcrule, &$param, $value, $value2){
        $fcrule[] = "(" . $dataIndx . " >= ? and ".$dataIndx." <= ? )";
        $param[] = $value;
        $param[] = $value2;
    }
    public static function _range($dataIndx, &$fcrule, &$param, $value){
        $arrValue = $value;
        $fcRange = array();
        foreach ($value as $val){
            if ($val == ""){
                //continue;
                FilterHelper::_empty($dataIndx, $fcRange);
            }
            else{
                $fcRange[] = $dataIndx."= ?";
                $param[] = $val;
            }
        }
        $fcrule[] = (sizeof($fcRange)>0)? "(". join(" OR ", $fcRange) .")": "";
    }

    public static function deSerializeFilter($pq_filter)
    {
        //$filterObj = json_decode($pq_filter);//when stringify is true;        
        $filterObj = json_decode($pq_filter, FALSE);
        $mode = $filterObj->mode;
        $rules = $filterObj->data;
        $frule = array();
        $param = array();

        foreach ($rules as $rule){
            $dataIndx = $rule->dataIndx;
            if (ColumnHelper::isValidColumn($dataIndx) == false){
                throw new Exception("Invalid column name");
            }
            $dataType = $rule->dataType;

            if(property_exists($rule, "crules")){
                $crules = $rule->crules;
            }
            else{
                $crules = array();
                $crules[] = $rule;
            }
            $fcrule = array();
            foreach($crules as $crule){
                $value = $crule->value;
                $value2 = property_exists($crule, "value2")? $crule->value2: "";
                if($dataType == "bool"){
                    $value = ($value == "true")? 1: 0;
                    $value2 = ($value2 == "true")? 1: 0;
                }
                $condition = $crule->condition;
                FilterHelper::{"_".$condition}($dataIndx, $fcrule, $param, $value, $value2);
            }//end of crules loop.
            $frule[] = (sizeof($fcrule) > 1)? "(" . join(" ".$rule->mode." ", $fcrule) . ")": $fcrule[0];
        }//end of rules loop.
        $query = (sizeof($frule) > 0)? " where " . join(" ".$mode." ", $frule): "";
        $ds = new stdClass();
        $ds->query = $query;
        $ds->param = $param;
        return $ds;
    }
}//end of class





$filterQuery = "";
$filterParam = array();
if ( isset($_GET["pq_filter"]))
{
    $pq_filter = $_GET["pq_filter"];
    $dsf = FilterHelper::deSerializeFilter($pq_filter);
    $filterQuery = $dsf->query;
    $filterParam = $dsf->param;
}

if (isset($_GET["pq_curpage"]) && isset($_GET["pq_rpp"])) {
    include '../../_conn.php';
    $pq_curPage = $_GET["pq_curpage"];
    $pq_rPP = $_GET["pq_rpp"];
    
    $sql = "Select count(*) from aaa_erp_kt_bayiler $filterQuery";
    $stmt = $conn->prepare($sql);
    $stmt->execute($filterParam);
    $total_Records = $stmt->fetchColumn();
    
//    $sql = "Select count(*) from aa_erp_kt_fiyat_listesi";
//    $stmt = $conn->query($sql);
//    $total_Records = $stmt->fetchColumn();
    
    $skip = ($pq_rPP * ($pq_curPage - 1));
    if ($skip >= $total_Records) {
        $pq_curPage = ceil($total_Records / $pq_rPP);
        $skip = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($sortQuery=="") {
        $sortQuery="order by id";
    }
    $sql = "Select * from aaa_erp_kt_bayiler $filterQuery $sortQuery OFFSET $skip ROWS FETCH NEXT $pq_rPP ROWS ONLY";
    $stmt = $conn->prepare($sql);
    $stmt->execute($filterParam);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sb = "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($products) . "}";
    echo $sb;
    
}

