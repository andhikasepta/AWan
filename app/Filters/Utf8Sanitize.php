<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Sanitizes all incoming request data to ensure valid UTF-8 encoding.
 * Prevents PostgreSQL "invalid byte sequence for encoding UTF8: 0xa0" errors.
 */
class Utf8Sanitize implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Sanitize superglobals that CI4 reads from
        $_GET     = $this->cleanUtf8($_GET);
        $_POST    = $this->cleanUtf8($_POST);
        $_REQUEST = $this->cleanUtf8($_REQUEST);
        $_COOKIE  = $this->cleanUtf8($_COOKIE);

        // Also update CI4's Superglobals service internal store
        $superglobals = service('superglobals');
        $superglobals->setGetArray($_GET);
        $superglobals->setPostArray($_POST);
        $superglobals->setRequestArray($_REQUEST);
        $superglobals->setCookieArray($_COOKIE);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
        return $response;
    }

    /**
     * Recursively clean all string values in an array to valid UTF-8.
     *
     * @param mixed $data
     * @return mixed
     */
    private function cleanUtf8(mixed $data): mixed
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->cleanUtf8($value);
            }
            return $result;
        }

        if (is_string($data)) {
            // Replace 0xa0 (non-breaking space in Latin-1) with regular space
            $data = str_replace("\xC2\xA0", ' ', $data); // UTF-8 encoded NBSP
            $data = str_replace("\xA0", ' ', $data);      // Raw 0xa0 byte

            // Remove all other invalid UTF-8 sequences
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');

            // Final safety: strip any remaining non-UTF-8 bytes
            $data = preg_replace('/[\x80-\x9F]/u', '', $data) ?? $data;

            return $data;
        }

        return $data;
    }
}
