<?php

if(!defined('MyCandy'))
{
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
?>
