<?php
/**
 * SERAPH BUILD CONSTRUCTION — derived views over config/sbc-packages.php.
 *
 * The package config is a flat, display-ready data file (values contain HTML
 * entities on purpose). These helpers never mutate it; they reshape it into
 * the small number of shapes the page actually needs, so nothing in the
 * markup has to re-derive counts, reference codes or upgrade lists.
 *
 * Two flavours of value come out of every row:
 *   - "html"  — the raw config string, echoed as-is (already entity-encoded)
 *   - "text"  — tags stripped and entities decoded, lower-cased: safe to put
 *               in a data-* attribute and search from the client
 */

declare(strict_types=1);

/* ------------------------------------------------------------------ *
 * Row reference codes
 * ------------------------------------------------------------------ */

/** Human reference for a row, e.g. "IV-07" for the 7th line of Carpentry. */
function sk_ref(string $sectionNum, int $rowIndex): string
{
    return strtoupper($sectionNum) . '-' . str_pad((string) ($rowIndex + 1), 2, '0', STR_PAD_LEFT);
}

/* ------------------------------------------------------------------ *
 * Text normalisation
 * ------------------------------------------------------------------ */

function sk_text(string $value): string
{
    return trim(mb_strtolower(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
}

/* ------------------------------------------------------------------ *
 * Flattened row list
 * ------------------------------------------------------------------ */

/**
 * Every specification row in the document, flattened and decorated.
 *
 * @return list<array{
 *   secId:string, secNum:string, secTitle:string, secIcon:string,
 *   ri:int, ref:string, label:string, p:string, e:string,
 *   same:bool, text:string
 * }>
 */
function sk_rows(array $pk): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $cache = [];
    foreach ($pk['sections'] as $section) {
        foreach ($section['rows'] as $ri => $row) {
            $p = $row[1];
            $e = $row[2];
            $cache[] = [
                'secId'    => $section['id'],
                'secNum'   => $section['num'],
                'secTitle' => $section['title'],
                'secIcon'  => $section['icon'],
                'ri'       => $ri,
                'ref'      => sk_ref($section['num'], $ri),
                'label'    => $row[0],
                'p'        => $p,
                'e'        => $e,
                'same'     => trim($p) === trim($e),
                'text'     => sk_text($row[0] . ' ' . $p . ' ' . $e),
            ];
        }
    }

    return $cache;
}

/* ------------------------------------------------------------------ *
 * Counts
 * ------------------------------------------------------------------ */

/** @return array{rows:int, sections:int, diff:int, same:int, pct:int} */
function sk_stats(array $pk): array
{
    $rows = sk_rows($pk);
    $diff = 0;
    foreach ($rows as $r) {
        if (!$r['same']) {
            $diff++;
        }
    }

    $total = count($rows);

    return [
        'rows'     => $total,
        'sections' => count($pk['sections']),
        'diff'     => $diff,
        'same'     => $total - $diff,
        'pct'      => $total ? (int) round($diff / $total * 100) : 0,
    ];
}

