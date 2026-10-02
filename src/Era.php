<?php

declare(strict_types=1);

namespace RobloxSigner;

/**
 * Represents different Roblox client eras for script signature formats.
 *
 * Based on:
 * - Pre-2012: %<base64>%
 * - 2013+:    --rbxsig%<base64>%
 *
 * Later eras (2017–2019, 2019+) use the same signature string but may have
 * different expectations around how it's delivered (e.g., HTTP headers vs
 * inline script). For pure script-file signing, the difference is only prefix.
 */
enum Era: string
{
    /**
     * Pre-2012 clients.
     * Format: %<base64_signature>%
     */
    case PRE_2012 = 'pre_2012';

    /**
     * 2013–2016 era.
     * Format: --rbxsig%<base64_signature>%
     */
    case V2013 = 'v2013';

    /**
     * 2017–2019 era.
     * Format: --rbxsig%<base64_signature>% (same as 2013, but clients may be stricter).
     */
    case V2017 = 'v2017';

    /**
     * 2019+ era.
     * Format: --rbxsig%<base64_signature>% (same string; often used with HTTP signing).
     */
    case V2019 = 'v2019';

    /**
     * Returns the signature prefix for this era (without the % signs).
     */
    public function prefix(): string
    {
        return match ($this) {
            self::PRE_2012 => '',
            default        => '--rbxsig',
        };
    }

    /**
     * Whether this era expects the signature to be followed directly by the script
     * on the same response body (true) or just the token (false).
     *
     * For typical script-file serving, this is true for all eras.
     */
    public function includesScriptInResponse(): bool
    {
        // All known script-file eras send: [signature][\r\n + script]
        return true;
    }
}