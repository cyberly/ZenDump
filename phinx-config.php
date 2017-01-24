<?php
$dbFile = ".db.env";
$dbInfo = json_decode(file_get_contents($dbFile), true);

return [
  'paths' => [
    'migrations' => 'migrations'
  ],
  'migration_base_class' => '\ZenDump\Migration\Migration',
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'dev',
    'dev' => [
      'adapter' => 'mysql',
      'host' => $dbInfo["host"],
      'name' => $dbInfo["db"],
      'user' => $dbInfo["user"],
      'pass' => $dbInfo["pass"],
      'port' => $dbInfo["port"]
    ]
  ]
];
