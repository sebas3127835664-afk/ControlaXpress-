<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

        //esto es un ejercicio de variables en php
        //estos son array

        $array = array("uno", "dos", "tres" );
        echo "<h1> Array ejemplo: </h1>";
        echo "<p>Los elementos:  " . $array[0] . ", " . $array[1] . " y " . $array[2] . ",   </p>";

        //ejercicio de operadores aritmeticos
        $a =10;
        $b =5;
        $suma = $a + $b;
        $resta = $a - $b;
        $multiplicacion = $a * $b;
        $division = $a / $b;

        echo "<h1> Operadores aritmeticos</h1>";
        echo "<p> La suma de $a y $b es: $suma</p>";
        echo "<p> La resta de $a y $b es: $resta</p>";
        echo "<p> La multiplicacion de $a y $b es: $multiplicacion</p>";
        echo "<p> La division de $a y $b es: $division</p>";

        //ejercicio en clase 

        /*
        El señor carlos y su esposa  tiene 5 hijos y 2 nietos 
        Los nietos tuvieron 5 hijos cada uno
        pasado el tiempo fallecieron 2 hijos y un nieto
        el señor carlos muy triste cuante cuantas personas quedan en su familia 
        incluyendole a el

        realiza la formula e imprime el resultado 
        */
        $CyE = 2;
        $hijos = 5;
        $nietos = 2;
        $h_nietos = 5;

        $t_hijos = $nietos * $h_nietos;
        echo "<p>Los hijos que tuvieron los nietos fuero: $t_hijos</p>";

        $fallecieron = $hijos - 2;
        echo "<p>al fallecimientos de los 2 hijos quedaron: $fallecieron</p>";

        $falle_nieto = $nietos - 1;
        echo "<p>al fallecimientos de 1 de los nietos quedaron: $falle_nieto</p>";

        $total = $t_hijos + $fallecieron + $falle_nieto + $CyE;
        echo "<p>el total de los familiares fueron que quedaron incluyendo a don carlos fue: $total</p>";



        //ejercicios de estructura de control
        //determinar el numero mayor de dos numeros

        $num1 = 15;
        $num2 = 14;
        $num3 = 20;

        if ($num1 > $num2)
            echo "El numero mayor es: $num1";
        else
            echo "El  numero mayor es: $num2";

        // determinar el numero mayor de tres numeros 


        if ($num1 > $num2 && $num1 > $num3){
            echo "El numero mayor es: $num1";
        }elseif ($num2 > $num1 && $num2 > $num3){
            echo "El numero mayor es: $num2";
        }else{
            echo "El numero mayor es: $num3 </br>";
        }

        
       
       
        // Ciclo While 
        
        $contador = 1;
        while ($contador <= 5){
            echo "Contador: $contador <br>";
            $contador++;
        }
        echo "<h1>Fuera de la estructura while</h1>" . $contador;


        //ciclo for 
        $array = array ("uno", "dos", "tres");
        foreach ($array as $valor){
            echo "<p> Valor: $valor </p> ";
        }

        
    ?>
</body>
</html>