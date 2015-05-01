<?php
class Game_Moves
{
	/**
	 * @var Game
	 */
	protected $_game;


	/**
	 * @param Game $game
	 */
	public function __construct($game)
	{
		$this->_game = $game;
	}


	/**
	 * Пеместить карту из основной колоды в игровую
	 *
	 * @param int $holderId
	 */
	public function fromDeckToHolder($holderId)
	{
		$deck = $this->_game->getDecks()->getMain();
		$holder = $this->_game->getDecks()->getHolder($holderId);

		if ($deck->getPop()->nominal !== $holder->getRequired()) {
			throw new Game_Moves_Exception('Нельзя переместить эту карту');
		}
		
		$holder->push($deck->pop());

		return $this;
	}

	
	/**
	 * Переместить карту из основной колоды в буферную
	 *
	 * @param int $bufferId
	 */
	public function fromDeckToBuffer($bufferId)
	{
		$deck = $this->_game->getDecks()->getMain();
		$buffer = $this->_game->getDecks()->getBuffer($bufferId);
		if ($card = $deck->getPop()) {
			$buffer->push($deck->pop());
		}

		return $this;
	}


	/**
	 * Переместить из буфера в игровую колоду
	 */
	public function fromBufferToHolder($bufferId, $holderId)
	{
		$buffer = $this->_game->getDecks()->getBuffer($bufferId);
		$holder = $this->_game->getDecks()->getHolder($holderId);

		if ($buffer->getPop()->nominal !== $holder->getRequired()) {
			throw new Game_Moves_Exception('Нельзя переместить эту карту');
		}

		$holder->push($buffer->pop());

		return $this;
	}


	/**
	 * Переместить из буфера в игровую колоду
	 */
	public function fromHolderToHolder($fromId, $toId)
	{
		$from = $this->_game->getDecks()->getHolder($fromId);
		$to = $this->_game->getDecks()->getHolder($toId);

		if ($from->getPop()->nominal !== $to->getRequired()) {
			throw new Game_Moves_Exception('Нельзя переместить эту карту');
		}

		$to->push($from->pop());

		return $this;
	}

}
