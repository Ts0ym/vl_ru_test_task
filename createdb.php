<?php
$db = new SQLite3('requests.db');
$db->exec("CREATE TABLE IF NOT EXISTS requests (
   id INTEGER PRIMARY KEY,
   subject TEXT NOT NULL,
   text TEXT NOT NULL,
   priority INTEGER NOT NULL,
   email TEXT NOT NULL,
   pin TEXT NOT NULL
)");
?>
