###[DEF]###
[name		=	MDT Beschattungs-Diagnosetext JAL-B1UP.02]

[e#1 trigger=	Diagnose]

[a#1		=	Bereitschaft]
[a#2		=	Sperre Beschattung]
[a#3		=	Sperre Außentemperatur]
[a#4		=	Helligkeits-Schwelle]
[a#5		=	Azimut]
[a#6		=	Sonnenstand]

###[/DEF]###


###[HELP]###
Bereitet den Beschattungs-Diagnosetext des Jalousieaktors für eine Visualisierung auf

E1 = Beschattungs-Diagnosetext vom MDT Jalousieaktor JAL-B1UP.02

A1 = Beschattungs-Bereitschaft (0/1)
A2 = Sperre Beschattung (0/1)
A3 = Sperre Außentemperatur (0/1)
A4 = Helligkeitsschwelle (0/1/2)
A5 = Errechneter Sonnenstand (Azimut), nummerischer Wert (0..359)
A6 = Errechneter Sonnenhöhe (Elevation), nummerischer Wert (0..90)

Autor Martin Chmielewski
Stand 12.08.2021

###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh'] && $E[1]['value']!="") {
            // Get the Delimiter, default to '|'
            if ($E[2]['value']=="") {
                $delimiter = "|";
            }
            else {
                $delimiter = $E[2]['value'];
            }

            // Split the input (4 blocks will be available)
            $text_splitted = explode(" ",$E[1]['value']);

            // First Block: Mx to identify status of shading
            switch (substr($text_splitted[0],1,1)) {
                case "0": // 000
                    $shading_readiness = 0;
                    $shading_lock = 0;
                    $outside_temp_lock = 0;
                break;
                case "1": // 001
                    $shading_readiness = 1;
                    $shading_lock = 0;
                    $outside_temp_lock = 0;
                break;
                case "2": // 010
                    $shading_readiness = 0;
                    $shading_lock = 1;
                    $outside_temp_lock = 0;
                break;
                case "3": // 011
                    $shading_readiness = 1;
                    $shading_lock = 1;
                    $outside_temp_lock = 0;
                break;
                case "4": // 100
                    $shading_readiness = 0;
                    $shading_lock = 0;
                    $outside_temp_lock = 1;
                break;
                case "5": // 101
                    $shading_readiness = 1;
                    $shading_lock = 0;
                    $outside_temp_lock = 1;
                break;
                case "6": // 110
                    $shading_readiness = 0;
                    $shading_lock = 1;
                    $outside_temp_lock = 1;
                break;
                case "7": // 111
                    $shading_readiness = 1;
                    $shading_lock = 1;
                    $outside_temp_lock = 1;
                break;
                default:
                    $shading_readiness = "Fehler";
                    $shading_lock = "Fehler";
                    $outside_temp_lock = "Fehler";
                break;
            }
            // Write Outputs
            logic_setOutput($id,1,$shading_readiness);
            logic_setOutput($id,2,$shading_lock);
            logic_setOutput($id,3,$outside_temp_lock);

            // Second Block: Sx to identify status of the brightness threshold
            switch (substr($text_splitted[1],1,1)) {
                case "0":
                    $threshold = 0;
                break;
                case "1":
                    $threshold = 1;
                break;
                case "2":
                    $threshold = 2;
                break;
                default:
                $threshold =  "Fehler";
                break;
            }
            // Write Output
            logic_setOutput($id,4,$threshold);

            // Third Block: Axxx for the calculated Azimut
            $azimut = substr($text_splitted[2],1);
            // Write Output
            logic_setOutput($id,5,$azimut);

            // Fourth Block: Exxx for the calculated Elevation
            $elevation = substr($text_splitted[3],1);
            // Write Output
            logic_setOutput($id,6,$elevation);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###
