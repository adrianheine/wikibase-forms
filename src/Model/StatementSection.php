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

namespace WikibaseForms\Model;

class StatementSection implements Section {
  private $label;
  private $main;
  private $qualifiers;
  private $quantifier;

  public function __construct( string $label, Snak $main, string $quantifier, Statement ...$qualifiers ) {
    $this->label = $label;
    $this->main = $main;
    $this->qualifiers = $qualifiers;
    $this->quantifier = $quantifier;
  }

  public function getLabel() : string {
    return $this->label;
  }

  public function getMain() : Snak {
    return $this->main;
  }

  public function getQualifiers() : array {
    return $this->qualifiers;
  }

  public function getQuantifier() : string {
    return $this->quantifier;
  }

  public function toPlainObj() {
    return [
      'label' => $this->label,
      'main' => $this->main->toPlainObj(),
      'qualifiers' => array_map( function( Statement $s ) { return $s->toPlainObj(); }, $this->qualifiers ),
      'quantifier' => $this->quantifier,
    ];
  }
}
