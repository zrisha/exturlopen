<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  External link filtering
 *
 *  This filter will replace any external links
 *  with a target of _blank
 *
 * @package    filter
 * @subpackage urlopenext
 */

defined('MOODLE_INTERNAL') || die();

/**
 * External link modifier.
 *
 *
 * @package    filter
 * @subpackage urlopenext
 */

class filter_urlopenext extends moodle_text_filter {
    /** @var bool True if currently filtering trusted text */
    private $trusted;

    public function filter($text, array $options = array()) {
        global $CFG, $PAGE;

        $dom = new DOMDocument;
        $html_data  = mb_convert_encoding($text , 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html_data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $links = $dom->getElementsByTagName('a');

        //Determine if external or internal
        //Checks if url host matches with moodle hostt
        foreach ($links as $link) {
          $domain = parse_url($CFG->wwwroot);
          $href = parse_url($link->getAttribute('href'));

          if(!array_key_exists('host', $href))
            continue;



          if($domain['host'] !== $href['host']){
            $alert = $dom->createElement('span', ' (new window)');
            $alert->setAttribute('class', "url-alert");
            $link->setAttribute('target', '_blank');
            $link->appendChild($alert);
          }
        }
        $html = $dom->saveHTML();
        error_log($html);
        return $html;
    }

  }
