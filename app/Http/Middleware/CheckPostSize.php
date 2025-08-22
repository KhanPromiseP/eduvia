<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPostSize
{
    public function handle(Request $request, Closure $next)
    {
        $max = $this->getPostMaxSize();

        if ($request->server('CONTENT_LENGTH') > $max && $max > 0) {
            return redirect()->back()
                ->withErrors(['file' => 'The uploaded file is too large. Please upload a file smaller than ' . $this->bytesToHuman($max) . '.'])
                ->withInput();
        }

        return $next($request);
    }

    protected function getPostMaxSize()
    {
        $postMax = ini_get('post_max_size');
        return $this->convertToBytes($postMax);
    }

    protected function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $val = (int) $value;

        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }

        return $val;
    }

    protected function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
