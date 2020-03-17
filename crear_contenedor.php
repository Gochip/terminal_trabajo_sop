<?php

/*
https://www.digitalocean.com/community/questions/how-to-fix-docker-got-permission-denied-while-trying-to-connect-to-the-docker-daemon-socket

    ubuntu@ip-172-31-21-106:/var/run$ ls -lrth docker.sock
    srw-rw---- 1 root root 0 Oct 17 11:08 docker.sock
    ubuntu@ip-172-31-21-106:/var/run$ sudo chmod 666 /var/run/docker.sock
    ubuntu@ip-172-31-21-106:/var/run$ ls -lrth docker.sock
    srw-rw-rw- 1 root root 0 Oct 17 11:08 docker.sock
*/

$nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_STRING);

$id_contenedor = null;
ob_start();
exec("docker run -d -it ubuntu bash 2>&1", $id_contenedor);
$result = ob_get_contents();
ob_end_clean();
echo $id_contenedor;

setcookie("nombre", $nombre);
setcookie("id-contenedor", $id_contenedor[0]);
setcookie("dir-actual", "/");

