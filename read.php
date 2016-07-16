<?php

ob_start();

if(!isset($_GET['filename'])){
    header("Location: index.php?error=1");
}

$filename = 'conversations/' . $_GET['filename'];

header('Content-type: text/html; charset=UTF-8');

    require 'inc/header.inc.php';
    require 'inc/navbar.inc.php';

?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
<?php

if(!fopen($filename, "r")){
    header('Location: index.php');
} else {
    $handle = fopen($filename, "r");
    #echo $handle . 'got file <br>';
}
$names_array = array();

#echo "names_array created <br>";


if($handle){
    $index = 0;
    while (($line = fgets($handle)) !== false){

        
        
        $line = explode(': ', $line);
        #echo "<b><i> count after exploding[" . count($line) . "] </b></i> <br>";

        //check for android/windows message or heading
        /*

        Windows phone: 
        6/5/2016 5:08:37 PM: Viditi Bhargava: <contact omitted>

        Android:
        3/4/16, 3:35 AM - Prince Kansal: Mrng me puch le

        Heading
        garry has added you to the group
        
        */
        
        $heading = 0;

        if (count($line) == 1)
        {
            // the msg is heading 
            // TODO: create proper css for headings
            $msg1 ="Messages you send to this chat and calls are now secured with end-to-end encryption. Tap for more info.";
            $msg2 = "added";
            $msg3 = "left";
            $msg4 = "removed";
            $msg5 = 'changed';
            $msg6 = "created this group";
            #echo "here";
            #echo strpos(trim($line[0]), $msg1);
            if (strpos(trim($line[0]), $msg1) >=0 || strpos($line[0], $msg2)>=0 || strpos($line[0], $msg3) >=0||strpos($line[0], $msg5)>=0 || strpos($line[0], $msg4) >=0 || strpos($line[0], $msg6) >=0 )
            {
                #echo "its a heading<br>";
                #echo  $line[0];
                $line = explode(" - ", $line[0]);
                #echo count($line);
                if (count($line) == 1)
                {
                    echo '<div class="msg_group_head">' . htmlspecialchars(trim($line[0])) . '</div><br>';
                    

                }
                else
                {
                    echo '<div class="msg_group_head">' . htmlspecialchars(trim($line[1])) . '</div><br>';
                    
                }

                $heading = 1;
                
            }


        }
        elseif ( count($line) == 2) {
            
            // Android message
            // TODO: write proper code
            /*
                line[0] = 3/4/16, 3:35 AM - Prince Kansal
                line[1] = Mrng me puch le
            */

            //We have to make it compatible with windows phone format

            $timestamp_array = explode("-", $line[0]);
            $timestamp =  returntimestamp(trim($timestamp_array[0]));
            $line[2] = $line[1];
            $line[1] = $timestamp_array[1];

            #echo "nnow we have android timestamp <br>";
            #echo "timstamp[Android] <b><i>[" . $timestamp . "]</b></i></br>";


        }
        elseif ( count($line) == 3) {
            // Windows msg
            $timestamp = $line[0];
            #echo "timestamp[Windows] [". $timestamp. "] <br>";
            $timestamp = returntimestamp($timestamp);
             
        
        }
    // main processing starts here
    if (empty($heading))
     {
        if($timestamp == false){
             $line = implode('-', $line);
            #echo "time stamp is false for line \"" . $line . "\"<br>";
            #echo "time stamp: [not provided] <br>";
        } else {
            #echo "time stamp: [provided] <br>";
            unset($line[0]);
            $line = implode('-', $line);
            #echo "[" . $line . "]<br>";

            

            $line = explode('-', trim($line));
            /*
            echo "[";
            foreach ($line as $value) {
                echo $value . " ";
                        } 
            echo "]";           
            */

            $name = trim($line[0]);
            
            unset($line[0]);
            $line = implode(':', $line);
            #echo "line is :" . $line;

            if(in_array($name, $names_array) == false){
                array_push($names_array, $name);
            }
            $index = array_search($name, $names_array);
            

            //remove follwoing foreach loop in the end
            //used only for debugging purposes
            /*

            foreach ($names_array as $name) {
                echo $name;
            }

            */
        }


        if($index%2 != 0){
            //echo $index;
            echo '<div class="aloo person' . $index . ' left-margin-20">';
        } else {
            echo '<div class="aloo person' . $index . '">';
        }


        echo '<div class="text">' . htmlspecialchars($line) . '</div>';
        echo '<div class="time">' . $name . "  " . $timestamp . '</div>';
        echo "</div>\n";
        // die;
     }
    }
    fclose($handle);
}

