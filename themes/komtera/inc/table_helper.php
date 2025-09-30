<?php
/**
 * Table Helper - Domain-based table name resolver
 *
 * This helper provides dynamic table name resolution based on the domain.
 * Test environment (erptest.komtera.com) uses tables with 'atest_' prefix,
 * while production (erp.komtera.com) uses original table names.
 */

/**
 * Get table name based on current domain
 *
 * @param string $tableName Original table name
 * @return string Table name with prefix if in test environment
 */
function getTableName($tableName) {
    $domain = $_SERVER['HTTP_HOST'] ?? '';

    // Test environment - add atest_ prefix
    if ($domain === 'erptest.komtera.com') {
        return 'atest_' . $tableName;
    }

    // Production environment - use original table name
    return $tableName;
}

/**
 * Get table name with LKS.dbo schema
 *
 * @param string $tableName Original table name
 * @return string Full table name with schema
 */
function getTableNameWithSchema($tableName) {
    return 'LKS.dbo.' . getTableName($tableName);
}
