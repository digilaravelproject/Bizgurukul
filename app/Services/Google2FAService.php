<?php

namespace App\Services;

class Google2FAService
{
    /**
     * Generate a new secret key.
     */
    public function generateSecretKey($length = 16): string
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $validChars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Get QR Code URL.
     */
    public function getQRCodeUrl($company, $holder, $secret): string
    {
        return 'otpauth://totp/' . urlencode($company) . ':' . urlencode($holder) . 
               '?secret=' . $secret . '&issuer=' . urlencode($company);
    }

    /**
     * Verify a code.
     */
    public function verifyKey($secret, $code, $discrepancy = 1): bool
    {
        $currentTimeSlice = floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($calculatedCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate the code for a secret and time slice.
     */
    public function getCode($secret, $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);

        // Pack time into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        
        // Hash it with HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        
        // Dynamic truncation
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);
        
        $value = unpack('N', $hashPart)[1];
        $value = $value & 0x7FFFFFFF;
        
        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 decoding helper.
     */
    private function base32Decode($base32): string
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));

        $output = '';
        $i = 0;
        $buffer = 0;
        $bufferLength = 0;

        while ($i < strlen($base32)) {
            $char = strtoupper($base32[$i]);
            if (!isset($base32charsFlipped[$char])) {
                $i++;
                continue;
            }

            $buffer = ($buffer << 5) | $base32charsFlipped[$char];
            $bufferLength += 5;

            if ($bufferLength >= 8) {
                $output .= chr(($buffer >> ($bufferLength - 8)) & 0xFF);
                $bufferLength -= 8;
            }
            $i++;
        }

        return $output;
    }
}
