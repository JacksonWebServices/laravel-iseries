<?php

namespace JWS\Iseries;

use PDO;
use Illuminate\Database\Connection;
use JWS\Iseries\Schema\Builder;
use JWS\Iseries\Query\Processors\DB2Processor;
use JWS\Iseries\Query\Grammars\DB2Grammar as QueryGrammar;
use JWS\Iseries\Schema\Grammars\DB2Grammar as SchemaGrammar;

class IseriesConnection extends Connection
{

    /**
     * The name of the default schema.
     *
     * @var string
     */
    protected $defaultSchema;

    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
        $this->currentSchema = $this->defaultSchema = strtoupper($config['schema']);
    }

    /**
     * Get the name of the default schema.
     *
     * @return string
     */
    public function getDefaultSchema()
    {
        return $this->defaultSchema;
    }

    /**
     * Reset to default the current schema.
     *
     * @return string
     */
    public function resetCurrentSchema()
    {
        $this->setCurrentSchema($this->getDefaultSchema());
    }

    /**
     * Set the name of the current schema.
     *
     * @return string
     */
    public function setCurrentSchema($schema)
    {
        //$this->currentSchema = $schema;
        $this->statement('SET SCHEMA ?', [strtoupper($schema)]);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); }

        return new Builder($this);
    }

    /**
     * @return Query\Grammars\Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Default grammar for specified Schema
     * @return Schema\Grammars\Grammar
     */
    protected function getDefaultSchemaGrammar()
    {

        return $this->withTablePrefix(new SchemaGrammar);
    }

    /**
    * Get the default post processor instance.
    *
    * @return \Illuminate\Database\Query\Processors\PostgresProcessor
    */
    protected function getDefaultPostProcessor()
    {
        return new DB2Processor;
    }

}
