<?php

use Symfony\Component\Console\Style\SymfonyStyle;

function run(string $command, SymfonyStyle $symfonyStyle, bool $withOutput = false): void {
    exec($command, $output, $return);
    if ($return) {
        // Some error occurred.
        $message = sprintf("Command '%s' failed. %s", $command, implode("\n", $output));
        $symfonyStyle->error($message);
    } else {
        if ($withOutput) {
            $symfonyStyle->text($output);
        }
        $message = sprintf("Command '%s' executed successfully.", $command);
        $symfonyStyle->success($message);
    }
}
