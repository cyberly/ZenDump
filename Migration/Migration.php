<?php

namespace ZenDump\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration {
    /** @var \Illuminate\Database\Capsule\Manager $capsule */
    public $capsule;
    /** @var \Illuminate\Database\Schema\Builder $capsule */
    public $schema;

    public function init()
    {
        $dbFile = ".db.env";
        $dbInfo = json_decode(file_get_contents($dbFile), true);

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
          'driver'    => 'mysql',
          'host'      => $dbInfo["host"],
          'port'      => $dbInfo["port"],
          'database'  => $dbInfo["db"],
          'username'  => $dbInfo["user"],
          'password'  => $dbInfo["pass"],
          'charset'   => 'utf8',
          'collation' => 'utf8_unicode_ci',
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}
