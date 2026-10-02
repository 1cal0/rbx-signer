<?php

declare(strict_types=1);

namespace RobloxSigner;

class Signer
{
    /**
     * Sign a Lua script for a given Roblox era.
     *
     * @param string $scriptContent Raw Lua script content (without leading \r\n).
     * @param string $privateKeyPath Path to PrivateKey.pem.
     * @param Era $era Roblox era to target.
     * @return string Signed script output ready to send to the client.
     */
    public static function sign(
        string $scriptContent,
        string $privateKeyPath,
        Era $era = Era::V2019
    ): string {
        // Roblox expects a newline before the script content when signing.
        $scriptWithNewline = "\r\n" . $scriptContent;

        $privateKey = file_get_contents($privateKeyPath);
        if ($privateKey === false) {
            throw new \RuntimeException("Unable to read private key from: {$privateKeyPath}");
        }

        $signature = '';
        $ok = openssl_sign(
            $scriptWithNewline,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA1
        );

        if (!$ok) {
            throw new \RuntimeException("openssl_sign failed: " . openssl_error_string());
        }

        $signatureB64 = base64_encode($signature);
        $prefix = $era->prefix();

        // Build signature token: [prefix]%<base64>%
        $signatureToken = "{$prefix}%{$signatureB64}%";

        if (!$era->includesScriptInResponse()) {
            // If an era ever wants only the token, return that.
            return $signatureToken;
        }

        // Typical behavior: signature token followed by the script with leading \r\n.
        return $signatureToken . $scriptWithNewline;
    }

    /**
     * Backwards-compatible helper using a boolean flag.
     *
     * @param bool $useNewFormat true => 2013+ era, false => pre-2012.
     */
    public static function signLegacy(
        string $scriptContent,
        string $privateKeyPath,
        bool $useNewFormat = true
    ): string {
        $era = $useNewFormat ? Era::V2019 : Era::PRE_2012;
        return self::sign($scriptContent, $privateKeyPath, $era);
    }
}