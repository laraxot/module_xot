<?php
/**
 * @see https://dev.to/kodeas/executing-shell-commands-in-laravel-1098
 */
declare(strict_types=1);

namespace Modules\Xot\Actions;

use Spatie\QueueableAction\QueueableAction;
use Symfony\Component\Process\Process;

class ShellCommandAction
{
    use QueueableAction;

    public static function execute(string $cmd): string
    {
        $process = Process::fromShellCommandline($cmd);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        if ($process->getExitCode()) {
            // $exception = new ShellCommandFailedException($cmd.' - '.$processOutput);
            // report($exception);
            // throw $exception;
            throw new \Exception($cmd.' - '.$processOutput);
        }

        return $processOutput;
    }
}
