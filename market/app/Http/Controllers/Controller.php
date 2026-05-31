<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Normaliza una URL de imagen proporcionada por el vendedor.
     * - Si ya es absoluta (http/https), se devuelve tal cual.
     * - Si comienza con // o / se devuelve tal cual.
     * - Si parece un nombre de archivo con extensión, se asume que está
     *   en la carpeta pública `/imagenes/` y se prefixa.
     * - Si está vacío, devuelve null.
     */
    protected function normalizeImageUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        // URL absolute (http/https), protocol-relative (//), root-relative (/...), data URI or blob URI.
        if (preg_match('#^(https?://|//|/|data:|blob:)#i', $url)) {
            return $url;
        }

        // Si parece un nombre de archivo con extensión común, asumir carpeta /imagenes/
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $url)) {
            return '/imagenes/' . ltrim($url, '/');
        }

        // Devolver tal cual si no encaja en los casos anteriores
        return $url;
    }
}
