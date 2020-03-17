<?php

$id_contenedor = htmlspecialchars($_COOKIE["id-contenedor"]);

if (isset($id_contenedor)) {
    exec('docker stop ' . $id_contenedor);
    exec('docker rm ' . $id_contenedor);

    setcookie("nombre", "", time() - 3600);
    setcookie("id-contenedor", "", time() - 3600);
    setcookie("dir-actual", "", time() - 3600);
    
    echo "OK";
} else {
    echo "NO";
}
