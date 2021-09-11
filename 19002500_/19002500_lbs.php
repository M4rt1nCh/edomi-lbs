###[DEF]###
[name		=	Nächstgelegene Notfallapotheke von aponet]

[e#1 trigger=	Trigger]
[e#2        =   PLZ]
[e#3        =   API Timeout#init=10]


[a#1		=	Name der Apotheke]
[a#2		=	Straße & Hausnr.]
[a#3		=	PLZ]
[a#4		=	Ort]
[a#5		=	Entfernung (km)]
[a#6		=	Telefon]
[a#7		=	Zuletzt aktualisiert]
[a#8		=	Fehler]

###[/DEF]###


###[HELP]###
Ruft die aktuelle, nächstgelegene "Notfall-Apotheke" von aponet.de ab.
Die Zeiten für den Apothekennotdienst gelten 24h lang von 8.30 bis 8.30 Uhr am Folgetag. Ein Trigger alle 24h um kurz nach 8.30 Uhr wird empfohlen.

E1 = Trigger != 0 triggert den Baustein
E2 = Postleitzahl, anhand der die nächst gelegene Notfallapotheke ermittelt wird
E3 = Timeout in s

A1 = Name der Apotheke
A2 = Straße und Hausnummer
A3 = PLZ
A4 = Ort 
A5 = Entfernung in km
A6 = Telefonnummer
A7 = Zuletzt aktualisiert (Datum und Uhrzeit, an dem die Anfrage verarbeitet wurde)
A8 = Fehler

Autor Martin Chmielewski
Stand 10.09.2021

Changelog:
v0.1: Initiale Version

###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id) {
    if ($E=getLogicEingangDataAll($id)) {
        if (getLogicElementStatus($id)==0) {
            if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
                if($E[2]['value'] == "" or strlen($E[2]['value']) != 5) {
                    for ($i=1;$i<=6;$i++) {
                        logic_setOutput($id,$i,"");
                    }
                    logic_setOutput($id,7,date("Y-m-d H:i:s"));
                    logic_setOutput($id,8,"Fehler - keine gültige PLZ an E2");
                }
                else {
                    setLogicElementStatus($id,1);
                    callLogicFunctionExec(LBSID,$id);
                }
            }
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
sql_connect();
//-------------------------------------------------------------------------------------
if ($E=logic_getInputs($id)) {
    $baseUrl="https://www.aponet.de/apotheke/notdienstsuche?tx_aponetpharmacy_search[action]=result&type=1981&tx_aponetpharmacy_search[search][plzort]=";
    $ctx=stream_context_create(array('http' => array('timeout' => $E[3]['value'] )));
    $url = $baseUrl . $E[2]['value'];
    $json_result = json_decode(file_get_contents($url,false,$ctx));
    
    $next_apo = $json_result->features[0]->properties;
    if(empty($next_apo) == false) {
        setLogicLinkAusgang($id,1,$next_apo->name);
        setLogicLinkAusgang($id,2,$next_apo->strasse);
        setLogicLinkAusgang($id,3,$next_apo->plz);
        setLogicLinkAusgang($id,4,$next_apo->ort);
        setLogicLinkAusgang($id,5,$next_apo->distanz);
        setLogicLinkAusgang($id,6,$next_apo->telefon);
        setLogicLinkAusgang($id,8,"");
    }
    else {
        for ($i=1;$i<=6;$i++) {
            logic_setOutput($id,$i,"");
        }
        setLogicLinkAusgang($id,8,"");
    }
    setLogicLinkAusgang($id,7,date("Y-m-d H:i:s"));
}
// Stop LBS
setLogicElementStatus($id,0);
sql_disconnect();

?>
###[/EXEC]###
