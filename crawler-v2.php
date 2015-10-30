<?php

require_once('ganon.php');
require_once('dbconnect.php');

$html = file_get_dom('https://www.airbnb.com/s/Fukuoka-Prefecture--Japan?checkin=10%2F30%2F2015&checkout=11%2F01%2F2015&guests=&ss_id=g1qzuyaq&page=1');

$doc = new DOMDocument();
@$doc->loadHTML($html);
$xpath = new DOMXpath($doc);
$articles = $xpath->query('//div[@class="col-sm-12 row-space-2 col-md-6"]');
$data = array();

foreach($articles as $container) {
    foreach($container->getElementsByTagName('div') as $div) {
        if($div->parentNode->getAttribute('class') == "col-sm-12 row-space-2 col-md-6")
        {
            $data["data-lat"] = $div->getAttribute('data-lat');
            $data["data-lng"] = $div->getAttribute('data-lng');
            $data["data-url"] = $div->getAttribute('data-url');
            $data["data-user"] = $div->getAttribute('data-user');
            $data["data-id"] = $div->getAttribute('data-id');
            $data["data-instant-book"] = $div->getAttribute('data-instant-book');
        }
    }

    foreach($container->getElementsByTagName('span') as $span) {
        if($span->parentNode->getAttribute('class') == "panel-overlay-top-right wl-social-connection-panel") {
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


    $ins = 'INSERT INTO t_sample(id, user, name, address, price, reviewcount, rating)
     VALUES("'.$data["data-id"].'", "'.$data["data-user"].'", "'
        .$data["data-name"].'", "'.$data["data-address"].'", "'
        .$data["data-price"].'", "'.$data["data-review_count"].'", "'
        .$data["data-star_rating"].'")';

    if(mysqli_query($conn, $ins)){
        echo "New record created successfully <br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    //print_r($data);
}

//$conn->close();