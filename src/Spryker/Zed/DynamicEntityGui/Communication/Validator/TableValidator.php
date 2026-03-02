<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DynamicEntityGui\Communication\Validator;

use Generated\Shared\Transfer\DynamicEntityConfigurationCriteriaTransfer;
use Propel\Runtime\Map\DatabaseMap;
use Spryker\Zed\DynamicEntityGui\Dependency\Facade\DynamicEntityGuiToDynamicEntityFacadeInterface;

class TableValidator implements TableValidatorInterface
{
    /**
     * @var \Spryker\Zed\DynamicEntityGui\Dependency\Facade\DynamicEntityGuiToDynamicEntityFacadeInterface
     */
    protected DynamicEntityGuiToDynamicEntityFacadeInterface $dynamicEntityFacade;

    /**
     * @var \Propel\Runtime\Map\DatabaseMap
     */
    protected DatabaseMap $databaseMap;

    /**
     * @var array<string>
     */
    protected array $disallowedTables = [];

    public function __construct(
        DynamicEntityGuiToDynamicEntityFacadeInterface $dynamicEntityFacade,
        DatabaseMap $databaseMap
    ) {
        $this->dynamicEntityFacade = $dynamicEntityFacade;
        $this->databaseMap = $databaseMap;
    }

    public function validateIsTableDisallowed(string $table): bool
    {
        return in_array($table, $this->getDisallowedTables()) === true;
    }

    /**
     * @return array<string>
     */
    protected function getDisallowedTables(): array
    {
        if (!$this->disallowedTables) {
            $this->disallowedTables = $this->dynamicEntityFacade->getDisallowedTables();
        }

        return $this->disallowedTables;
    }

    public function validateIsTableConfigured(string $table): bool
    {
        return in_array($table, $this->getConfiguredDynamicEntities()) === true;
    }

    public function validateIsTableExist(string $table): bool
    {
        return $this->databaseMap->hasTable($table);
    }

    /**
     * @return array<string>
     */
    protected function getConfiguredDynamicEntities(): array
    {
        $tableNames = [];
        $dynamicEntityConfigurationCollection = $this->dynamicEntityFacade->getDynamicEntityConfigurationCollection(new DynamicEntityConfigurationCriteriaTransfer());

        foreach ($dynamicEntityConfigurationCollection->getDynamicEntityConfigurations() as $dynamicEntityConfiguration) {
            $tableNames[] = $dynamicEntityConfiguration->getTableNameOrFail();
        }

        return $tableNames;
    }
}
