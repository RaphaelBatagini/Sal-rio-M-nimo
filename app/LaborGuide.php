<?php

namespace App;

use App\DomHandler;

/**
 * Labor Guide
 *
 * @author Raphael Batagini
 */
class LaborGuide {
    const URL_MINIMUM_WAGE = 'http://www.guiatrabalhista.com.br/guia/salario_minimo.htm';
    conSt ROW_LENGTH = 6;

    /**
     * Get Minimum Wage
     * 
     * Get the minimum wage based on a table from external URL
     *
     * @access private
     * @author Raphael Batagini
     * @param string $url Page address
     * @return string Page content
     */
    public static function getMinimumWage()
    {
        // Get content from URL
        $urlContent = utf8_encode(DomHandler::getUrlContent(self::URL_MINIMUM_WAGE));

        // Get start and end position from the first table on url
        $tableStart = strpos($urlContent, '<table');
        $tableEnd = strpos($urlContent, '</table>');

        // Get content from the first table on url
        $tableHtml = substr($urlContent, $tableStart, ($tableEnd - $tableStart));
        
        // Convert table html to array
        $domArray = DomHandler::htmlToArrayOfValues($tableHtml);
        $rows = DomHandler::getTableData($domArray);

        // Workaround to fix a columns that has two links
        $rows[28] = "{$rows[28]}{$rows[29]}";
        unset($rows[29]);

        // Break rows in arrays of row length
        $rows = array_chunk($rows, self::ROW_LENGTH);
        $headers = array_shift($rows);
        $counter = 0;
        $data = array();
        foreach ($rows as $key => $row) {
            foreach ($row as $k => $cell) {
                $data[$key][utf8_decode($headers[$counter])] = trim($cell);
                ++$counter;
                if ($counter > (self::ROW_LENGTH - 1)) {
                    $counter = 0;
                }
            }
        }

        return $data;
    }
}
