<?php /** @noinspection SpellCheckingInspection */

if (!function_exists('getallheaders')) {
    die("Error: getallheaders function does not exist!");
}

function send_json($response)
{
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

function detect_proxy($headers)
{
    $proxy_headers = array('client-ip', 'x-client-ip', 'forwarded', 'forwarded-for', 'x-forwarded-for', 'forwarded-for-ip', 'via', 'x-real-ip');

    $res = [
        'real_ip' => null,
        'proxy_detected' => false
    ];

    foreach ($headers as $name => $value) {
        $normalized = strtolower($name);

        if (in_array($normalized, $proxy_headers)) {
            $res['proxy_detected'] = true;

            if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $value, $matches) === 1) {
                $res['real_ip'] = $matches[1];
            }
        }
    }

    return $res;
}

$headers = getallheaders();
$proxy = detect_proxy($headers);

$data = array(
    'headers' => $headers,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'real_ip' => $proxy['real_ip'],
    'proxy_detected' => $proxy['proxy_detected']
);

send_json($data);

