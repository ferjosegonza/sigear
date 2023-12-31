<?php

function encodeBase32($data) {
    $chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    $mask = 0b11111;

    $dataSize = strlen($data);
    $res = '';
    $remainder = 0;
    $remainderSize = 0;

    for ($i = 0; $i < $dataSize; $i++) {
        $b = ord($data[$i]);
        $remainder = ($remainder << 8) | $b;
        $remainderSize += 8;
        while ($remainderSize > 4) {
            $remainderSize -= 5;
            $c = $remainder & ($mask << $remainderSize);
            $c >>= $remainderSize;
            $res .= $chars[$c];
        }
    }
    if ($remainderSize > 0) {
        $remainder <<= (5 - $remainderSize);
        $c = $remainder & $mask;
        $res .= $chars[$c];
    }

    return $res;
}

function decodeBase32($data) {
    $map = [
        '0' => 0,
        'O' => 0,
        'o' => 0,
        '1' => 1,
        'I' => 1,
        'i' => 1,
        'L' => 1,
        'l' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10,
        'a' => 10,
        'B' => 11,
        'b' => 11,
        'C' => 12,
        'c' => 12,
        'D' => 13,
        'd' => 13,
        'E' => 14,
        'e' => 14,
        'F' => 15,
        'f' => 15,
        'G' => 16,
        'g' => 16,
        'H' => 17,
        'h' => 17,
        'J' => 18,
        'j' => 18,
        'K' => 19,
        'k' => 19,
        'M' => 20,
        'm' => 20,
        'N' => 21,
        'n' => 21,
        'P' => 22,
        'p' => 22,
        'Q' => 23,
        'q' => 23,
        'R' => 24,
        'r' => 24,
        'S' => 25,
        's' => 25,
        'T' => 26,
        't' => 26,
        'V' => 27,
        'v' => 27,
        'W' => 28,
        'w' => 28,
        'X' => 29,
        'x' => 29,
        'Y' => 30,
        'y' => 30,
        'Z' => 31,
        'z' => 31,
    ];

    $data = strtoupper($data);
    $dataSize = strlen($data);
    $buf = 0;
    $bufSize = 0;
    $res = '';

    for ($i = 0; $i < $dataSize; $i++) {
        $c = $data[$i];
        if (!isset($map[$c])) {
            //throw new \Exception("Unsupported character $c (0x".bin2hex($c).") at position $i");
            return null;
        }
        $b = $map[$c];
        $buf = ($buf << 5) | $b;
        $bufSize += 5;
        if ($bufSize > 7) {
            $bufSize -= 8;
            $b = ($buf & (0xff << $bufSize)) >> $bufSize;
            $res .= chr($b);
        }
    }

    return $res;
}

function encodeBase64URL($data) {
    $b64 = base64_encode($data);
    if ($b64 === false) {
        return false;
    }

    // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
    $url = strtr($b64, '+/', '-_');

    // Remove padding character from the end of line and return the Base64URL result
    return rtrim($url, '=');
}

function decodeBase64URL($data, $strict = false) {
    // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
    $b64 = strtr($data, '-_', '+/');

    // Decode Base64 string and return the original data
    return base64_decode($b64, $strict);
}

function obtenerSID($v, $len = 7) {
    $h = encodeBase32(hash('sha256', $v, true));
    return substr($h, 0, $len);
}

function obtenerRandomHexToken($len) {
    $bytesLen = ceil($len / 2); // 4 bits por caracter 8 bits por byte
    $str = bin2hex(random_bytes($bytesLen));
    return substr($str, 0, $len);
}

function obtenerRandomBase32Token($len) {
    $bytesLen = ceil($len * 5 / 8); // 5 bits por caracter 8 bits por byte
    $str = encodeBase32(random_bytes($bytesLen));
    return substr($str, 0, $len);
}

function obtenerRandomBase64Token($len) {
    $bytesLen = ceil($len * 3 / 4); // 6 bits por caracter 8 bits por byte
    $str = encodeBase64URL(random_bytes($bytesLen));
    return substr($str, 0, $len);
}

function generarRS256Keys($bits = 2048) {
    $pkGenerate = openssl_pkey_new(array(
        'private_key_bits' => $bits,
        'private_key_type' => OPENSSL_KEYTYPE_RSA
    ));

    openssl_pkey_export($pkGenerate, $privateKey);
    $publicKey = openssl_pkey_get_details($pkGenerate)['key'];

    openssl_pkey_free($pkGenerate);

    return array(
        'private' => $privateKey,
        'public' => $publicKey
    );
}

function encriptarCryptoJsAes(string $passphrase, string $value) {
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

function desencriptarCryptoJsAes(string $passphrase, string $jsonString) {
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

function isUUID(string $uuid): bool {
    return Ramsey\Uuid\Uuid::isValid($uuid);
}
