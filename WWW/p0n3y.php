<?php
define('MyCandy', 1);
//define('DEBUGME', 1);

include("private/config.php");
include("private/functions.php");

    // Premier check des paramètres
    if ( isset($_GET['ip'])) {
        // la nouvelle méthode est moins batarde
        $arch = $_GET['arch'];
        if ( $arch != "x86" AND $arch != "AMD64") {
            // C'est bizarre, c'est pas une architecture attendue
            exit404();
        }

        // Est-on bien appelé correctement ?
        $FileType = $_GET['ext'];
        if (( $FileType == "Forensic" ) OR ( $FileType != ".jpg" AND $FileType != ".exe" AND $FileType != ".png" AND $FileType != ".pdf")) {
            exit404();
        }

        $IP = $_GET['ip'];
        // Pour l'IPv6 on met entre " pour rien perdre
        if ( strpos($IP, ":") != false ) {
            // IPv6
            $IP = "\"" . $IP . "\"";
        }

        // Récupération des données
        $RemoteIP = $_SERVER['REMOTE_ADDR'];
        $User = $_GET['user'];
        $ComputerName = $_GET['computername'];
        $DomaineName = $_GET['domain'];
        $UIDKEY = $_GET['uid'];
        $FileName = $_GET['file'];
        $LaunchFrom = $_GET['LaunchFrom'];
        $LaunchName = $_GET['LaunchName'];
    } else {
        //list($arch, $IP, $User, $ComputerName, $DomaineName, $UIDKEY) = 
        //    explode("_", $_GET);
        // Sinon ce n'est pas une demande légitime
        exit404();
    }
    // Datation du click
    $DateDeClic = date ("Y-m-d H:i:s");

    // Geolocalisation du click
    $gi = geoip_open("private/GeoIP.dat", GEOIP_STANDARD);
    //$country = geoip_country_name_by_name($RemoteIP);
    $country = geoip_country_name_by_addr($gi, $RemoteIP);
    debuglog(">>>" .  $RemoteIP . " = " . $country);
    geoip_close($gi);

    // Journalse tout
    $fp = fopen ("/home/www/mycandy.efflam.net/htdocs/private/JournauxDeClics.txt", "a");
    $fwrite = fwrite($fp, $DateDeClic . ";" . $UIDKEY . ";" . $IP . ";" . $RemoteIP . ";" . 
        $User . ";" . $ComputerName . ";" . $arch . ";" . $DomaineName . ";" . $FileType . ";" . 
        $FileName . ";" . $LaunchFrom . ";" . $LaunchName . ";" . $country . "\n");
    fclose ($fp);

    // Recherche le ou les mail associé à la clef
    $fp = fopen ("/home/www/mycandy.efflam.net/htdocs/private/Clients.txt", "r");
    while (!feof($fp)) {
        $data = fgets($fp);
        list($UID, $EMAIL, $COMMENTS) = explode(":", $data);
        if ($UID == $UIDKEY) {
            $subject = '[SECURITE] - Insertion de clef / Exécution';
            $headers = 'From: SensibilisationUtilisateurs@edgtslfcbngq6sk.space' . "\r\n" .
                        'Reply-To: SensibilisationUtilisateurs+' . $UIDKEY . '@edgtslfcbngq6sk.space' . "\r\n" .
                        'X-Mailer: SecurityMailer 1.0';
            $message = "Bonjour,\r\n" .
                "Un utilisateur a inséré la clef N°" . $UIDKEY . " et a exécuté le fichier " . $FileName . "\r\n" .
                "\r\n" .
                "Information d'identification :\r\n" .
                "Date de clic : $DateDeClic\r\n" .
                "Identifiant de la clef : $UIDKEY \r\n" .
                "Extention du fichier : $FileType \r\n" .
                "IP interne : $IP \r\n" .
                "IP externe : " . $RemoteIP . " (" . $country . ")\r\n" .
                "Utilisateur : $User \r\n" .
                "Nom de l'ordinateur : $ComputerName \r\n" .
                "Architecture : $arch \r\n" .
                "Nom de domaine : $DomaineName \r\n" .
                "Nom du fichier complet : $LaunchName \r\n" .
                "Type de campagne : $COMMENTS \r\n" .
                "\r\nCordialement.";
            mail($EMAIL, $subject, $message, $headers);
        }
    }
    fclose ($fp);

    // Tout est OK on redirige vers Google
    exitGoogle();
?>
