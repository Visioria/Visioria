<?php
// awstats_reader.php - versão que devolve também histórico anual
header('Content-Type: application/json');

// Recebe year/month (opcionais)
$year  = isset($_GET['year']) ? (int)$_GET['year'] : null;
$month = isset($_GET['month']) ? (int)$_GET['month'] : null;

// Se não foram passados, encontra o último ficheiro modificado
if (!$year || !$month) {
    $files = glob("/home/visioria/tmp/awstats/awstats*.visioria.pt.txt");
    if ($files) {
        usort($files, function($a, $b) {
            return filemtime($b) <=> filemtime($a); // mais recente primeiro
        });
        $latestPath = $files[0]; // caminho completo
        $latestFile = basename($latestPath);
        if (preg_match('/awstats(\d{2})(\d{4})/', $latestFile, $m)) {
            $month = (int)ltrim($m[1], '0'); // "09" -> 9
            $year  = (int)$m[2];
        } else {
            // fallback para hoje
            $year  = (int)date('Y');
            $month = (int)date('n');
        }
    } else {
        // sem ficheiros -> fallback para hoje
        $year  = (int)date('Y');
        $month = (int)date('n');
    }
}

$monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
if ($month && $year) {
    $file = "/home/visioria/tmp/awstats/awstats{$monthPadded}{$year}.visioria.pt.txt";
} else {
    // Se não vier parâmetro, usar o último arquivo modificado
    $file = $latestPath;
}

// Estruturas de saída
$general = [];
$data = [
    'days'     => [],
    'months'   => [], // será sobrescrito por monthsTotals
    'devices'  => [],
    'viewers'  => [],
    'referers' => [],
    'locales'  => []
];

// Lê ficheiro do mês ativo (mantém o seu parser)
if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $currentBlock = null;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;

        if (preg_match('/^BEGIN_(\w+)/', $line, $m)) {
            $currentBlock = strtoupper($m[1]);
            continue;
        }
        if (preg_match('/^END_(\w+)/', $line)) {
            $currentBlock = null;
            continue;
        }

        // General
        if ($currentBlock === "GENERAL") {
            $parts = preg_split('/\s+/', $line, 2);
            if (count($parts) === 2) {
                $general[$parts[0]] = $parts[1];
            }
        }

        // DAY (YYYYMMDD visits ...)
        if ($currentBlock === "DAY") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $day = substr($parts[0], -2);
                $data['days'][$day] = (int)$parts[1];
            }
        }

        // MONTH (YYYYMM visits ...)
        if ($currentBlock === "MONTH") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $monthKey = substr($parts[0], -2);
                $data['months'][$monthKey] = (int)$parts[1];
            }
        }

        // OS
        if ($currentBlock === "OS") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $os = $parts[0];
                $data['devices'][$os] = (int)$parts[1];
            }
        }

        // BROWSER
        if ($currentBlock === "BROWSER") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $browser = $parts[0];
                $data['viewers'][$browser] = (int)$parts[1];
            }
        }

        // DOMAIN
        if ($currentBlock === "DOMAIN") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $domain = $parts[0];
                $data['locales'][$domain] = (int)$parts[1];
            }
        }

        // REFERER
        if ($currentBlock === "REFERER") {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 2) {
                $ref = $parts[0];
                $data['referers'][$ref] = (int)$parts[1];
            }
        }
    }
} else {
    error_log("AWStats: arquivo não encontrado: $file");
}

// --------------------
// Monta histórico anual (visitas + visitantes únicos)
// --------------------
$monthsTotals = [];
$monthsUnique = [];
for ($m = 1; $m <= 12; $m++) {
    $mp = str_pad($m, 2, '0', STR_PAD_LEFT);
    $f  = "/home/visioria/tmp/awstats/awstats{$mp}{$year}.visioria.pt.txt";

    $monthsTotals[$mp] = 0;
    $monthsUnique[$mp] = 0;

    if (!file_exists($f)) {
        // ficheiro não existe -> 0
        continue;
    }

    // lê só o bloco GENERAL preferencialmente (TotalVisits, TotalUnique)
    $linesM = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $inGeneral = false;
    $foundVisits = null;
    $foundUnique = null;

    foreach ($linesM as $l) {
        $l = trim($l);
        if ($l === '') continue;

        if (stripos($l, 'BEGIN_GENERAL') === 0) { $inGeneral = true; continue; }
        if (stripos($l, 'END_GENERAL') === 0)   { $inGeneral = false; break; }

        if ($inGeneral) {
            $parts = preg_split('/\s+/', $l, 2);
            if (count($parts) === 2) {
                $key = $parts[0];
                $val = trim($parts[1]);
                if ($key === 'TotalVisits' && $foundVisits === null) {
                    $foundVisits = (int)$val;
                }
                if ($key === 'TotalUnique' && $foundUnique === null) {
                    $foundUnique = (int)$val;
                }
            }
        }
    }

    // Se não encontrou no GENERAL, tenta o bloco MONTH (YYYYMM visits ...)
    if ($foundVisits === null) {
        $inMonth = false;
        foreach ($linesM as $l) {
            $l = trim($l);
            if ($l === '') continue;
            if (stripos($l, 'BEGIN_MONTH') === 0) { $inMonth = true; continue; }
            if (stripos($l, 'END_MONTH') === 0)   { $inMonth = false; break; }
            if ($inMonth) {
                $parts = preg_split('/\s+/', $l);
                if (count($parts) >= 2) {
                    $monthKey = substr($parts[0], -2);
                    if ($monthKey === $mp) {
                        $foundVisits = (int)$parts[1];
                        break;
                    }
                }
            }
        }
    }

    $monthsTotals[$mp] = $foundVisits ?? 0;
    $monthsUnique[$mp] = $foundUnique ?? 0;
}

