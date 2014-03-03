<?php
use mageekguy\atoum\scripts;


$score_file = '/tmp/'.posix_getpwuid(posix_getuid())['name'].'atoum.score';
scripts\runner::setScoreFile($score_file);