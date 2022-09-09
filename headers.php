<?php

function get_HTTP_request_headers() {
    $HTTP_headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $single_header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $HTTP_headers[$single_header] = $value;
    }
    return $HTTP_headers;
}
$headers = get_HTTP_request_headers();
foreach ($headers as $key => $value) {
    echo "$key => $value <br/>";
}

echo "<br/>---------------------------------<br/>";

$apache_headers= apache_request_headers();

foreach ($apache_headers as $key => $value) {
    echo "$key => $value <br/>";
}
?>
