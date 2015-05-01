<?php
class Game_Saves
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
	 * Dump game to array
	 *
	 * @return $result
	 */
	public function dumpGame()
	{
		$result = ['buffers' => [], 'holders' => [], 'deck' => []];

		foreach ($this->_buffers as $bufferNumber => $buffer) {
			while ($card = $buffer->pop()) {
				$result['buffers'][$bufferNumber][] = $card->dump();
			}
		}

		foreach ($this->_holders as $holderNumber => $holder) {
			while ($card = $holder->pop()) {
				$result['holders'][$holderNumber][] = $card->dump();
			}
		}

		while ($card = $deck->pop()) {
			$result['deck'][] = $card->dump();
		}


		return $result;
	}


	/**
	 * Load game from array
	 *
	 * @param array $gameData
	 */
	public function loadGame($gameData)
	{
		$this->_game->init();

		foreach($gameData['buffers'] as $bufferId => $cards) {
			if ($buffer = $this->_game->getBuffer($bufferId)) {
				$buffer->flush();
				foreach ($cards as $cardData) {
					$card = new Game_Card($cardData[0], $cardData[1]);
					$buffer->push($card);
				}			
			}
		}

		foreach($gameData['holders'] as $holderId => $cards) {
			if ($holder = $this->_game->getHolder($holderID)) {
				$holder->flush();
				foreach ($cards as $cardData) {
					$card = new Game_Card($cardData[0], $cardData[1]);
					$holder->push($card);
				}
			}
		}

		if ($deck = $this->_game->getDeck()) {
			$deck->flush();
			foreach($gameData['deck'] as $card) {
				$card = new Game_Card($cardData[0], $cardData[1]);
				$deck->push($card);
			}
		}
	}
}
