

<?php
$mes = date("m");
$mes = 12;
for ($i=0; $i < 6; $i++) { 
    if ($i == 0) {
        # SE IMPRIMIE LO DEL MES ACTUA
        echo $mes;
        echo "<br>";
    } elseif ( $i > 0 ){
        $mes = $mes + 1;
        echo $mes;
        echo "<br>";
    }
}

?>