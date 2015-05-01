<?php
/**
 * Игра расчет - Простой ИИ
 * @author Evgeniy Vasilev <info@johnynsk.ru>
 */

require_once 'bootstrap.php';

$game = new Game();
$game->init();

$bot = new Game_Bot($game);
$bot->loop();

