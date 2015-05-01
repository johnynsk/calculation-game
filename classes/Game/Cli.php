<?php
class Game_Cli
{
	/**
	 * @var Game
	 */
	protected $_game;


	/**
	 * @var int
	 */
	protected $_loopActive;


	/**
	 * @var int
	 */
	protected $_cycleCount = 0;


	/**
	 * @var bool
	 */
	protected $_quiet = false;


	/**
	 * @param Game $game
	 */
	public function __construct(Game $game)
	{
		$this->_game = $game;
	}


	/**
	 * Основной цикл
	 */
	public function loop()
	{
		$inputStream = fopen('php://stdin', 'r');
		$this->_loopActive = true;
		$this->_cycleCount = 0;

		while ($this->_loopActive) {
			$this->_printGameInfo();
			$message = fgets($inputStream, 1024);

			try {
				$this->_processMessage($message);
			} catch (Game_Cli_Exception $e) {
				$this->_print('[Error] ' . $e->getMessage());
				$this->_printUsage();
			} catch (Game_Moves_Exception $e) {
				$this->_print('[Error] ' . $e->getMessage());
			}
			$this->_cycleCount++;
		}

		fclose($inputStream);
	}


	/**
	 * Выводит на экран данные игры
	 */
	protected function _printGameInfo()
	{
		if ($this->_game->getDecks()->isHoldersSolved()) {
			$this->_print('Больше ходов не требуется. Игра выиграна');
			$this->_print('Ходов: ' . $this->_loopActive);
			$this->_print('Перемешиваний: ' . $this->_game->getDecks()->getShufflesCount());
			$this->_loopActive = false;
		}

		$this->_print('Шаг ' . $this->_cycleCount);
		$decks = [];

		$decks[] = $this->_game->getDecks()->getMain();
		$decks = array_merge($decks, $this->_game->getDecks()->getHolders());
		$decks = array_merge($decks, $this->_game->getDecks()->getBuffers());

		foreach ($decks as $deck) {
			$this->_printDeck($deck);
		}
	
		$this->_print('> ', false);
	}

	
	/**
	 * Количество циклов
	 *
	 * @return int
	 */
	public function getCyclesCount()
	{
		return $this->_cycleCount;
	}


	/**
	 * Распечатать колоду карт
	 *
	 * @param Game_Deck_Abstract $deck
	 * @return string[]
	 */
	protected function _printDeck($deck)
	{
		$string = [0 => '  ', 1 => '  '];
		$string[0] .= $deck->getName() . '  ';
		$string[1] .= str_pad('', mb_strlen($deck->getName()), ' ') . '  ';

		$cards = $deck->getCards();

		if ($deck->getLimitCards() == 1 && !empty($cards))
		{
			$cards = [end($cards)];
		}

		foreach ($cards as $card)
		{
			if ($card == reset($cards)) {
				$string[0] .= '[';
				$string[1] .= '[';
			} else {
				$string[0] .= '|';
				$string[1] .= '|';
			}

			$string[0] .= $card->suitIcon;
			if (mb_strlen($card->nominalIcon) == 2) {
				$string[0] .= ' ';
			}
			$string[1] .= $card->nominalIcon;
			if ($card == end($cards)) {
				$string[0] .= ' <]';
				$string[1] .= ' <]';
			}
		}

		if ($deck->getRequired() !== false ) {
			$card = new Game_Card(0, $deck->getRequired());
			$string[0] .= ' Ожидаю '. $card->nominalIcon;
		}

		$this->_print();
		$this->_print($string);
	}


