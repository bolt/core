<?php

use Symfony\Component\Console\Style\SymfonyStyle;

function run(string $command, SymfonyStyle $symfonyStyle, bool $withOutput = false, string $onerror = ''): void {
    exec($command, $output, $return);
    if ($return) {
        if (empty($onerror)) {
            // Some error occurred.
            $message = sprintf("Command '%s' failed. %s", $command, implode("\n", $output));
            $symfonyStyle->error($message);
        } else {
            $symfonyStyle->error($onerror);
        }
    } else {
        if ($withOutput) {
            $symfonyStyle->text($output);
        }
        $message = sprintf("Command '%s' executed successfully.", $command);
        $symfonyStyle->success($message);
    }
}
