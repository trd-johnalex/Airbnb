<?php

require_once('dbconnect.php');

header('Content-Type: text/html; charset=utf-8');

class cURL{

    public $response;
    protected $sendHeader;
    protected $PostFields;
    private $query;

    public function __construct($query = '')
    {
        $this->sendHeader = false;
        $this->query = $query;
        if(!empty($this->query)){
            if(!is_array($this->query))
                $this->response =   $this->Connect($this->query);
            else
                $this->encode();
        }
    }

    public function SendPost($array = array())
    {
        $this->PostFields['payload'] = $array;
        $this->PostFields['query'] = http_build_query($array);
        return $this;
    }

    public  function Connect($_url,$deJSON = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if(strpos($_url,"https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        }

        if(!empty($this->PostFields['payload'])) {
            curl_setopt($ch, CURLOPT_POST, count($this->PostFields['payload']));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->PostFields['query']);
        }

        if(!empty($this->sendHeader))

            curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/
                601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56');

        $decode = curl_exec($ch);
        $_response = ($deJSON)? json_decode($decode, true) : $decode;
        $error = curl_error($ch);

        curl_close($ch);
        return (empty($error))? $_response: $error;
    }

    public function emulateBrowser()
    {
        $this->sendHeader = true;
        return $this;
    }

    public function encode($_filter = 0)
    {
        foreach($this->query as $key => $value) {
            $string[]   =   urlencode($key).'='.urlencode($value);
        }

        if($_filter == true)
            $string =   array_filter($string);

        return implode("&",$string);
    }
}

class dbFunctions
{
    function dbRowInsert($table_name, $form_data, $conn)
    {
        $fields = array_keys($form_data);

        $sql = "INSERT INTO ".$table_name."
    (`".implode('`,`', $fields)."`)
    VALUES('".implode("','", $form_data)."')";

        return mysqli_query($conn, $sql);
    }

    function dbRowDelete($table_name, $where_clause='')
    {
        $whereSQL = '';
        if(!empty($where_clause))
        {
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
            {
                $whereSQL = " WHERE ".$where_clause;
            } else
            {
                $whereSQL = " ".trim($where_clause);
            }
        }
        $sql = "DELETE FROM ".$table_name.$whereSQL;

        return mysql_query($sql);
    }

    function dbRowUpdate($table_name, $form_data, $where_clause='')
    {
        $whereSQL = '';
        if(!empty($where_clause))
        {
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
            {
                $whereSQL = " WHERE ".$where_clause;
            } else
            {
                $whereSQL = " ".trim($where_clause);
            }
        }
        $sql = "UPDATE ".$table_name." SET ";

        $sets = array();
        foreach($form_data as $column => $value)
        {
            $sets[] = "`".$column."` = '".$value."'";
        }
        $sql .= implode(', ', $sets);

        $sql .= $whereSQL;

        return mysql_query($sql);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Airbnb - crawler</title>
    <meta charset='utf-8'>
</head>
<body>

<?php

//$path   =   'https://www.airbnb.com/s/Fukuoka-Prefecture--Japan?guests=&checkin=11%2F02%2F2015&checkout=11%2F07%2F2015&ss_id=3qfx8hqk&source=bb';
$path = $_GET["url"];
if(empty($path)){
    echo "No data found or Missing url!";
}
$cURL   =   new cURL();
$dbFunc = new dbFunctions();
$html   =   $cURL->emulateBrowser()->connect($path,false);
$dom    =   new DOMDocument;
@$dom->loadHTML($html);
$xpath = new DOMXpath($dom);
$articles = $xpath->query('//div[@class="col-sm-12 row-space-2 col-md-6"]');
$data = array();

foreach($articles as $container) {

    foreach($container->getElementsByTagName('div') as $div) {
        if($div->parentNode->getAttribute('class')
            == "col-sm-12 row-space-2 col-md-6"){
            $data["data-lat"] = $div->getAttribute('data-lat');
            $data["data-lng"] = $div->getAttribute('data-lng');
            $data["data-url"] = $div->getAttribute('data-url');
            $data["data-user"] = $div->getAttribute('data-user');
            $data["data-id"] = $div->getAttribute('data-id');//same value in hosting id
            $data["data-instant-book"] = $div->getAttribute('data-instant-book');
        }
    }
    foreach($container->getElementsByTagName('span') as $span) {
        if($span->parentNode->getAttribute('class')
            == "panel-overlay-top-right wl-social-connection-panel") {
            $data["data-img"]= $span->getAttribute('data-img');
            $data["data-name"] = $span->getAttribute('data-name');
            $data["data-address"]= $span->getAttribute('data-address');
            $data["data-hosting_id"] = $span->getAttribute('data-hosting_id');
            $data["data-price"] = $span->getAttribute('data-price');
            $data["data-review_count"] = $span->getAttribute('data-review_count');
            $data["data-host_img"] = $span->getAttribute('data-host_img');
            $data["data-summary"] = $span->getAttribute('data-summary');
            $data["data-description"] = $span->getAttribute('data-description');
            $data["data-star_rating"] = $span->getAttribute('data-star_rating');
        }
    }

    echo "User ID :" .$data['data-user']. "<br>";
    echo "Hosting ID :" .$data['data-hosting_id']. "<br>";
    echo "Description : " .$data['data-name']. "<br>";
    echo "Location : " .$data['data-address']. "<br>";
    echo "Price : " .$data['data-price']. "<br>";
    echo "Reviews : " .$data['data-review_count']. "<br>";
    echo "Rating : " .$data['data-star_rating']. "<br><br>";

    $form_data = array(
        'id' => $data['data-id'],
        'user' => $data['data-user'],
        'name' => $data['data-name'],
        'address' => $data['data-address'],
        'price' => $data['data-price'],
        'reviewcount' => $data['data-review_count'],
        'rating' => $data['data-star_rating'],
    );

    $dbFunc->dbRowInsert('t_sample', $form_data, $conn);
}
?>

</body>
</html>