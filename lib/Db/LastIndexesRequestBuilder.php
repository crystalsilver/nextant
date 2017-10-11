<?php
/**
 * FullNextSearch - Full Text Search your Nextcloud.
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
 * @license GNU AGPL version 3 or any later version
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

namespace OCA\FullNextSearch\Db;


use OCA\FullNextSearch\Model\DocumentIndex;
use OCA\FullNextSearch\Model\SearchDocument;
use OCA\FullNextSearch\Service\ConfigService;
use OCA\FullNextSearch\Service\MiscService;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IL10N;

class LastIndexesRequestBuilder extends CoreRequestBuilder {


	/**
	 * WebsitesRequestBuilder constructor.
	 *
	 * {@inheritdoc}
	 */
	public function __construct(
		IL10N $l10n, IDBConnection $connection, ConfigService $configService, MiscService $miscService
	) {
		parent::__construct($l10n, $connection, $configService, $miscService);
	}


	/**
	 * Base of the Sql Insert request
	 *
	 * @return IQueryBuilder
	 */
	protected function getLastIndexesInsertSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->insert(self::TABLE_LAST_INDEXES)
		   ->setValue('indexed', $qb->createFunction('NOW()'));

		return $qb;
	}


	/**
	 * Base of the Sql Update request
	 *
	 * @return IQueryBuilder
	 */
	protected function getLastIndexesUpdateSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->update(self::TABLE_LAST_INDEXES)
		   ->set('indexed', $qb->createFunction('NOW()'));

		return $qb;
	}


	/**
	 * Base of the Sql Select request for Shares
	 *
	 * @return IQueryBuilder
	 */
	protected function getLastIndexesSelectSql() {
		$qb = $this->dbConnection->getQueryBuilder();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$qb->select('li.owner_id', 'li.provider_id', 'li.document_id', 'li.status', 'li.indexed')
		   ->from(self::TABLE_LAST_INDEXES, 'li');

		$this->defaultSelectAlias = 'li';

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return IQueryBuilder
	 */
	protected function getLastIndexesDeleteSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->delete(self::TABLE_LAST_INDEXES);

		return $qb;
	}


	/**
	 * @param array $data
	 *
	 * @return DocumentIndex
	 */
	protected function parseLastIndexesSelectSql($data) {
		$index = new DocumentIndex($data['provider_id'], $data['document_id']);
		$index->setStatus($data['status'])
			  ->setOwnerId($data['owner_id'])
			  ->setLastIndex($data['indexed']);

		return $index;
	}

}