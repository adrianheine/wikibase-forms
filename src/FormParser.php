<?php
/**
 * Wikibase forms extension
 * Copyright (C) 2018 Adrian Heine <mail@adrianheine.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace WikibaseForms;

use Exception;
use Wikibase\DataModel\Entity\EntityIdParser;
use WikibaseForms\Model\Form;
use WikibaseForms\Model\Section;
use WikibaseForms\Model\Snak;
use WikibaseForms\Model\Statement;
use WikibaseForms\Model\StatementList;
use WikibaseForms\Model\StatementSection;

class FormParser {

  private $entityIdParser;
  private $regexp;

  public function __construct( EntityIdParser $entityIdParser ) {
    $this->entityIdParser = $entityIdParser;

    $ws = " *";
    $sectionTitle = "[^- ].*";
    $propertyId = "P\d+";
    $quantifier = "\+?";
    $listOf = function ($of) use ($ws) { return "$of(?:$ws,$ws$of)*"; };
    $listOfQ = $listOf("Q\d+");
    $validValues = "(?:\($ws(?:($listOfQ))$ws\))?";
    // FIXME: Maybe add these?
    // $listOfStrings = $listOf('"[^"]*"');
    // $validValues = "(?:\($ws(?:($listOfQ)|($listOfStrings))$ws\))?";
    $this->regexp = "/^$ws(?:" .
      "($sectionTitle)$ws\($ws($propertyId)$ws$validValues$ws\)$ws($quantifier)" . "|" .
      "($sectionTitle)" . "|" .
      "-$ws($propertyId)$ws$validValues$ws($quantifier)(?:$ws#.*)?" .
    ")$ws$/";
  }

  public function parse( string $form ) : Form {
    $sections = [];

    try {
      $in = null;
      $line = strtok( $form, "\r\n" );
      while ( $line !== false ) {
        if ( !preg_match( $this->regexp, $line, $match ) ) throw new Exception("Cannot parse $line");
        if ( $match[1] ) {
          if ( $in ) $sections[] = $this->finishSection( $in );
          $in = [ "statementSection", $match[1], [], $this->snakFrom( $match, 2 ), $match[4] ];
        } else if ( $match[5] ) { // Single sectionTitle
          if ( $in ) $sections[] = $this->finishSection( $in );
          $in = [ "statementList", $match[5], [] ];
        } else if ( $match[6] ) {
          if ( !$in ) throw new Exception();
          $in[2][] = new Statement( $this->snakFrom( $match, 6 ), $match[8]);
        }
        $line = strtok( "\r\n" );
      }
      if ( $in ) $sections[] = $this->finishSection( $in );
    } finally {
      strtok( "", "" ); // Release memory
    }

    return new Form(...$sections);
  }

  private function finishSection( array $in ) : Section {
    switch ($in[0]) {
    case "statementList":
      return new StatementList($in[1], ...$in[2]);
      break;
    case "statementSection":
      return new StatementSection($in[1], $in[3], $in[4], ...$in[2]);
    }
  }

  private function snakFrom( $match, int $idx ) : Snak {
    $list = [];
    if ( $match[$idx + 1] ) {
      preg_match_all("/Q\d+/", $match[$idx + 1], $list);
      $list = array_map( [$this->entityIdParser, 'parse'], $list[0] );
/*
    } else if ( $match[$idx + 2] ) {
      preg_match_all('/"[^"]*"/', $match[$idx + 2], $list);
      $list = $list[0];
*/
    }
    return new Snak( $this->entityIdParser->parse( $match[$idx] ), ...$list );
  }
}
