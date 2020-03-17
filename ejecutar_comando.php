<?php

$comando = filter_input(INPUT_POST, "comando", FILTER_SANITIZE_STRING);

$id_contenedor = htmlspecialchars($_COOKIE["id-contenedor"]);

if (isset($id_contenedor)) {
    $dir_actual = "/";
    if (isset($_COOKIE["dir-actual"])) {
        $dir_actual = htmlspecialchars($_COOKIE["dir-actual"]);
        if (!isset($dir_actual)) {
            $dir_actual = "/";
        }
    }
    ob_start();
    $cmd = 'docker exec -w ' . $dir_actual . ' ' . $id_contenedor . ' bash -c "' . $comando . '; pwd"';
    $salidas = null;
    exec($cmd . ' 2>&1', $salidas);
    $result = ob_get_contents();
    ob_end_clean();
    $salida = "";
    $i = 0;
    $count_salidas = count($salidas);
    $dir_actual = "/";
    foreach ($salidas as $s) {
        if ($i < $count_salidas - 1) {
            $salida .= $s . "<br/>";
        } else {
            $dir_actual = $s;
        }
        $i += 1;
    }
    setcookie("dir-actual", $dir_actual);
    echo $salida;
} else {
    echo "NO";
}
