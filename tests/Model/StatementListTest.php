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

namespace WikibaseForms\Tests\Model;

use PHPUnit\Framework\TestCase;
use WikibaseForms\Model\Statement;
use WikibaseForms\Model\StatementList;

class StatementListTest extends TestCase {
    /**
     * @dataProvider statementListProvider
     */
    public function testStatementList($label, $statements) {
        $tpl = new StatementList($label, ...$statements);
        $this->assertInstanceOf(StatementList::class, $tpl);
        $this->assertEquals($statements, $tpl->getStatements());
        $this->assertEquals($label, $tpl->getLabel());
    }

    public function statementListProvider() {
      return [
        [ "label", [] ],
        [ "label", [ $this->createMock(Statement::class) ] ],
        [ "label", [ $this->createMock(Statement::class), $this->createMock(Statement::class) ] ],
      ];
    }
}
