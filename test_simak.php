<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$client = new Client(['verify' => false]);
$cookieJar = new CookieJar();

// 1. Login
$loginRes = $client->post('https://202.146.181.102/login/proses/', [
    'cookies' => $cookieJar,
    'headers' => ['Host' => 'simak.uss.ac.id'],
    'form_params' => [
        'username' => 'hadi.syaputra',
        'password' => '9601'
    ]
]);

// 2. Dashboard
$dashRes = $client->get('https://202.146.181.102/apps/dashboard/dashboard/', [
    'cookies' => $cookieJar,
    'headers' => ['Host' => 'simak.uss.ac.id']
]);
$html = $dashRes->getBody()->getContents();

$targetUrl = "https://202.146.181.102/apps/dosen/jadwal/5670/ta-11~REGULER/1/";
echo "Target URL: $targetUrl\n";

// 3. Akses Jadwal page to find JADWAL RUANGAN link
$jadwalRes = $client->get($targetUrl, [
    'cookies' => $cookieJar,
    'headers' => ['Host' => 'simak.uss.ac.id']
]);
$jadwalHtml = $jadwalRes->getBody()->getContents();

file_put_contents('simak_jadwal_full.html', $jadwalHtml);
echo "Saved to simak_jadwal_full.html\n";

$ruangUrl = "https://202.146.181.102/apps/krs/gedung/ruang/daftar/5670/0~11~REGULER/";
$ruangRes = $client->get($ruangUrl, [
    'cookies' => $cookieJar,
    'headers' => ['Host' => 'simak.uss.ac.id']
]);
file_put_contents('simak_ruangan.html', $ruangRes->getBody()->getContents());
echo "Saved to simak_ruangan.html\n";