// --------------------
// Tratamento do LastUpdate
// --------------------
// --------------------
// Tratamento do LastUpdate
// --------------------
$rawLastUpdate = $general['LastUpdate'] ?? '';
$lastUpdateDate = substr($rawLastUpdate, 0, 14);

if ($lastUpdateDate && ($dt = DateTime::createFromFormat('YmdHis', $lastUpdateDate))) {
    // formato legível em português
    setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil');
    $lastUpdateFormatted = strftime('%d de %B de %Y, %H:%M', $dt->getTimestamp());
    $lastUpdateMonth     = ucfirst(strftime('%B', $dt->getTimestamp())); // Setembro
    $lastUpdateYear      = $dt->format('Y');
} else {
    $dt = new DateTime();
    setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil');
    $lastUpdateFormatted = strftime('%d de %B de %Y, %H:%M', $dt->getTimestamp());
    $lastUpdateMonth     = ucfirst(strftime('%B', $dt->getTimestamp()));
    $lastUpdateYear      = $dt->format('Y');
}


// Monta resposta final (mantendo formato anterior)
$response = [
    // Cards (mês ativo)
    'uniqueVisitors' => (int)($general['TotalUnique'] ?? 0),
    'totalVisits'    => (int)($general['TotalVisits'] ?? 0),
    'pagesPerVisit'  => 0,
    'avgDuration'    => 0,
    'lastUpdate'     => $lastUpdateFormatted,
    'lastUpdateMonth'=> $lastUpdateMonth,
    'lastUpdateYear' => $lastUpdateYear,
    'year'   => $year,
    'month'  => $month,

    // Dados para gráficos
    'days'         => $data['days'],
    'months'       => $monthsTotals,
    'monthsUnique' => $monthsUnique,
    'devices'      => $data['devices'],
    'viewers'      => $data['viewers'],
    'referers'     => $data['referers'],
    'locales'      => $data['locales']
];

// Converte locales em lista com percentuais
$regionsList = [];
$totalLocales = array_sum($data['locales']);

foreach ($data['locales'] as $code => $val) {
    $percent = $totalLocales > 0 ? round(($val / $totalLocales) * 100, 1) : 0;
    $regionsList[] = [
        'name'    => strtoupper($code), // exemplo: BR, US
        'value'   => $val,
        'percent' => $percent
    ];
}

// Substitui no response
$response['regions'] = $regionsList;

// Normaliza dispositivos em 4 categorias: Desktop, Mobile, Tablet, Other
$devicesRaw = $data['devices'];
$devicesGrouped = [
    'Desktop' => 0,
    'Mobile'  => 0,
    'Tablet'  => 0,
    'Other'   => 0
];

foreach ($devicesRaw as $os => $count) {
    $osLower = strtolower($os);

    if (strpos($osLower, 'win') !== false ||
        strpos($osLower, 'mac') !== false ||
        strpos($osLower, 'linux') !== false) {
        $devicesGrouped['Desktop'] += $count;
    }
    elseif (strpos($osLower, 'android') !== false ||
            strpos($osLower, 'iphone') !== false ||
            strpos($osLower, 'ios') !== false) {
        $devicesGrouped['Mobile'] += $count;
    }
    elseif (strpos($osLower, 'ipad') !== false ||
            strpos($osLower, 'tablet') !== false) {
        $devicesGrouped['Tablet'] += $count;
    }
    else {
        $devicesGrouped['Other'] += $count;
    }
}

// Substitui no response
$response['devices'] = $devicesGrouped;


echo json_encode($response, JSON_UNESCAPED_UNICODE);