	/**
	 * Обрабатывает сообщение командной строки
	 *
	 * @param string $message
	 * @throws Game_Cli_Exception
	 */
	protected function _processMessage($message)
	{
		$message = str_replace(PHP_EOL, '', $message);

		if ($message === 'quit' || $message === 'exit') {
			$this->_print('terminated');
			$this->_loopActive = false;
			return;
		}

		$arguments = explode(' ', $message);
		$firstLetter = substr($arguments[0], 0, 1);

		if ($firstLetter === 's') {
			$this->_print('Перемешиваем колоду');
			$this->_game->getDecks()->shuffleBuffers();
			return;
		}

		if ($firstLetter === 'n') {
			$this->_print('Перезапуск игры');
			$this->_game->restart();
			return;
		}

		if ($firstLetter === 't') {
			if (!$this->_game->getDecks()->getMain()->getLimitCards()) {
				$this->_game->getDecks()->getMain()->showOne();
			} else {
				$this->_game->getDecks()->getMain()->showAll();
			}
			return;
		}


		if (count($arguments) < 2) {
			throw new Game_Cli_Exception('Мало аргументов');
		}

		if ($firstLetter === 'd' && count($arguments) == 3) {
			if (!$deck = $this->_game->getDecks()->getMain()) {
				return;
			}

			$destinationLetter = substr($arguments[1], 0, 1);

			if (!is_numeric($arguments[2])) {
				throw new Game_Cli_Exception('Неверно указан номер буфера, должно быть число');
			}

			$destinationId = $arguments[2]-1;

			if ($destinationLetter == 'h') {
				$this->_game->getMoves()->fromDeckToHolder($destinationId);
				return;
			} elseif ($destinationLetter = 'b') {
				$this->_game->getMoves()->fromDeckToBuffer($destinationId);
				return;
			}

			throw new Game_Cli_Exception('Неизвестное действие');
		}

		if (!in_array($firstLetter, ['b', 'h']) || count($arguments) < 4) {
			throw new Game_Cli_Exception('Неизвестное действие');
		}

		if (!is_numeric($arguments[1]) || !is_numeric($arguments[3])) {
			throw new Game_Cli_Exception('Сломан формат');
		}
		
		$destinationLetter = substr($arguments[2], 0, 1);
		$fromId = $arguments[1] - 1;
		$toId = $arguments[3] - 1;

		if ($firstLetter == 'b' && $destinationLetter == 'h') {
			$this->_game->getMoves()->fromBufferToHolder($fromId, $toId);
			return;
		} elseif ($firstLetter == 'h' && $destinationLetter == 'h') {
			$this->_game->getMoves()->fromHolderToHolder($fromId, $toId);
			return;
		}

		throw new Game_Cli_Exception('Неизвестное действие');
	}
	

	/**
	 * Вывести на экран строку
	 *
	 * @param string|string[] $stringOrArray
	 * @param bool $needEndOfLine
	 */
	protected function _print($strings = '', $needEndOfLine = true)
	{
		if ($this->_quiet) {
			return;
		}
		if (is_array($strings)) {
			foreach ($strings as $string) {
				$this->_print($string);
			}
			return;
		}

		echo $strings;
		if ($needEndOfLine) {
			echo PHP_EOL;
		}
	}


	/**
	 * Установка параметра тишины
	 *
	 * @param bool $quiet
	 */
	public function setQuiet($quiet)
	{
		$this->_quiet = $quiet;
	}


	/**
	 * Вывести usage
	 */
	protected function _printUsage()
	{
		$this->_print('Использование: доступны перемещения карт, перезапуск и перемешивание карт');
		$this->_print('  длинная нотация | краткая');
		$this->_print('              new | n       - перезапустить игру');
		$this->_print('           toggle | t       - скрыть/показать колоду');
		$this->_print('          shuffle | s       - перемешать карты');
		$this->_print('    deck buffer 1 | d b 1   - переместить карту из основной колоды в буфер 1');
		$this->_print('    deck holder 3 | d h 3   - переместить карту из основной колоды в игровую 1');
		$this->_print('buffer 1 buffer 2 | b 1 b 2 - переместить карту из буфера 1 в буфер 2');
		$this->_print('buffer 2 holder 3 | b 2 h 3 - переместить карту из буфера в игровую колоду 3');
		$this->_print();
	}
}
