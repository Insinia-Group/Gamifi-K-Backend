<?php

function fixingBlob($blob)
{
    $blob = chunk_split(base64_encode($blob));
    $blob = str_ireplace(array("\r", "\n", "\\/", '\r', '\n', '\\/'), '', $blob);
    return $blob;
}
