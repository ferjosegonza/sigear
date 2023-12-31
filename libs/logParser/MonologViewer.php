<?php
namespace Jtclark;
use Exception;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
//use Westsworld\TimeAgo;

class MonologViewer
{
    protected $settings = [];

    public function __construct(array $settings)
    {
        if (empty($settings['path'])) {
            throw new Exception('Log path must be set');
        }
        $this->settings = $settings;
    }


    /**
     * Filters the logs and renders them
     *
     * @param int $lines Number of lines to return, if null, entire file will be read
     * @param string $logLevelFilter log level type to filter, debug, critical, error, warning, info
     * @param string $supportCode
     * @param string $search
     * @throws Exception
     * @return void
     */
    public function render($lines = 100, $logLevelFilter = null, $supportCode = null, $search = null)
    {
        $logPath = $this->settings['path'];

        if ($logLevelFilter === 'all') {
            $logLevelFilter = null;
        }

        // make sure log path actually exists
        if (!file_exists($logPath)) {
            die('Log file does not exist');
        }

        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader, [
            'debug' => true,
        ]);

        $twig->addFilter(new TwigFilter('ago', function ($dateTime) {
            //$timeAgo = new TimeAgo();
            //return $timeAgo->inWordsFromStrings($dateTime);
        }));

        $twig->addFilter(new TwigFilter('alertIconClass', function ($string) {
            switch ($string) {
                case 'ERROR':
                case 'CRITICAL':
                    return 'fa-exclamation-circle';
                    break;
                case 'WARNING':
                    return 'fa-exclamation-triangle';
                    break;
                case 'INFO':
                case 'DEBUG':
                    return 'fa-info-circle';
                    break;
            }
            return '';
        }));

        $twig->addFilter(new TwigFilter('alertClass', function ($string) {
            switch ($string) {
                case 'ERROR':
                case 'CRITICAL':
                    return 'alert-danger';
                    break;
                case 'WARNING':
                    return 'alert-warning';
                    break;
                case 'INFO':
                case 'DEBUG':
                    return 'alert-info';
                    break;
            }
            return '';
        }));

        if ($lines === null) {
            $lines = array_reverse(explode("\n", file_get_contents($logPath)));
        } else {
            $lines = array_reverse(explode("\n", $this->tail($logPath, $lines)));
        }
        $logs = [];
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }
            $json = json_decode($line, true);
            if ($json === null) {
                die('Could not read log line: ' . $line);
            }

            if (strtolower($logLevelFilter) && strtolower($json['level_name']) != strtolower($logLevelFilter)) {
                continue;
            }
            if ($supportCode && $json['extra']['uid'] != $supportCode) {
                continue;
            }
            if ($search && strpos($line, $search) === false) {
                continue;
            }

            $logs[] = $json;

        }
        //var_export($logs);
        $template = !empty($this->settings['template']) ? $this->settings['template'] : 'log.twig';
        echo $twig->render($template, [
            'logs' => $logs,
            'search' => [
                'sc' => $supportCode,
                'filter' => $logLevelFilter,
                'q' => $search
            ]
        ]);
    }

    /**
     * Read X lines using a dynamic buffer (more efficient for all file sizes)
     *
     * @author Lorenzo Stanco
     * @url https://gist.github.com/lorenzos/1711e81a9162320fde20
     *
     * @param $filepath
     * @param int $lines
     * @param bool|true $adaptive
     * @return bool|string
     */
    public function tail($filepath, $lines = 100, $adaptive = true)
    {

        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {

            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");

        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {

            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);

        }

        // Close file and return
        fclose($f);
        return trim($output);

    }


    
}