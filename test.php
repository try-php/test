<?php
namespace Tapping;

use function Task\{forkTask, getProcessStatus};
use function Crayon\{text};
use function Load\{dots,removeLastCharsByCount};

function test(string $desc, callable $test) {
	// build flag
	$options = getopt('b', ['build', 'ci']);
	$buildState = isset($options['b']) || isset($options['build']) || isset($options['ci']);

	$loadingDesc = text($desc)->yellow();
	$successIndicator = text('ok')->green();
	$successMessage = "$desc $successIndicator";
	$failIndicator = text('not ok')->red();
	$failMessage = "$desc $failIndicator";

	$pid = forkTask($test, []);
	if ($pid > 0) {
		try {
			dots(function () use ($pid, $loadingDesc) {
				$processStatus = getProcessStatus($pid, $status);
				if ($processStatus > 0) {
					if ($status === 0) {
						return true;
					} else {
						return false;
					}
				}

				return "$loadingDesc";
			}, "$successMessage");
		} catch (\Exception $ex) {
			fwrite(fopen('php://output', 'w'), "$failMessage\n");
			if ($buildState) {
				exit(1);
			}
		}
	} else {
		exit;
	}
}