<!DOCTYPE html>
<html>
<head>
    <title>Airbnb Crawler</title>
    <link href="https://a1.muscache.com/airbnb/static/packages/common_o2.1-6eddc18600cfbdbb295329a06f26d181.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
function processForm() {
    $zipCode = $_GET['url'];
    $url = "crawler.php?url=" . $zipCode;
    header("Location: $url");
    exit;
}
?>

<div id="searchbar" style="text=align:center; width:500px; margin:0 auto 0; padding:50px 0">
    <div style="margin:0 0 50px 0"><img src="https://upload.wikimedia.org/wikipedia/commons/6/69/Airbnb_Logo_B%C3%A9lo.svg" alt="Airbnb logo" height="auto" width="500px"></div>
    <div class="searchbar">
        <form id="searchbar-form" method="get" action="crawler.php">
            <div class="searchbar__input-wrapper">
                <label class="searchbar__location">
          <span class="screen-reader-only">
            Paste URL here
          </span>
                    <input
                        id="location"
                        type="text"
                        class="input-large input-contrast"
                        name="url"
                        onblur="if(this.value=='') this.value='Paste URL here';"
                        onfocus="if(this.value=='Paste URL here') this.value='';"
                        placeholder="Paste URL here" />
                    <div id="searchbar-location-error" class="searchbar__location-error hide">
                        Please input a url
                    </div>
                </label>
            </div>
            <button
                id="submit_location"
                type="submit"
                class="searchbar__submit btn btn-primary btn-large">
                Crawl
            </button>
        </form>
    </div>
</body>
</html>