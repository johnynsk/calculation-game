<?php
class Game_Decks
{
	/**
	 * @var Game_Deck_Holder[]
	 */
	protected $_holders;


	/**
	 * @var Game_Deck_Buffer[]
	 */
	protected $_buffers;


	/**
	 * @var Game_Deck_Main
	 */
	protected $_main;


	/**
	 * @var Game
	 */
	protected $_game;


	/**
	 * Количество перемешиваний
	 * 
	 * @var int
	 */
	protected $_shufflesCount = 0;


	/**
	 * Лимит перемешиваний
	 */
	const MAX_SHUFFLES = 2;


	/**
	 * Максимальное количество карт в колоде
	 */
	const MAX_DECK_CARDS = 48;


	/**
	 * Максимальное количество буферных колод
	 */
	const MAX_BUFFERS = 4;


	/**
	 * Вернуть главную колоду
	 * @return Game_Deck_Main
	 */
	public function getMain()
	{
		return $this->_main;
	}


	/**
	 * Вернуть игровую колоду
	 *
	 * @return Game_Deck_Holder
	 */
	public function getHolder($holderId)
	{
		if (!isset($this->_holders[$holderId])) {
			throw new Game_Exception('Неизвестная колода Holders ' . $holderId);
		}

		return $this->_holders[$holderId];
	}


	/**
	 * Получить игровую колоду
	 * 
	 * @return Game_Deck_Holder[]
	 */
	public function getHolders()
	{
		return $this->_holders;
	}


	/**
	 * Решены ли все колоды
	 *
	 * @return bool
	 */
	public function isHoldersSolved()
	{
		foreach($this->_holders as $holder)
		{
			if ($holder->getRequired() !== false)
			{
				return false;
			}
		}

		$this->_game->setSolved(true);

		return true;
	}


	/**
	 * Вернуть буферную колоду
	 *
	 * @return Game_Deck_Buffer
	 */
	public function getBuffer($bufferId)
	{
		if (!isset($this->_buffers[$bufferId])) {
			throw new Game_Exception('Неизвестная колода Buffers ' . $bufferId);
		}

		return $this->_buffers[$bufferId];
	}

	
	/**
	 * Получить буферные колоды
	 *
	 * @return Game_Deck_Buffer[]
	 */
	public function getBuffers()
	{
		return $this->_buffers;
	}


	/**
	 * Инициализация колод
	 *
	 * @return $this
	 */
	public function init($fillData = true)
	{
		$this->_main = new Game_Deck_Main();
		$this->_main->setName('Колода');

		$holders = [];
		$buffers = [];

		for ($i = 0; $i < 4; $i++) {
			$holders[$i] = new Game_Deck_Holder($i+1, $i, $i);
			$holders[$i]->setName('Кратные ' . ($i+1));
			$buffers[$i] = new Game_Deck_Buffer();
			$buffers[$i]->setName('Буфер ' . ($i+1));
		}

		$this->_holders = $holders;
		$this->_buffers = $buffers;

		if ($fillData) {
			$this->_main->fill();
		}

		return $this;
	}


	/**
	 * Забрать все карты с буферных колод в основную и перемешать её
	 *
	 * @return bool
	 */
	public function shuffleBuffers()
	{
		if ($this->_shufflesCount == self::MAX_SHUFFLES) {
			return false;
		}

		$this->_shufflesCount++;

		$stacked = [];
		foreach ($this->_buffers as $buffer) {
			while ($card = $buffer->pop()) {
				$stacked[] = $card;
			}
		}

		while($card = $this->_main->pop()) {
			$stacked[] = $card;
		}

		shuffle($stacked);

		$this->_main = new Game_Deck_Main();
		$this->_main->setName("Колода");

		foreach ($stacked as $card) {
			$this->_main->push($card);
		}

		return true;
	}


	/**
	 * Получить количество перемешиваний
	 *
	 * @return int
	 */
	public function getShufflesCount()
	{
		return $this->_shufflesCount;
	}


	/**
	 * @param Game $game
	 */
	public function __construct($game)
	{
		$this->_game = $game;
	}
}
