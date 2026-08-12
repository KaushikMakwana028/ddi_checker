<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Lightweight XLSX Reader for CodeIgniter 3
 * Parses Microsoft Excel (.xlsx) files without external dependencies.
 */
class SimpleXLSXReader {

    /**
     * Parse an XLSX file into a 2D array of rows
     *
     * @param string $filename Path to the .xlsx file
     * @return array|false Returns array of rows or false on error
     */
    public static function parse($filename) {
        if (!class_exists('ZipArchive')) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($filename) !== true) {
            return false;
        }

        // 1. Read Shared Strings (if available)
        $sharedStrings = [];
        $sharedXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXmlContent !== false) {
            $xml = @simplexml_load_string($sharedXmlContent);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        // Formatted rich text
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read first worksheet (sheet1.xml)
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            $sheetXmlContent = $zip->getFromName('xl/worksheets/Sheet1.xml');
        }

        if ($sheetXmlContent === false) {
            $zip->close();
            return false;
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        $zip->close();

        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];

        foreach ($sheetXml->sheetData->row as $row) {
            $rowArray = [];
            $maxColIdx = 0;

            foreach ($row->c as $cell) {
                $cellRef = (string)$cell['r'];
                $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                $colIndex = self::columnToIndex($colLetters);

                $cellType = (string)$cell['t'];
                $value = '';

                if ($cellType === 's') {
                    // Shared String
                    $valIndex = (int)$cell->v;
                    $value = $sharedStrings[$valIndex] ?? '';
                } elseif ($cellType === 'inlineStr') {
                    $value = (string)($cell->is->t ?? '');
                } else {
                    $value = (string)($cell->v ?? '');
                }

                $rowArray[$colIndex] = trim($value);
                if ($colIndex > $maxColIdx) {
                    $maxColIdx = $colIndex;
                }
            }

            // Fill missing gaps in the row
            $normalizedRow = [];
            for ($i = 0; $i <= $maxColIdx; $i++) {
                $normalizedRow[$i] = $rowArray[$i] ?? '';
            }

            if (!empty(array_filter($normalizedRow, 'strlen'))) {
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Convert Excel column letters (e.g. A, B, Z, AA) to 0-based integer index
     */
    private static function columnToIndex($col) {
        $col = strtoupper($col);
        $len = strlen($col);
        $index = 0;

        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - 64);
        }

        return $index - 1;
    }
}
