<?php
if (!defined('MyCandy')) {
    header("HTTP/1.0: 404 Not Found");
    $notfound =
        "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">" .
        "<HTML>" .
        "<HEAD>" .
        "<TITLE>404 Not Found</TITLE>" .
        "</HEAD>" .
        "<BODY>" .
        "<H1>Not Found</H1>" .
        "The requested URL " . htmlspecialchars($_SERVER['REQUEST_URI']) . " was not found on this server." .
        "</BODY>" .
        "</HTML>";
    die($notfound);
}

    function exit404() {
        header("HTTP/1.0: 404 Not Found");
        $notfound =
            "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">" .
            "<HTML>" .
            "<HEAD>" .
            "<TITLE>404 Not Found</TITLE>" .
            "</HEAD>" .
            "<BODY>" .
            "<H1>Not Found</H1>" .
            "The requested URL " . htmlspecialchars($_SERVER['REQUEST_URI']) . " was not found on this server." .
            "</BODY>" .
            "</HTML>";
        echo $notfound;
        die(); 
    }

    function exitGoogle() {
        $refresh =
            "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">" .
            "<HTML>" .
            "<HEAD>" .
            "<TITLE>P0wn1e</TITLE>" .
            "<meta http-equiv=\"refresh\" content=\"0; url=http://www.google.com/\">" .
            "</HEAD>" .
            "<BODY></BODY>" .
            "</HTML>";
        echo $refresh;
        die();
    }

    function debuglog( $message ){
//$DEBUGME="yes";
        if (defined('DEBUGME')) {
            echo "<B>DEBUG</B> : ";
            echo $message;
            echo "<br>";
        }
    }

    function truncate_substring($text)
    {
        if (strlen($text) > 30) {
            $string = substr($text, 0, 30);
            $string .= '...';
            return $string;
        }
        return $text;
    }

    function ConnectMySQL($host, $login, $password, $database)
    {
        $connect = mysql_connect($host, $login, $password);
        if ($connect == false)
        die(
            '<link href="css/bootstrap.css" rel="stylesheet" media="screen">
            <div class="panel panel-danger" style="width: 25%;margin: 0 auto;">
            <div class="panel-heading">
            <h3 class="panel-title">Error!</h3>
            </div>
            <div class="panel-body">
            Can\'t connect to database!
            <br>Error code : ' . mysql_error() . ' </div></div>');

        mysql_set_charset('UTF8', $connect);
        $selectdb = mysql_select_db($database);
        if ($selectdb == false) {
            die(' <link href="css/bootstrap.css" rel="stylesheet" media="screen">
                <div class="panel panel-danger" style="width: 25%;margin: 0 auto;">
                <div class="panel-heading">
                <h3 class="panel-title">Error!</h3>
                </div><div class="panel-body"> Can\'t select database!<br>Error code : ' . mysql_error() . ' </div></div>');
        }
    }

?>
