<?php

namespace App\Support;

/**
 * Similarité entre deux textes par le cosinus des fréquences de mots.
 * PHP pur, sans dépendance : rapide même sur un mémoire entier
 * (similar_text() est trop lent sur de longs textes).
 */
class SimilariteTexte
{
    private const MOTS_VIDES = [
        'les', 'des', 'une', 'dans', 'pour', 'par', 'sur', 'avec', 'que', 'qui', 'est', 'sont',
        'aux', 'ces', 'ses', 'son', 'leur', 'leurs', 'plus', 'pas', 'mais', 'ont', 'été', 'être',
        'cette', 'comme', 'aussi', 'ainsi', 'donc', 'entre', 'tout', 'tous', 'fait', 'peut',
        'the', 'and', 'for', 'with',
    ];

    /**
     * Retourne un score entre 0 (aucun point commun) et 1 (textes identiques).
     */
    public static function cosinus(string $texteA, string $texteB): float
    {
        $frequencesA = self::frequences($texteA);
        $frequencesB = self::frequences($texteB);

        if ($frequencesA === [] || $frequencesB === []) {
            return 0.0;
        }

        $produit = 0.0;
        foreach ($frequencesA as $mot => $nombre) {
            if (isset($frequencesB[$mot])) {
                $produit += $nombre * $frequencesB[$mot];
            }
        }

        $normeA = sqrt(array_sum(array_map(fn ($n) => $n * $n, $frequencesA)));
        $normeB = sqrt(array_sum(array_map(fn ($n) => $n * $n, $frequencesB)));

        if ($normeA == 0.0 || $normeB == 0.0) {
            return 0.0;
        }

        return $produit / ($normeA * $normeB);
    }

    /**
     * @return array<string,int> mot => nombre d'occurrences
     */
    public static function frequences(string $texte): array
    {
        $texte = mb_strtolower($texte, 'UTF-8');
        $mots = preg_split('/[^\p{L}\p{N}]+/u', $texte, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $frequences = [];
        foreach ($mots as $mot) {
            if (mb_strlen($mot) < 3 || in_array($mot, self::MOTS_VIDES, true)) {
                continue;
            }
            $frequences[$mot] = ($frequences[$mot] ?? 0) + 1;
        }

        return $frequences;
    }
}
