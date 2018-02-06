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

namespace WikibaseForms\Tests;

use PHPUnit\Framework\TestCase;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use WikibaseForms\Model\Snak;
use WikibaseForms\Model\Statement;
use WikibaseForms\Model\StatementList;
use WikibaseForms\Model\StatementSection;
use WikibaseForms\Model\Form;
use WikibaseForms\FormParser;

class FormParserTest extends TestCase {
    /**
     * @dataProvider formsProvider
     */
    public function testParse( string $form, Form $result ) {
        $parser = new FormParser(new BasicEntityIdParser());

        $this->assertEquals($result, $parser->parse($form));
    }

    public function formsProvider() {
      return [
        [ "", new Form ],
        [ "Bio", new Form( new StatementList( "Bio" ) ) ],
        [ "Bio\n- P20(Q5, Q6,Q7) # something", new Form( new StatementList(
          "Bio",
          new Statement(new Snak(new PropertyId( "P20" ), new ItemId( "Q5" ), new ItemId( "Q6" ), new ItemId( "Q7" )), "")
        ) ) ],
        [ "Wohnort(P5)+\n- P3 # von\n  - P4+ # bis\n", new Form( new StatementSection(
          "Wohnort", new Snak(new PropertyId( "P5" )), "+", new Statement( new Snak( new PropertyId( "P3" ) ), "" ), new Statement( new Snak( new PropertyId( "P4" ) ), "+" )
        ) ) ],
        [ "Wohnort(P5(Q5))+\n- P3 # von\n  - P4+ # bis\n", new Form( new StatementSection(
          "Wohnort", new Snak( new PropertyId( "P5" ), new ItemId( "Q5" ) ), "+", new Statement( new Snak( new PropertyId( "P3" ) ), "" ), new Statement( new Snak( new PropertyId( "P4" ) ), "+" )
        ) ) ],
      ];
    }
}
