<?php
/**
 * Игра расчет - Простой ИИ
 * @author Evgeniy Vasilev <info@johnynsk.ru>
 */

require_once 'bootstrap.php';

$countAll = 0;
$countSuccess = 0;
$shufflesCount = 0;
$movesCount = 0;

for ($i = 0; $i < 999; $i++)
{
	$game = new Game();
	$game->init();

	$bot = new Game_Bot($game);
	$bot->setAllowShuffle(true);
	$bot->setQuiet(true);
	$bot->loop();

	if ($game->getSolved()) {
		$countSuccess++;
		$movesCount += $bot->getCyclesCount();
		$shufflesCount += $game->getDecks()->getShufflesCount();
	}
	$countAll++;
}

if ($countAll > 0 && $countSuccess > 0) {
	echo 'Процент выигрышей: '. round($countSuccess * 100 / $countAll, 2) . '%' . PHP_EOL;
	echo 'Количество действий (среднее): ' . round($movesCount / $countSuccess) . PHP_EOL;
	echo 'Количество перемешиваний (среднее): ' . round ($shufflesCount / $countSuccess) . PHP_EOL;
}