?>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <div class="list">
<?php

    $count = 0;


    foreach($names_array as $i => $name){
        if($name != '')
            //echo $name;
            //echo $i%2;
            echo '<span class="person' . $i%2 . '"><img src="img/default-user-image.png">' . $name . '</span>';
        $count++;
        #echo $count;
    }
    unlink($filename);

?>
                </div>
            </div>
        </div>
    </div>

<?php
    require 'inc/footer.inc.php';


// Returns the curated time stamp (common for all the devices)
// Currently it works only for windows mobile
// TODO: Make it common for android, ios and windows

function returntimestamp($time){

    #echo "<br>in function <b>returntimestamp</b> <br>";
    #echo $time. "<br>";

    $times_array = array();

    $times_array = explode(" ", $time);
    
    $regex = "(?<date_month>(\d)*)"
            . "(\/)+(?<date_day>(\d)*)"
            . "(\/)+(?<date_year>(\d)*)"
            . "( )(?<time_hour>(\d)*)"
            . "(:)+(?<time_minute>(\d)*)"
            . "(:)+(?<time_second>(\d)*)"
            . "( )(?<time_type>AM|PM)?";

    
    $matches = array();

    //For WINDOWS platform backup
    
    if(preg_match("/^" . $regex . "$/i", trim($time), $matches) > 0) {
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
            $time_second = $matches['time_second'];
            $date_year = intval($matches['date_year']);
            $date_day = $matches['date_day'];
            $date_month = $matches['date_month'];
        #echo "in if [windows]";
    }
    //For ANDROID platform backup
    else
    {
        #echo "time stamp to platfrom [Android]";
         $pattern = "(?<time_hour>(\d)*)"
            . "(:)+(?<time_minute>(\d)*)"
            . "(?<time_type>AM|PM)?"
            . "(,)+( )*(?<date_day>(\d)*)"
            . "( )*(?<date_month>(\w)*)";

        if(preg_match("/^" . $pattern . "$/i", trim($time), $matches) > 0) {

            #echo "<br>matched in [Android][if]";
            $time_hour = floatval($matches['time_hour']);
            $time_minute = $matches['time_minute'];
            $time_type = $matches['time_type'];
            $date_day = $matches['date_day'];
            $date_month = $matches['date_month'];
        } 
        else {
            #echo "<br>matched in [Android][else]";
             $pattern = "(?<date_year>(\d)*)"
            . "(\/)+(?<date_month>(\d)*)"
            . "(\/)+(?<date_day>(\d)*)"
            . "(,)+( )*(?<time_hour>(\d)*)"
            . "(:)*(?<time_minute>(\d)*)"
            . "( )*(?<time_type>AM|PM)*";
            if(preg_match("/^" . $pattern . "$/i", trim($time), $matches) > 0) {
                $time_hour = intval($matches['time_hour']);

                if(!isset($matches['time_type'])){
                    if($time_hour >= 12)
                        $time_type = "PM";
                    else
                        $time_type = "AM";
                } 
                else {
                    
                    $time_type = $matches['time_type'];
                }

                if($time_hour > 12)
                    $time_hour = $time_hour - 12;

                $time_minute = $matches['time_minute'];
                $date_year = intval($matches['date_year']);
                $date_day = $matches['date_day'];
                $date_month = $matches['date_month'];
                
            }
            else {
                    return false;
            }
        }
    

    }

    
    

    if(isset($date_year))
        $timestamp = $date_year;
    else
        $timestamp = date('Y', time());

    if (empty($time_second))
    {
         $timestamp .= '-' . $date_month . '-' . $date_day . " " . $time_hour . ':' . $time_minute . ":" .' ' . $time_type;

    }
    else
    {
         $timestamp .= '-' . $date_month . '-' . $date_day . " " . $time_hour . ':' . $time_minute . ":" . $time_second . ' ' . $time_type;

    }
   
    #echo "<p> I am here </p>";
    return date('(d-M-y) h:i:s A' , strtotime($timestamp));
}

?>
