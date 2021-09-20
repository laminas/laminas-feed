<?php

namespace Laminas\Feed\PubSubHubbub\Model;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

use function array_pop;
use function explode;
use function strtolower;

class AbstractModel
{
    /**
     * Laminas\Db\TableGateway\TableGatewayInterface instance to host database methods
     *
     * @var TableGatewayInterface
     */
    protected $db;

    public function __construct(?TableGatewayInterface $tableGateway = null)
    {
        if ($tableGateway === null) {
            $parts    = explode('\\', static::class);
            $table    = strtolower(array_pop($parts));
            $this->db = new TableGateway($table, null);
        } else {
            $this->db = $tableGateway;
        }
    }
}
