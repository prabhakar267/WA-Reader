<?php

if(!isset($_GET['filename'])){
    header("Location: index.php");
}

$filename = 'conversations/' . $_GET['filename'];

header('Content-type: text/html; charset=UTF-8');
include('../php-emoji/emoji.php');

function returntimestamp($time){
    $pattern = "(?<time_hour>(\d)*)"
            . "(:)+(?<time_minute>(\d)*)"
            . "(?<time_type>AM|PM)?"
            . "(,)+( )*(?<date_day>(\d)*)"
            . "( )*(?<date_month>(\w)*)";
    $matches = array();
    
    if(preg_match("/^" . $pattern . "$/i", trim($time), $matches) > 0) {            
        $time_hour = floatval($matches['time_hour']);
        $time_minute = $matches['time_minute'];
        $time_type = $matches['time_type'];
        $date_day = $matches['date_day'];
        $date_month = $matches['date_month'];
    } else {
        $pattern = "(?<date_year>(\d)*)"
            . "(\/)+(?<date_month>(\d)*)"
            . "(\/)+(?<date_day>(\d)*)"
            . "(,)+( )*(?<time_hour>(\d)*)"
            . "(:)*(?<time_minute>(\d)*)"
            . "( )*(?<time_type>AM|PM)*";
        $matches = array();
        if(preg_match("/^" . $pattern . "$/i", trim($time), $matches) > 0) {            
            $time_hour = intval($matches['time_hour']);

            if(!isset($matches['time_type'])){
                if($time_hour >= 12)
                    $time_type = "PM";
                else
                    $time_type = "AM";
            } else {
                $time_type = $matches['time_type'];
            }

            if($time_hour > 12)
                $time_hour = $time_hour - 12;

            $time_minute = $matches['time_minute'];
            $date_year = intval($matches['date_year']);
            $date_day = $matches['date_day'];
            $date_month = $matches['date_month'];
        } else {
            return false;
        }
    }

    if(isset($date_year))
        $timestamp = $date_year;
    else 
        $timestamp = date('Y', time());

    $timestamp .= '-' . $date_month . '-' . $date_day . " " . $time_hour . ':' . $time_minute . ' ' . $time_type;
    return date('(d-M-y) h:i A' , strtotime($timestamp));
}

?>

<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>WhatsApp Reader | By Prabhakar Gupta</title>
</head>
<body>

<nav class="navbar navbar-fixed-top navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Whatsapp Reader</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
<?php

// $filename = 'test1.txt';
if(fopen($filename, "r") == false){
    header('Location: index.php');
} else {
    $handle = fopen($filename, "r");
}
$names_array = array();

if($handle){
    $index = 0;
    while (($line = fgets($handle)) !== false) {
        $line = explode('-', $line);
        $timestamp = $line[0];

        $timestamp = returntimestamp($timestamp);

        if($timestamp == false){
            $line = implode('-', $line);
        } else {
            unset($line[0]);
            $line = implode('-', $line);

            $line = explode(':', trim($line));
            $name = trim($line[0]);
            unset($line[0]);
            $line = implode(':', $line);
        
            if(in_array($name, $names_array) == false){
                array_push($names_array, $name);
            }
            $index = array_search($name, $names_array);
        }

        if($index%2 != 0){
            echo '<div class="aloo person' . $index . ' left-margin-20">';
        } else {
            echo '<div class="aloo person' . $index . '">';
        }
        echo '<div class="text">' . emoji_unified_to_html(htmlspecialchars($line)) . '</div>';
        echo '<div class="time">' . $timestamp . '</div>';
        echo "</div>\n";
        // die;
    }
    fclose($handle);
} else {
    // error opening the file.
}
?>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <div class="list">
<?php
    $count = 0;
    foreach($names_array as $name){
        if($name != '')
            echo '<span class="person' . $count . '"><img src="img/default-user-image.png">' . $name . '</span>';
        $count++;
    }
    // unlink($filename);
?>
                </div>
            </div>
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</body>
</html>