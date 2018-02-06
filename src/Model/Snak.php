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

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;

class Snak {
  private $propertyId;
  private $validValues;

  public function __construct( PropertyId $propertyId, ItemId ...$validValues ) {
    $this->propertyId = $propertyId;
    $this->validValues = $validValues;
  }

  public function getPropertyId(): PropertyId {
    return $this->propertyId;
  }

  public function getValidValues(): array {
    return $this->validValues;
  }
}
