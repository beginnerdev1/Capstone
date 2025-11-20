<?php

if (!function_exists('is_real_email')) {
    /**
     * Check whether an email is plausibly deliverable.
     * - Uses filter_var for format
     * - Checks for MX DNS records for the domain
     * - Falls back to A record if MX not present
     *
     * Note: This cannot guarantee deliverability (only SMTP probe can),
     * but it prevents obvious fake domains like `asd@asd`.
     *
     * @param string $email
     * @return bool
     */
    function is_real_email(string $email): bool
    {
        $email = trim($email);
        if ($email === '') return false;

        // Basic format check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Extract domain
        $parts = explode('@', $email);
        if (count($parts) !== 2) return false;
        $domain = $parts[1];

        // If domain is an IP (unlikely), accept if valid
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return true;
        }

        // Normalize domain
        $domain = idn_to_ascii($domain);
        if ($domain === false) return false;

        // Check MX records
        if (function_exists('checkdnsrr')) {
            if (checkdnsrr($domain, 'MX')) {
                return true;
            }
            // fallback to A/AAAA record
            if (checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA')) {
                return true;
            }
        }

        // As a final fallback, try gethostbyname and see if it resolves to different string
        if (function_exists('gethostbyname')) {
            $resolved = gethostbyname($domain);
            if ($resolved && $resolved !== $domain) {
                return true;
            }
        }

        return false;
    }
}
