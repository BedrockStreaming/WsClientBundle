<?php
require_once __DIR__ . '/../../vendor/atoum/atoum/classes/autoloader.php';

/*
 * CLI report.
 */
$stdOutWriter = new \mageekguy\atoum\writers\std\out();
$cli = new \mageekguy\atoum\reports\realtime\cli();
$cli->addWriter($stdOutWriter);

$basedir = __DIR__.'/../../../';

/*
 * Xunit report
 */
$xunit = new \mageekguy\atoum\reports\asynchronous\xunit();
/*
 * Xunit writer
 */
$writer = new \mageekguy\atoum\writers\file($basedir.'/build/logs/junit.xml');
$xunit->addWriter($writer);

/*
 * Clover xml coverage - todo
 */

/*
 * Html coverage
 */
$html = new \mageekguy\atoum\report\fields\runner\coverage\html('Bundle WSClient',  $basedir.'/build/coverage');
$cli->addField($html, array(\mageekguy\atoum\runner::runStop));

//$runner->addReport($clover);
$runner->addReport($xunit);
$runner->addReport($cli);