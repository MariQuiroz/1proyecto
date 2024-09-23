<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'protocol' => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'quirozmolinamaritza@gmail.com',
    'smtp_pass' => 'zdmk qkfw wgdf lshq',
    'smtp_crypto' => 'tls',
    'mailtype' => 'html',
    'charset' => 'utf-8',
    'newline' => "\r\n",
    'wordwrap' => TRUE,
    'smtp_timeout' => 30
);

$config['smtp_crypto'] = 'tls';
$config['smtp_port'] = 587;
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_timeout'] = '30';
$config['smtp_keepalive'] = TRUE;
$config['smtp_ssl_verify_peer'] = FALSE;
$config['smtp_ssl_verify_peer_name'] = FALSE;
$config['smtp_ssl_allow_self_signed'] = TRUE;

$config['smtp_debug'] = 2;

