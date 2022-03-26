<?php
$attributes = array(
    "countryName" => "GB",
    "stateOrProvinceName" => "Somerset",
    "localityName" => "Glastonbury",
    "organizationName" => "The Brain Room Limited",
    "organizationalUnitName" => "PHP Documentation Team",
    "commonName" => "Wez Furlong",
    "emailAddress" => "wez@example.com"
);
        
$privateKey = openssl_pkey_new(array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));

openssl_pkey_export($privateKey, $pkeyout, "mypassword") and var_dump($pkeyout);
        
// Generate a certificate signing request
$csr = openssl_csr_new($attributes, $privateKey, array('digest_alg' => 'sha256'));
openssl_csr_export($csr, $csrout) and var_dump($csrout);
        
// Generate a self-signed cert, valid for 365 days
$x509 = openssl_csr_sign($csr, null, $privateKey, $days=365, array('digest_alg' => 'sha256'));
openssl_x509_export($x509, $certout) and var_dump($certout);
        
// Show any errors that occurred here
while (($e = openssl_error_string()) !== false) {
    echo $e . "-------------------";
}

?>