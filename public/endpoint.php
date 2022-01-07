<?php

require __DIR__ . '/../vendor/autoload.php';

function _log($str) {
    file_put_contents(__DIR__ . '/../deploy.log', $str, FILE_APPEND);
}

function _command($command) {
    echo "> $command\n";
    $proc = proc_open($command, array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("pipe", "w") // stderr is a file to write to
    ), $pipes, $_ENV['TARGET_PATH']);
    $output = stream_get_contents($pipes[1]);
    $output_error = stream_get_contents($pipes[2]);
    proc_close($proc);

    if ($output) {
        echo $output . "\n";
    }
    if (trim($output_error)) {
        echo 'OUTPUT_ERROR: ' . $output_error . "\n";
        //throw new Exception('Command error');
    }
}

_log(date('c') . "\n---\n" . file_get_contents('php://input') . "\n\n");

try {
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
    $dotenv->load();

    $decoded = @json_decode(file_get_contents('php://input'));

    if (!$decoded) {
        throw new Exception('Bad content');
    }

    if (!isset($decoded->ref) || $decoded->ref != 'refs/heads/' . $_ENV['BRANCH']) {
        throw new Exception('Wrong ref');
    }

    chdir($_ENV['TARGET_PATH']);

    include __DIR__ . '/../deploy_script.php';

} catch (Exception $e) {
    _log('ERROR: ' . $e->getFile() . '@' . $e->getLine() . ': ' . $e->getMessage() . "\n\n");
    throw $e;
}