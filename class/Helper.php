<?php

/**
 * Da formato correcto a un blob corrupto.
 */
function fixingBlob($blob)
{  
    $blob = chunk_split(base64_encode($blob));
    $blob = str_ireplace(array("\r", "\n", "\\/", '\r', '\n', '\\/'), '', $blob);
    return $blob;
}

/**
 * Devuelve el token de la request del cliente.
 */
function getClientToken()
{
    $tokens = apache_request_headers();
    return $tokens['Authorization'];
}

function parse()
{
  $json = file_get_contents("php://input");
  return json_decode($json);
}