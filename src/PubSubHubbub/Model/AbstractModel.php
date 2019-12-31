<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\PubSubHubbub\Model;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

class AbstractModel
{
    /**
     * Laminas\Db\TableGateway\TableGatewayInterface instance to host database methods
     *
     * @var TableGatewayInterface
     */
    protected $db = null;

    /**
     * Constructor
     *
     * @param null|TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway = null)
    {
        if ($tableGateway === null) {
            $parts = explode('\\', get_class($this));
            $table = strtolower(array_pop($parts));
            $this->db = new TableGateway($table, null);
        } else {
            $this->db = $tableGateway;
        }
    }
}
