<?php
/**
 * Игра расчет
 * @author Evgeniy Vasilev <info@johnynsk.ru>
 */

require_once 'bootstrap.php';

$game = new Game();
$game->init();

$cli = new Game_Cli($game);
$cli->loop();

