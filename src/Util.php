<?php

declare(strict_types=1);

namespace RobloxSigner;

class Util
{
    /**
     * Sign a script file for a specific era.
     */
    public static function signFile(
        string $scriptPath,
        string $privateKeyPath,
        Era $era = Era::V2019
    ): string {
        $content = file_get_contents($scriptPath);
        if ($content === false) {
            throw new \RuntimeException("Unable to read script from: {$scriptPath}");
        }

        return Signer::sign($content, $privateKeyPath, $era);
    }

    /**
     * Legacy boolean-based wrapper.
     */
    public static function signFileLegacy(
        string $scriptPath,
        string $privateKeyPath,
        bool $useNewFormat = true
    ): string {
        $era = $useNewFormat ? Era::V2019 : Era::PRE_2012;
        return self::signFile($scriptPath, $privateKeyPath, $era);
    }
}