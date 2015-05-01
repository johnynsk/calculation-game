<?php
class Game_Bot extends Game_Cli
{
	/**
	 * Перемешивать колоду
	 */
	protected $_allowShuffle = false;


	/**
	 * Разрешить перемешивать колоду
	 *
	 * @param bool $value
	 */
	public function setAllowShuffle($value = true)
	{
		$this->_allowShuffle = $value;
	}
	

	/**
	 * Основной цикл
	 */
	public function loop()
	{
		$this->_loopActive = true;
		$this->_cycleCount = 0;

		while ($this->_loopActive) {
			$this->_printGameInfo();
			$this->_cycleCount++;

			if (!$this->_quiet) {
				usleep(500000);
			}

			try {
				if ($this->_tryToMoveDeckToHolder()) {
					continue;
				}

				if ($this->_tryToMoveBuffersToHolder()) {
					continue;
				}
				
				if ($this->_tryToMoveDeckToBuffer()) {
					continue;
				}

				$this->_print('Больше нет ходов');

				if ($this->_allowShuffle) {
					$this->_print('Перемешиваю колоду и буферы');
					if (!$this->_game->getDecks()->shuffleBuffers()) {
						$this->_print('Недоступно для перемешивания');
						$this->_allowShuffle = false;
					};
					continue;
				}

				break;
			} catch (Game_Cli_Exception $e) {
				$this->_print('[Error] ' . $e->getMessage());
				$this->_printUsage();
			} catch (Game_Moves_Exception $e) {
				$this->_print('[Error] ' . $e->getMessage());
			}
		}
	}


	/**
	 * Попытка перенести карты из основной колоды в игровую
	 *
	 * @return bool
	 */
	protected function _tryToMoveDeckToHolder()
	{
		$holders = $this->_game->getDecks()->getHolders();
		$deck = $this->_game->getDecks()->getMain();

		if(!$card = $deck->getPop()) {
			return;
		}

		foreach ($holders as $holder) {
			if ($card->nominal === $holder->getRequired()) {
				$holder->push($deck->pop());
				return true;
			}
		}

		return false;
	}


	/**
	 * Попытка переместить карты из буферов в игровые колоды
	 *
	 * @return bool
	 */
	protected function _tryToMoveBuffersToHolder()
	{
		$buffers = $this->_game->getDecks()->getBuffers();
		$holders = $this->_game->getDecks()->getHolders();

		foreach ($buffers as $buffer) {
			if (!$card = $buffer->getPop()) {
				//next buffer
				continue;
			}

			foreach ($holders as $holder) {
				if ($card->nominal === $holder->getRequired()) {
					$holder->push($buffer->pop());
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Попытка переместить карты из колоды в буферы
	 * 
	 * @return bool
	 */
	protected function _tryToMoveDeckToBuffer()
	{
		$deck = $this->_game->getDecks()->getMain();
		$bufferId = rand() % (Game_Decks::MAX_BUFFERS );

		if ($deck->getPop() && $deck->getPop()->nominal === 12) {
			$bufferId = Game_Decks::MAX_BUFFERS - 1;
		}

		$buffer = $this->_game->getDecks()->getBuffer($bufferId);

		if ($card = $deck->getPop()) {
			$buffer->push($deck->pop());
			return true;
		}

		return false;
	}
}
