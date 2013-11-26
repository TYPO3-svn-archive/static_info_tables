<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Repository for \SJBR\StaticInfoTables\Domain\Model\Country
 */
class CountryRepository extends AbstractEntityRepository {

	/**
	 * ISO keys for this static table
	 * @var array
	 */
	protected $isoKeys = array('cn_iso_2');

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository
	 */
	protected $territoryRepository;

 	/**
	 * Dependency injection of the Territory Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository
 	 * @return void
	 */
	public function injectTerritoryRepository(\SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository) {
		$this->territoryRepository = $territoryRepository;
	}

	/**
	 * Finds countries by territory
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\Territory $territory
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
	 */
	public function findByTerritory(\SJBR\StaticInfoTables\Domain\Model\Territory $territory) {
		$unCodeNumbers = array($territory->getUnCodeNumber());
		// Get UN code numbers of subterritories
		$subterritories = $this->territoryRepository->findByTerritory($territory);
		foreach ($subterritories as $subterritory) {
			$unCodeNumbers[] = $subterritory->getUnCodeNumber();
		}
		$query = $this->createQuery();
		$query->matching(
			$query->in('parentTerritoryUnCodeNumber', $unCodeNumbers)
		);
		return $query->execute();
	}

	/**
	 * Finds countries by territory ordered by localized name
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\Territory $territory
	 *
	 * @return array Countries of the territory sorted by localized name
	 */
	public function findByTerritoryOrderedByLocalizedName(\SJBR\StaticInfoTables\Domain\Model\Territory $territory) {
		$entities = $this->findByTerritory($territory);
		return $this->localizedSort($entities);
	}
}
?>