<?php
class Game
{
	/**
	 * @var Game_Moves
	 */
	protected $_moves;


	/**
	 * @var Game_Decks
	 */
	protected $_decks;

	/**
	 * Окончена ли игра
	 *
	 * @var bool
	 */
	protected $_solved = false;


	/**
	 * Инициализация игры
	 *
	 * @return $this
	 */
	public function init()
	{
		$this->getDecks()->init();

		return $this;
	}


	/**
	 * Перезапуск игры
	 *
	 * @return $this
	 */
	public function restart()
	{
		$this->getDecks()->init();

		return $this;
	}


	/**
	 * Движения картами
	 *
	 * @return Game_Moves
	 */
	public function getMoves()
	{
		if (!$this->_moves) {
			$this->_moves = new Game_Moves($this);
		}

		return $this->_moves;
	}


	/**
	 * Колоды карт
	 *
	 * @return Game_Decks
	 */
	public function getDecks()
	{
		if (!$this->_decks) {
			$this->_decks = new Game_Decks($this);
		}

		return $this->_decks;
	}


	/**
	 * Получение статуса завершенности игры
	 */
	public function getSolved()
	{
		return $this->_solved;
	}


	/**
	 * Установка статуса о завершенности игры
	 *
	 * @param bool $solved
	 */
	public function setSolved($solved)
	{
		$this->_solved = $solved;
	}
}
